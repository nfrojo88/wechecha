<?php
namespace App\Http\Controllers;

use App\Models\ErpPlanHeader;
use App\Models\Project;
use App\Services\ErpPlanService;
use Illuminate\Http\Request;

class ErpPlanController extends Controller
{
    public function __construct(private ErpPlanService $erpPlanService)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $takeoffs = \App\Models\TakeoffSheet::with('project', 'creator')->latest()->get();
        $schedules = \App\Models\Schedule::with('project')->latest()->get();
        return view('erp_plans.index', compact('takeoffs', 'schedules'));
    }

    public function create()
    {
        $projects = Project::whereIn('status', ['active', 'Active', 'planning', 'Planning'])->get();
        return view('erp_plans.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id'         => 'required|exists:projects,id',
            'name'               => 'required|string|max:255',
            'description'        => 'nullable|string',
            'plan_start_date'    => 'required|date',
            'plan_end_date'      => 'required|date|after_or_equal:plan_start_date',
            'total_budget'       => 'nullable|numeric|min:0',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status']     = 'draft';
        $validated['total_duration_days'] = (int) now()->parse($validated['plan_start_date'])
            ->diffInDays(now()->parse($validated['plan_end_date']));

        $plan = ErpPlanHeader::create($validated);

        return redirect()->route('erp-plans.show', $plan)
            ->with('success', 'ERP Plan created successfully.');
    }

    public function show(ErpPlanHeader $erp_plan)
    {
        $erp_plan->load('tasks.dependencies', 'tasks.resources', 'baselines', 'project', 'creator');

        // Dynamically compute zero-rate resources using latest market prices / equipment rates / manpower rates
        foreach ($erp_plan->tasks as $task) {
            foreach ($task->resources as $res) {
                if ((float) $res->rate <= 0) {
                    $rate = 0;
                    $rtype = strtolower($res->resource_type);
                    if ($rtype === 'material') {
                        $product = \App\Models\Product::where('name', $res->resource_name)->first();
                        if ($product) {
                            $latestMarket = \App\Models\MaterialPrice::where('product_id', $product->id)->orderBy('effective_date', 'desc')->first();
                            $rate = $latestMarket ? (float)$latestMarket->price : (float)$product->unit_price;
                        }
                    } elseif ($rtype === 'equipment') {
                        $eq = \App\Models\EquipmentMaster::where('name', $res->resource_name)->first();
                        if ($eq) {
                            $rate = (float)($eq->daily_rate ?: $eq->hourly_rate);
                        }
                    } elseif ($rtype === 'manpower') {
                        $role = \App\Models\Designation::where('title', $res->resource_name)->first();
                        if ($role && $role->min_salary) {
                            $rate = (float) round($role->min_salary / 26, 2);
                        }
                    }
                    if ($rate > 0) {
                        $res->rate = $rate;
                        $res->total_cost = round($res->quantity * $rate, 2);
                    }
                }
            }
        }

        return view('erp_plans.show', compact('erp_plan'));
    }

    public function edit(ErpPlanHeader $erp_plan)
    {
        $projects = Project::whereIn('status', ['active', 'Active', 'planning', 'Planning'])->get();
        return view('erp_plans.edit', compact('erp_plan', 'projects'));
    }

    public function update(Request $request, ErpPlanHeader $erp_plan)
    {
        $validated = $request->validate([
            'project_id'         => 'required|exists:projects,id',
            'name'               => 'required|string|max:255',
            'description'        => 'nullable|string',
            'plan_start_date'    => 'required|date',
            'plan_end_date'      => 'required|date|after_or_equal:plan_start_date',
            'total_budget'       => 'nullable|numeric|min:0',
            'status'             => 'required|in:draft,active,on_hold,completed,cancelled',
        ]);

        $validated['total_duration_days'] = (int) now()->parse($validated['plan_start_date'])
            ->diffInDays(now()->parse($validated['plan_end_date']));

        $erp_plan->update($validated);

        return redirect()->route('erp-plans.show', $erp_plan)
            ->with('success', 'ERP Plan updated successfully.');
    }

    public function destroy(ErpPlanHeader $erp_plan)
    {
        $erp_plan->delete();
        return redirect()->route('erp-plans.index')
            ->with('success', 'ERP Plan deleted.');
    }
}
