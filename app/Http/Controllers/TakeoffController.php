<?php

namespace App\Http\Controllers;

use App\Models\TakeoffSheet;
use App\Models\TakeoffItem;
use App\Models\TakeoffSection;
use App\Models\Project;
use App\Models\StandardWork;
use App\Models\ErpPlanHeader;
use App\Models\ErpPlanTask;
use App\Models\ErpPlanTaskResource;
use App\Models\Store;
use App\Models\TakeoffEditRequest;
use App\Models\Product;
use App\Models\EquipmentMaster;
use App\Models\Designation;
use App\Services\TakeoffService;
use App\Services\RebarCutOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TakeoffController extends Controller
{
    public function __construct(private TakeoffService $takeoffService)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $query = TakeoffSheet::with(['project', 'creator'])->latest();
        
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if ($user && $user->hasRole('planning') && !$user->hasRole(['planning_manager', 'admin', 'global_admin'])) {
            $assignedProjectIds = $user->projects()->pluck('projects.id');
            $query->whereIn('project_id', $assignedProjectIds);
        }

        $takeoffSheets = $query->paginate(20);
        return view('takeoff.index', compact('takeoffSheets'));
    }

    public function create()
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        $query = Project::whereIn('status', ['active', 'Active', 'planning', 'Planning']);
        
        if ($user && $user->hasRole('planning') && !$user->hasRole(['planning_manager', 'admin', 'global_admin'])) {
            $assignedProjectIds = $user->projects()->pluck('projects.id');
            $query->whereIn('id', $assignedProjectIds);
        }

        $projects = $query->get();
        return view('takeoff.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'sheet_type' => 'required|string',
            'boq_id' => 'nullable|integer',
            'category' => 'nullable|string|max:255',
            'discipline' => 'nullable|string|max:255',
            'ref_drawing' => 'nullable|string|max:255',
            'measurement_std' => 'nullable|string|max:255',
            'execution_type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        $sheet = $this->takeoffService->createSheet($validated);

        return redirect()->route('takeoff.show', $sheet)->with('success', 'Takeoff Sheet created successfully.');
    }

    public function show(TakeoffSheet $takeoff)
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if ($user && $user->hasRole('planning') && !$user->hasRole(['planning_manager', 'admin', 'global_admin'])) {
            $assignedProjectIds = $user->projects()->pluck('projects.id')->toArray();
            if (!in_array($takeoff->project_id, $assignedProjectIds)) {
                abort(403, 'You are not assigned to this project.');
            }
        }

        $takeoff->load(['sections.items.rebarDetails', 'items.rebarDetails', 'project.schedules.tasks', 'creator']);

        // Safely resolve edit-request data (table may not exist yet on first deploy)
        $isCreator     = auth()->id() == $takeoff->created_by;
        $editRequest   = null;
        $canEdit       = $isCreator;
        $pendingRequests  = collect();
        $approvedRequests = collect();

        if (Schema::hasTable('takeoff_edit_requests')) {
            $editRequest = TakeoffEditRequest::where('takeoff_sheet_id', $takeoff->id)
                ->where('user_id', auth()->id())
                ->first();
            $canEdit = $isCreator || ($editRequest && $editRequest->status === 'approved');

            if ($isCreator) {
                $pendingRequests  = $takeoff->editRequests()->with('user')->where('status', 'pending')->get();
                $approvedRequests = $takeoff->editRequests()->with('user')->where('status', 'approved')->get();
            }
        }

        return view('takeoff.show', compact(
            'takeoff', 'isCreator', 'editRequest', 'canEdit', 'pendingRequests', 'approvedRequests'
        ));
    }

    public function destroy(TakeoffSheet $takeoff)
    {
        if (auth()->id() !== $takeoff->created_by) {
            abort(403, 'Only the creator can delete this takeoff sheet.');
        }

        $takeoff->delete();

        return redirect()->route('takeoff.index')->with('success', 'Takeoff Sheet deleted successfully.');
    }

    private function checkEditPermission(TakeoffSheet $takeoff)
    {
        $userId = auth()->id();
        if ($userId === $takeoff->created_by) {
            return;
        }

        $hasApproval = TakeoffEditRequest::where('takeoff_sheet_id', $takeoff->id)
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->exists();

        if (!$hasApproval) {
            abort(403, 'You do not have permission to edit this takeoff sheet. Please request access from the creator.');
        }
    }

    public function requestEdit(TakeoffSheet $takeoff)
    {
        if (auth()->id() === $takeoff->created_by) {
            return back()->with('error', 'You are the creator, you already have edit access.');
        }

        TakeoffEditRequest::updateOrCreate(
            ['takeoff_sheet_id' => $takeoff->id, 'user_id' => auth()->id()],
            ['status' => 'pending']
        );

        return back()->with('success', 'Edit access requested.');
    }

    public function approveEdit(TakeoffEditRequest $editRequest)
    {
        $takeoff = $editRequest->takeoffSheet;
        if (auth()->id() !== $takeoff->created_by) {
            abort(403, 'Only the creator can approve edit requests.');
        }

        $editRequest->update(['status' => 'approved']);
        return back()->with('success', 'Edit request approved.');
    }

    public function rejectEdit(TakeoffEditRequest $editRequest)
    {
        $takeoff = $editRequest->takeoffSheet;
        if (auth()->id() !== $takeoff->created_by) {
            abort(403, 'Only the creator can reject edit requests.');
        }

        $editRequest->update(['status' => 'rejected']);
        return back()->with('success', 'Edit request rejected.');
    }

    public function revokeEdit(TakeoffEditRequest $editRequest)
    {
        $takeoff = $editRequest->takeoffSheet;
        if (auth()->id() !== $takeoff->created_by) {
            abort(403, 'Only the creator can revoke edit requests.');
        }

        $editRequest->update(['status' => 'revoked']);
        return back()->with('success', 'Edit access revoked.');
    }

    public function createItem(TakeoffSheet $takeoff)
    {
        return view('takeoff.items.create', compact('takeoff'));
    }

    public function storeSection(Request $request, TakeoffSheet $takeoff)
    {
        $this->checkEditPermission($takeoff);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'schedule_task_id' => 'nullable|exists:schedule_tasks,id',
            'boq_id' => 'nullable|exists:boqs,id',
        ]);

        $takeoff->sections()->create($validated);

        return redirect()->route('takeoff.show', $takeoff)->with('success', 'Section added successfully.');
    }

    public function getSectionBoqItems(TakeoffSheet $takeoff, \App\Models\TakeoffSection $section)
    {
        if (!$section->boq_id) {
            return response()->json([]);
        }

        $items = \App\Models\BoqItem::where('boq_id', $section->boq_id)->get(['id', 'item_code', 'description', 'unit', 'unit_rate']);
        return response()->json($items);
    }

    public function storeItem(Request $request, TakeoffSheet $takeoff)
    {
        $this->checkEditPermission($takeoff);

        $validated = $request->validate([
            'takeoff_section_id' => 'nullable|exists:takeoff_sections,id',
            'element' => 'required|string|max:255',
            'result_unit' => 'required|string|max:50',
            'length' => 'nullable|string',
            'width' => 'nullable|string',
            'height' => 'nullable|string',
            'count' => 'nullable|string',
            'result_quantity' => 'required|numeric|min:0',
            'unit_rate' => 'nullable|numeric|min:0',
            'bar_dia' => 'nullable|numeric',
            'bar_length' => 'nullable|numeric',
            'no_of_bar' => 'nullable|numeric',
        ]);

        $data = $validated;
        
        // Calculate total cost
        $quantity = $data['result_quantity'] ?? 0;
        $rate = $data['unit_rate'] ?? 0;
        $data['total_cost'] = $quantity * $rate;

        // Store dimension formulas in calculation_data
        $data['calculation_data'] = [
            'length' => $data['length'] ?? null,
            'width' => $data['width'] ?? null,
            'height' => $data['height'] ?? null,
            'bar_dia' => $data['bar_dia'] ?? null,
            'bar_length' => $data['bar_length'] ?? null,
            'no_of_bar' => $data['no_of_bar'] ?? null,
        ];
        
        // The count can be a formula too, we'll store the evaluated result in the db (or keep it in calculation_data if preferred)
        // For now, let's just use the evaluated result_quantity provided by the frontend.
        $data['count'] = is_numeric($data['count']) ? $data['count'] : 1;
        
        unset($data['length'], $data['width'], $data['height'], $data['bar_dia'], $data['bar_length'], $data['no_of_bar']);

        $this->takeoffService->addItem($takeoff, $data);

        return redirect()->route('takeoff.show', $takeoff)->with('success', 'Item added successfully.');
    }

    public function destroyItem(TakeoffSheet $takeoff, TakeoffItem $item)
    {
        $this->checkEditPermission($takeoff);
        $item->delete();
        return redirect()->route('takeoff.show', $takeoff)->with('success', 'Item deleted.');
    }

    public function updateItem(Request $request, TakeoffSheet $takeoff, TakeoffItem $item)
    {
        $this->checkEditPermission($takeoff);

        $validated = $request->validate([
            'element'         => 'required|string|max:255',
            'result_unit'     => 'nullable|string|max:50',
            'length'          => 'nullable|string',
            'width'           => 'nullable|string',
            'height'          => 'nullable|string',
            'count'           => 'nullable|string',
            'result_quantity' => 'nullable|numeric|min:0',
            'unit_rate'       => 'nullable|numeric|min:0',
            'bar_dia'         => 'nullable|numeric',
            'bar_length'      => 'nullable|numeric|min:0',
            'no_of_bar'       => 'nullable|numeric|min:0',
        ]);

        $calcData = $item->calculation_data ?? [];
        if ($request->has('length'))     $calcData['length']     = $validated['length']     ?? null;
        if ($request->has('width'))      $calcData['width']      = $validated['width']      ?? null;
        if ($request->has('height'))     $calcData['height']     = $validated['height']     ?? null;
        if ($request->has('bar_dia'))    $calcData['bar_dia']    = $validated['bar_dia']    ?? null;
        if ($request->has('bar_length')) $calcData['bar_length'] = $validated['bar_length'] ?? null;
        if ($request->has('no_of_bar'))  $calcData['no_of_bar']  = $validated['no_of_bar']  ?? null;

        $updateData = [
            'element'          => $validated['element'],
            'calculation_data' => $calcData,
        ];

        if (array_key_exists('count', $validated)) {
            $updateData['count'] = is_numeric($validated['count']) ? (float)$validated['count'] : 1;
        }

        if (array_key_exists('result_quantity', $validated) && $validated['result_quantity'] !== null) {
            $updateData['result_quantity'] = (float)$validated['result_quantity'];
        } elseif (isset($validated['bar_length']) && isset($validated['no_of_bar'])) {
            $noOfBar     = (int)   ($validated['no_of_bar']  ?? 0);
            $count       = (int)   ($validated['count']       ?? 1);
            $barLength   = (float) ($validated['bar_length']  ?? 0);
            $updateData['result_quantity'] = $barLength * $noOfBar * $count;
        }

        if (array_key_exists('result_unit', $validated) && $validated['result_unit']) {
            $updateData['result_unit'] = $validated['result_unit'];
        }

        if (array_key_exists('unit_rate', $validated)) {
            $updateData['unit_rate'] = $validated['unit_rate'];
        }

        $qty  = $updateData['result_quantity'] ?? $item->result_quantity ?? 0;
        $rate = $updateData['unit_rate'] ?? $item->unit_rate ?? 0;
        $updateData['total_cost'] = $qty * $rate;

        $item->update($updateData);

        return redirect()->route('takeoff.show', $takeoff)->with('success', 'Item updated.');
    }

    public function toggleHeader(TakeoffSheet $takeoff, TakeoffItem $item)
    {
        $this->checkEditPermission($takeoff);
        $item->update(['is_header' => !$item->is_header]);
        return redirect()->route('takeoff.show', $takeoff)->with('success', 'Item updated.');
    }

    public function destroySection(TakeoffSheet $takeoff, TakeoffSection $section)
    {
        $this->checkEditPermission($takeoff);
        // Delete all items in section, then the section itself
        $section->items()->delete();
        $section->delete();
        return redirect()->route('takeoff.show', $takeoff)->with('success', 'Section deleted.');
    }

    public function convert(TakeoffSheet $takeoff)
    {
        $takeoff->load(['sections.items', 'sections.task', 'project', 'creator']);

        // Build section totals: sum of all item result_quantities per section
        foreach ($takeoff->sections as $section) {
            $section->total_quantity = $section->items->sum('result_quantity');
            $section->primary_unit   = $section->items->first()?->result_unit ?? '';
            // Duration from linked schedule task: prefer duration_days, fallback to date diff
            $task = $section->task;
            if ($task) {
                if ($task->duration_days) {
                    $section->schedule_duration_days = $task->duration_days;
                } elseif ($task->start_date && $task->end_date) {
                    $section->schedule_duration_days = $task->start_date->diffInDays($task->end_date) + 1;
                } else {
                    $section->schedule_duration_days = null; // no duration info at all
                }
            } else {
                $section->schedule_duration_days = null;
            }
        }

        // Load all standard works with their sub-resources for the dropdowns
        $standardWorks = StandardWork::with(['materials', 'manpower', 'equipment'])->get();

        // Encode for JavaScript
        $standardWorksJson = $standardWorks->map(fn($sw) => [
            'id'                  => $sw->id,
            'name'                => $sw->name,
            'unit'                => $sw->unit,
            'category'            => $sw->category,
            'default_productivity'=> (float) $sw->default_productivity,
            'materials' => $sw->materials->map(fn($m) => [
                'name'     => $m->material_name,
                'quantity' => $m->quantity,
                'unit'     => $m->unit,
            ])->values(),
            'manpower' => $sw->manpower->map(fn($m) => [
                'name'     => $m->role,
                'quantity' => $m->quantity,
                'unit'     => $m->unit,
            ])->values(),
            'equipment' => $sw->equipment->map(fn($e) => [
                'name'     => $e->equipment_name,
                'quantity' => $e->quantity,
                'unit'     => $e->unit,
            ])->values(),
        ])->values();

        $stores = Store::orderBy('name')->get();

        // Registered products for manual material selection
        $registeredProducts = Product::orderBy('name')
            ->get(['id', 'name', 'unit', 'category', 'unit_price'])
            ->map(fn($p) => [
                'id'       => $p->id,
                'name'     => $p->name,
                'unit'     => $p->unit,
                'category' => $p->category,
                'rate'     => (float) $p->unit_price,
            ])->values();

        // Registered equipment for manual equipment selection
        $registeredEquipment = EquipmentMaster::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'unit', 'category', 'hourly_rate', 'daily_rate'])
            ->map(fn($e) => [
                'id'       => $e->id,
                'name'     => $e->name,
                'unit'     => $e->unit,
                'category' => $e->category,
                'rate'     => (float) $e->hourly_rate,
            ])->values();

        // Registered roles/designations for manual manpower selection
        $registeredRoles = Designation::where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title', 'min_salary'])
            ->map(fn($d) => [
                'id'   => $d->id,
                'name' => $d->title,
                'unit' => 'man-day',
                'rate' => (float) ($d->min_salary ? round($d->min_salary / 26, 2) : 0),
            ])->values();

        return view('takeoff.convert', compact(
            'takeoff', 'standardWorks', 'standardWorksJson', 'stores',
            'registeredProducts', 'registeredEquipment', 'registeredRoles'
        ));
    }

    public function processConversion(Request $request, TakeoffSheet $takeoff)
    {
        $request->validate([
            'plan_name'       => 'required|string|max:255',
            'plan_start_date' => 'required|date',
            'plan_end_date'   => 'required|date|after_or_equal:plan_start_date',
            'notes'           => 'nullable|string|max:500',
            'sections'        => 'required|array',
        ]);

        // Calculate total budget from all resources
        $totalBudget = 0;
        foreach ($request->input('sections', []) as $sectionData) {
            foreach ($sectionData['resources'] ?? [] as $resource) {
                $qty  = (float)($sectionData['section_total'] ?? 0) * (float)($resource['ratio'] ?? 1);
                $rate = (float)($resource['rate'] ?? 0);
                $totalBudget += round($qty * $rate, 2);
            }
        }

        // Create ERP Plan Header
        $plan = ErpPlanHeader::create([
            'project_id'          => $takeoff->project_id,
            'name'                => $request->plan_name,
            'description'         => $request->notes ?? ('From Takeoff: ' . $takeoff->title),
            'plan_start_date'     => $request->plan_start_date,
            'plan_end_date'       => $request->plan_end_date,
            'total_duration_days' => now()->parse($request->plan_start_date)->diffInDays(now()->parse($request->plan_end_date)),
            'total_budget'        => $totalBudget,
            'status'              => 'draft',
            'overall_progress'    => 0,
            'created_by'          => auth()->id(),
        ]);

        // Create one task per section
        $wbsIdx = 1;
        foreach ($request->input('sections', []) as $sIdx => $sectionData) {
            $sectionName  = $sectionData['section_name'] ?? "Section {$sIdx}";
            $sectionTotal = (float) ($sectionData['section_total'] ?? 0);
            $resources    = $sectionData['resources'] ?? [];
            $startDate    = $request->plan_start_date;
            $endDate      = $request->plan_end_date;

            // Calculate planned cost for this task
            $taskCost = 0;
            foreach ($resources as $resource) {
                $qty = $sectionTotal * (float)($resource['ratio'] ?? 1);
                $taskCost += round($qty * (float)($resource['rate'] ?? 0), 2);
            }

            $task = ErpPlanTask::create([
                'plan_header_id'   => $plan->id,
                'parent_task_id'   => null,
                'wbs_code'         => (string)$wbsIdx,
                'name'             => $sectionName,
                'description'      => "Section from takeoff: {$takeoff->title}. Total Qty: {$sectionTotal}",
                'start_date'       => $startDate,
                'end_date'         => $endDate,
                'duration_days'    => now()->parse($startDate)->diffInDays(now()->parse($endDate)),
                'planned_progress' => 0,
                'actual_progress'  => 0,
                'planned_cost'     => $taskCost,
                'actual_cost'      => 0,
                'status'           => 'not_started',
                'sort_order'       => $wbsIdx,
            ]);

            // Add resources to the task
            foreach ($resources as $resource) {
                if (empty($resource['name'])) continue;
                $qty  = round($sectionTotal * (float)($resource['ratio'] ?? 1), 3);
                $rate = (float)($resource['rate'] ?? 0);

                ErpPlanTaskResource::create([
                    'task_id'       => $task->id,
                    'resource_type' => strtolower($resource['type'] ?? 'material'),
                    'resource_name' => $resource['name'],
                    'quantity'      => $qty,
                    'unit'          => $resource['unit'] ?? '',
                    'rate'          => $rate,
                    'total_cost'    => round($qty * $rate, 2),
                    'details'       => [
                        'section_name'  => $sectionName,
                        'section_total' => $sectionTotal,
                        'ratio'         => $resource['ratio'] ?? 1,
                        'from_takeoff'  => $takeoff->id,
                    ],
                ]);
            }
            $wbsIdx++;
        }

        return redirect()
            ->route('erp-plans.show', $plan)
            ->with('success', 'ERP Plan "' . $plan->name . '" created successfully from takeoff with ' . ($wbsIdx - 1) . ' tasks.');
    }

    // ─────────────────────────────────────────────────────────────────
    // Rebar Cut Optimization
    // ─────────────────────────────────────────────────────────────────

    /**
     * AJAX: Run cut optimization on this rebar takeoff sheet.
     * Returns JSON with cutting plan per diameter.
     */
    public function rebarCutOptimize(TakeoffSheet $takeoff)
    {
        $takeoff->load(['sections.items']);

        $service = new RebarCutOptimizationService();

        try {
            $results        = $service->optimizeFromSections($takeoff->sections);
            $sectionResults = $service->optimizeBySections($takeoff->sections);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'takeoff_id'      => $takeoff->id,
            'takeoff_title'   => $takeoff->title,
            'bar_length'      => $service->barLength,
            'kerf'            => $service->kerf,
            'results'         => $results,
            'section_results' => $sectionResults,
        ]);
    }

    /**
     * Save rebar cut optimization result to an ERP Plan.
     */
    public function rebarConvertToErpPlan(Request $request, TakeoffSheet $takeoff)
    {
        $validated = $request->validate([
            'plan_name'       => 'required|string|max:255',
            'plan_start_date' => 'required|date',
            'plan_end_date'   => 'required|date|after_or_equal:plan_start_date',
            'notes'           => 'nullable|string|max:500',
            'results'         => 'nullable|array',
        ]);

        $takeoff->load(['sections.items']);

        $service = new RebarCutOptimizationService();
        $unitWeights = [
            8  => 0.395, 10 => 0.617, 12 => 0.889,
            14 => 1.210, 16 => 1.580, 20 => 2.469,
            24 => 3.550, 32 => 6.313,
        ];

        // Create ERP Plan Header
        $plan = ErpPlanHeader::create([
            'project_id'          => $takeoff->project_id,
            'name'                => $validated['plan_name'],
            'description'         => $validated['notes'] ?? ('Rebar cut plan from: ' . $takeoff->title),
            'plan_start_date'     => $validated['plan_start_date'],
            'plan_end_date'       => $validated['plan_end_date'],
            'total_duration_days' => now()->parse($validated['plan_start_date'])->diffInDays(now()->parse($validated['plan_end_date'])),
            'total_budget'        => 0,
            'status'              => 'draft',
            'overall_progress'    => 0,
            'created_by'          => auth()->id(),
        ]);

        $wbsIdx = 1;
        $totalTaskCount = 0;

        // Group tasks by takeoff section
        foreach ($takeoff->sections as $section) {
            $sectionName = $section->name ?: ("Section " . $wbsIdx);

            // Compute cut optimization specifically for this section
            $sectionDiaResults = $service->optimizeFromSections(collect([$section]));

            if (empty($sectionDiaResults)) {
                continue;
            }

            // Create a task named after the takeoff section
            $task = ErpPlanTask::create([
                'plan_header_id'   => $plan->id,
                'parent_task_id'   => null,
                'wbs_code'         => (string) $wbsIdx,
                'name'             => $sectionName,
                'description'      => "Rebar schedule section: {$sectionName} (From takeoff: {$takeoff->title})",
                'start_date'       => $validated['plan_start_date'],
                'end_date'         => $validated['plan_end_date'],
                'duration_days'    => now()->parse($validated['plan_start_date'])->diffInDays(now()->parse($validated['plan_end_date'])),
                'planned_progress' => 0,
                'actual_progress'  => 0,
                'planned_cost'     => 0,
                'actual_cost'      => 0,
                'status'           => 'not_started',
                'sort_order'       => $wbsIdx,
            ]);

            // Add resources (1 resource per diameter needed in this section)
            foreach ($sectionDiaResults as $diaResult) {
                $dia  = (int) $diaResult['dia'];
                $bars = (int) ($diaResult['result']['total_bars'] ?? 0);
                if ($bars <= 0) continue;

                $wpm           = $unitWeights[$dia] ?? 0;
                $totalLenUsed  = round($bars * 12.0, 3);
                $totalWeightKg = round($totalLenUsed * $wpm, 2);

                ErpPlanTaskResource::create([
                    'task_id'       => $task->id,
                    'resource_type' => 'material',
                    'resource_name' => "Rebar Ø{$dia}mm × 12m",
                    'quantity'      => $bars,
                    'unit'          => 'bars',
                    'rate'          => 0,
                    'total_cost'    => 0,
                    'details'       => [
                        'section_name'     => $sectionName,
                        'diameter_mm'      => $dia,
                        'total_bars'       => $bars,
                        'bar_length_m'     => 12.0,
                        'total_length_m'   => $totalLenUsed,
                        'total_weight_kg'  => $totalWeightKg,
                        'weight_per_m'     => $wpm,
                        'from_takeoff'     => $takeoff->id,
                    ],
                ]);
            }

            $wbsIdx++;
            $totalTaskCount++;
        }

        // Fallback if sheet has no sections with items
        if ($totalTaskCount === 0 && !empty($validated['results'])) {
            foreach ($validated['results'] as $diaResult) {
                $dia   = (int) $diaResult['dia'];
                $bars  = (int) $diaResult['total_bars'];
                $wpm   = $unitWeights[$dia] ?? 0;
                $totalLenUsed   = round($bars * 12.0, 3);
                $totalWeightKg  = round($totalLenUsed * $wpm, 2);

                $task = ErpPlanTask::create([
                    'plan_header_id'   => $plan->id,
                    'parent_task_id'   => null,
                    'wbs_code'         => (string) $wbsIdx,
                    'name'             => "Rebar Ø{$dia}mm",
                    'description'      => "Cut optimization for Ø{$dia}mm rebar from: {$takeoff->title}. Bars: {$bars} × 12m",
                    'start_date'       => $validated['plan_start_date'],
                    'end_date'         => $validated['plan_end_date'],
                    'duration_days'    => now()->parse($validated['plan_start_date'])->diffInDays(now()->parse($validated['plan_end_date'])),
                    'planned_progress' => 0,
                    'actual_progress'  => 0,
                    'planned_cost'     => 0,
                    'actual_cost'      => 0,
                    'status'           => 'not_started',
                    'sort_order'       => $wbsIdx,
                ]);

                ErpPlanTaskResource::create([
                    'task_id'       => $task->id,
                    'resource_type' => 'material',
                    'resource_name' => "Rebar Ø{$dia}mm × 12m",
                    'quantity'      => $bars,
                    'unit'          => 'bars',
                    'rate'          => 0,
                    'total_cost'    => 0,
                    'details'       => [
                        'diameter_mm'     => $dia,
                        'total_bars'      => $bars,
                        'bar_length_m'    => 12.0,
                        'total_length_m'  => $totalLenUsed,
                        'total_weight_kg' => $totalWeightKg,
                        'weight_per_m'    => $wpm,
                        'from_takeoff'    => $takeoff->id,
                    ],
                ]);
                $wbsIdx++;
                $totalTaskCount++;
            }
        }

        return response()->json([
            'success'  => true,
            'plan_id'  => $plan->id,
            'plan_url' => route('erp-plans.show', $plan),
            'message'  => 'ERP Plan "' . $plan->name . '" created with ' . $totalTaskCount . ' section task(s).',
        ]);
    }
}
