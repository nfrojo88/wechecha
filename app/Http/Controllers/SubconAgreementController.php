<?php

namespace App\Http\Controllers;

use App\Models\SubconAgreement;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\TakeoffSheet;
use App\Models\TakeoffItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SubconAgreementController extends Controller
{
    public function index()
    {
        $agreements = SubconAgreement::with(['project', 'createdBy'])
            ->latest()
            ->paginate(20);
        
        // Add status counts
        $statusCounts = [
            'all' => SubconAgreement::count(),
            'draft' => SubconAgreement::where('status', 'draft')->count(),
            'pending' => SubconAgreement::where('status', 'pending')->count(),
            'approved' => SubconAgreement::where('status', 'approved')->count(),
            'active' => SubconAgreement::where('status', 'active')->count(),
        ];
        
        return view('procurement.subcon-agreements.index', compact('agreements', 'statusCounts'));
    }

    public function create()
    {
        $projects = Project::where('status', 'active')->get();
        $suppliers = Supplier::where('status', 'active')->get();
        $takeoffs = TakeoffSheet::where('status', 'approved')->with('project')->latest()->get();
        
        return view('procurement.subcon-agreements.create', compact('projects', 'suppliers', 'takeoffs'));
    }

    /**
     * Get takeoff items for selected takeoff sheet
     * Used by AJAX in create view
     */
    public function getTakeoffItems(Request $request)
    {
        $takeoffId = $request->input('takeoff_id');
        
        if (!$takeoffId) {
            return response()->json(['items' => []]);
        }

        $items = TakeoffItem::where('takeoff_sheet_id', $takeoffId)
            ->with(['section', 'product'])
            ->select('id', 'takeoff_sheet_id', 'takeoff_section_id', 'description', 'quantity', 'unit', 'estimated_rate')
            ->get();

        return response()->json(['items' => $items]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id'       => 'required|exists:projects,id',
            'supplier_id'      => 'required|exists:suppliers,id',
            'takeoff_sheet_id' => 'nullable|exists:takeoff_sheets,id',
            'work_description' => 'required|string',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'items'            => 'required|array|min:1',
            'items.*.task_description' => 'required|string|max:255',
            'items.*.quantity'         => 'required|numeric|min:0.01',
            'items.*.unit'             => 'required|string|max:20',
            'items.*.unit_rate'        => 'required|numeric|min:0',
            'takeoff_items'    => 'nullable|array',
            'takeoff_items.*'  => 'exists:takeoff_items,id',
        ]);

        DB::transaction(function () use ($request) {
            // Create main subcon agreement
            $agr = SubconAgreement::create([
                'agreement_no'     => 'SUB-' . date('Ymd') . '-' . str_pad(SubconAgreement::count() + 1, 4, '0', STR_PAD_LEFT),
                'project_id'       => $request->project_id,
                'supplier_id'      => $request->supplier_id,
                'takeoff_sheet_id' => $request->takeoff_sheet_id,
                'work_description' => $request->work_description,
                'start_date'       => $request->start_date,
                'end_date'         => $request->end_date,
                'status'           => 'draft',
                'created_by'       => Auth::id(),
            ]);

            // Add manual items
            foreach ($request->items as $item) {
                $total = $item['quantity'] * $item['unit_rate'];
                $agr->items()->create([
                    'task_description' => $item['task_description'],
                    'quantity'         => $item['quantity'],
                    'unit'             => $item['unit'],
                    'unit_rate'        => $item['unit_rate'],
                    'total_amount'     => $total,
                ]);
            }

            // Attach selected takeoff items if provided
            if ($request->has('takeoff_items') && $request->takeoff_items) {
                $takeoffData = [];
                $takeoffItems = TakeoffItem::whereIn('id', $request->takeoff_items)->get();
                
                foreach ($takeoffItems as $item) {
                    $rate = $request->input("takeoff_rate_{$item->id}") ?? $item->estimated_rate ?? 0;
                    $quantity = $request->input("takeoff_qty_{$item->id}") ?? $item->quantity ?? 0;
                    $totalAmount = $quantity * $rate;

                    $takeoffData[$item->id] = [
                        'selected_quantity' => $quantity,
                        'rate' => $rate,
                        'total_amount' => $totalAmount,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $agr->takeoffItems()->attach($takeoffData);
            }

            // Calculate total
            $manualTotal = $agr->items()->sum('total_amount');
            $takeoffTotal = $agr->takeoffItems()->sum('subcon_agreement_takeoff_items.total_amount');
            $agr->update(['total_amount' => $manualTotal + $takeoffTotal]);
        });

        return redirect()->route('subcon-agreements.index')->with('success', 'Subcontractor Agreement created successfully.');
    }

    public function show(SubconAgreement $subconAgreement)
    {
        $subconAgreement->load([
            'project', 
            'createdBy',
            'approvedBy',
            'items', 
            'takeoffItems.section',
            'takeoffSheet.items',
            'ipcs'
        ]);
        
        return view('procurement.subcon-agreements.show', compact('subconAgreement'));
    }

    /**
     * Approve subcon agreement (HR Manager or Admin)
     */
    public function approve(Request $request, SubconAgreement $subconAgreement)
    {
        $this->authorize('hr.manage');

        $subconAgreement->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Subcon agreement approved.');
    }

    /**
     * Reject subcon agreement
     */
    public function reject(Request $request, SubconAgreement $subconAgreement)
    {
        $this->authorize('hr.manage');

        $subconAgreement->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('reason'),
            'approved_by' => Auth::id(),
        ]);

        return back()->with('success', 'Subcon agreement rejected.');
    }

    /**
     * Activate approved agreement
     */
    public function activate(Request $request, SubconAgreement $subconAgreement)
    {
        $this->authorize('hr.manage');

        if ($subconAgreement->status !== 'approved') {
            return back()->withErrors('Only approved agreements can be activated.');
        }

        $subconAgreement->update(['status' => 'active']);

        return back()->with('success', 'Subcon agreement activated.');
    }
}
