<?php

namespace App\Http\Controllers;

use App\Models\Boq;
use App\Models\BoqItem;
use App\Models\ClientIpc;
use App\Models\ClientIpcItem;
use App\Models\Payment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientIpcController extends Controller
{
    // ── List ────────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = ClientIpc::with(['project', 'createdBy'])->latest();

        if ($request->project_id) $query->where('project_id', $request->project_id);
        if ($request->status)     $query->where('status', $request->status);

        $ipcs     = $query->paginate(20)->withQueryString();
        $projects = Project::orderBy('name')->get(['id', 'name']);

        return view('finance.client-ipcs.index', compact('ipcs', 'projects'));
    }

    // ── Create Form ──────────────────────────────────────────────────────────────
    public function create(Request $request)
    {
        $projects = Project::where('status', 'active')->orderBy('name')->get();
        $selectedProject = $request->project_id ? Project::find($request->project_id) : null;
        $selectedBoq     = null;
        $boqItems        = collect();

        if ($request->boq_id) {
            $selectedBoq = Boq::with('items')->find($request->boq_id);
            $boqItems    = $selectedBoq?->items ?? collect();
        } elseif ($selectedProject) {
            $selectedBoq = Boq::where('project_id', $selectedProject->id)
                               ->where('status', 'approved')->latest()->first();
            $boqItems = $selectedBoq?->items ?? collect();
        }

        $boqs = $selectedProject
            ? Boq::where('project_id', $selectedProject->id)->where('status', 'approved')->get()
            : collect();

        // Previous certified amount for this project
        $previousCertified = $selectedProject
            ? ClientIpc::where('project_id', $selectedProject->id)
                       ->whereIn('status', ['approved', 'paid'])->sum('gross_amount')
            : 0;

        return view('finance.client-ipcs.create', compact(
            'projects', 'selectedProject', 'selectedBoq', 'boqs', 'boqItems', 'previousCertified'
        ));
    }

    // ── Store ────────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id'       => 'required|exists:projects,id',
            'boq_id'           => 'required|exists:boqs,id',
            'period_from'      => 'required|date',
            'period_to'        => 'required|date|after_or_equal:period_from',
            'submission_date'  => 'nullable|date',
            'retention_percent'=> 'required|numeric|min:0|max:100',
            'notes'            => 'nullable|string',
            'items'            => 'required|array|min:1',
            'items.*.boq_item_id'       => 'nullable|exists:boq_items,id',
            'items.*.description'       => 'required|string',
            'items.*.unit'              => 'nullable|string|max:50',
            'items.*.boq_quantity'      => 'required|numeric|min:0',
            'items.*.previous_quantity' => 'required|numeric|min:0',
            'items.*.current_quantity'  => 'required|numeric|min:0',
            'items.*.unit_rate'         => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $projectId = $validated['project_id'];

            $previousCertified = ClientIpc::where('project_id', $projectId)
                ->whereIn('status', ['approved', 'paid'])->sum('gross_amount');

            $ipcNumber = ClientIpc::where('project_id', $projectId)->count() + 1;

            $ipc = ClientIpc::create([
                'ipc_no'            => ClientIpc::generateIpcNo($projectId),
                'project_id'        => $projectId,
                'boq_id'            => $validated['boq_id'],
                'ipc_number'        => $ipcNumber,
                'period_from'       => $validated['period_from'],
                'period_to'         => $validated['period_to'],
                'submission_date'   => $validated['submission_date'] ?? today(),
                'retention_percent' => $validated['retention_percent'],
                'previous_certified'=> $previousCertified,
                'notes'             => $validated['notes'],
                'status'            => 'draft',
                'created_by'        => auth()->id(),
            ]);

            $grossTotal = 0;
            foreach ($validated['items'] as $item) {
                $cumQty   = $item['previous_quantity'] + $item['current_quantity'];
                $curAmt   = round($item['current_quantity'] * $item['unit_rate'], 2);
                $cumAmt   = round($cumQty * $item['unit_rate'], 2);
                $grossTotal += $curAmt;

                ClientIpcItem::create([
                    'client_ipc_id'       => $ipc->id,
                    'boq_item_id'         => $item['boq_item_id'] ?? null,
                    'description'         => $item['description'],
                    'unit'                => $item['unit'] ?? null,
                    'boq_quantity'        => $item['boq_quantity'],
                    'previous_quantity'   => $item['previous_quantity'],
                    'current_quantity'    => $item['current_quantity'],
                    'cumulative_quantity' => $cumQty,
                    'unit_rate'           => $item['unit_rate'],
                    'current_amount'      => $curAmt,
                    'cumulative_amount'   => $cumAmt,
                ]);
            }

            $retention = round($grossTotal * ($validated['retention_percent'] / 100), 2);

            $ipc->update([
                'gross_amount'         => $grossTotal,
                'retention_amount'     => $retention,
                'net_amount'           => $grossTotal - $retention,
                'cumulative_certified' => $previousCertified + $grossTotal,
            ]);
        });

        return redirect()->route('client-ipcs.index')->with('success', 'Payment Certificate (IPC) created successfully!');
    }

    // ── Show ─────────────────────────────────────────────────────────────────────
    public function show(ClientIpc $clientIpc)
    {
        $clientIpc->load(['project', 'boq', 'items.boqItem', 'createdBy', 'approvedBy', 'payments']);
        return view('finance.client-ipcs.show', compact('clientIpc'));
    }

    // ── Edit ─────────────────────────────────────────────────────────────────────
    public function edit(ClientIpc $clientIpc)
    {
        abort_if(!in_array($clientIpc->status, ['draft', 'under_review']), 403, 'Only draft IPCs can be edited.');

        $clientIpc->load('items.boqItem');
        $projects = Project::where('status', 'active')->orderBy('name')->get();
        $boqs     = Boq::where('project_id', $clientIpc->project_id)->where('status', 'approved')->get();
        $boqItems = $clientIpc->boq ? BoqItem::where('boq_id', $clientIpc->boq_id)->get() : collect();

        return view('finance.client-ipcs.edit', compact('clientIpc', 'projects', 'boqs', 'boqItems'));
    }

    // ── Update ───────────────────────────────────────────────────────────────────
    public function update(Request $request, ClientIpc $clientIpc)
    {
        abort_if(!in_array($clientIpc->status, ['draft', 'under_review']), 403);

        $validated = $request->validate([
            'period_from'       => 'required|date',
            'period_to'         => 'required|date|after_or_equal:period_from',
            'submission_date'   => 'nullable|date',
            'retention_percent' => 'required|numeric|min:0|max:100',
            'notes'             => 'nullable|string',
            'items'             => 'required|array|min:1',
            'items.*.id'                => 'nullable|exists:client_ipc_items,id',
            'items.*.description'       => 'required|string',
            'items.*.unit'              => 'nullable|string',
            'items.*.boq_quantity'      => 'required|numeric|min:0',
            'items.*.previous_quantity' => 'required|numeric|min:0',
            'items.*.current_quantity'  => 'required|numeric|min:0',
            'items.*.unit_rate'         => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $clientIpc) {
            $clientIpc->update([
                'period_from'       => $validated['period_from'],
                'period_to'         => $validated['period_to'],
                'submission_date'   => $validated['submission_date'],
                'retention_percent' => $validated['retention_percent'],
                'notes'             => $validated['notes'],
            ]);

            // Delete all items and re-insert
            $clientIpc->items()->delete();

            $grossTotal = 0;
            foreach ($validated['items'] as $item) {
                $cumQty  = $item['previous_quantity'] + $item['current_quantity'];
                $curAmt  = round($item['current_quantity'] * $item['unit_rate'], 2);
                $grossTotal += $curAmt;

                ClientIpcItem::create([
                    'client_ipc_id'       => $clientIpc->id,
                    'boq_item_id'         => $item['boq_item_id'] ?? null,
                    'description'         => $item['description'],
                    'unit'                => $item['unit'] ?? null,
                    'boq_quantity'        => $item['boq_quantity'],
                    'previous_quantity'   => $item['previous_quantity'],
                    'current_quantity'    => $item['current_quantity'],
                    'cumulative_quantity' => $cumQty,
                    'unit_rate'           => $item['unit_rate'],
                    'current_amount'      => $curAmt,
                    'cumulative_amount'   => round($cumQty * $item['unit_rate'], 2),
                ]);
            }

            $retention = round($grossTotal * ($validated['retention_percent'] / 100), 2);
            $clientIpc->update([
                'gross_amount'         => $grossTotal,
                'retention_amount'     => $retention,
                'net_amount'           => $grossTotal - $retention,
                'cumulative_certified' => $clientIpc->previous_certified + $grossTotal,
            ]);
        });

        return redirect()->route('client-ipcs.show', $clientIpc)->with('success', 'IPC updated.');
    }

    // ── Submit (Draft → Submitted) ────────────────────────────────────────────────
    public function submit(ClientIpc $clientIpc)
    {
        abort_if($clientIpc->status !== 'draft', 403);
        $clientIpc->update(['status' => 'submitted']);
        return back()->with('success', 'IPC submitted for review.');
    }

    // ── Approve ───────────────────────────────────────────────────────────────────
    public function approve(ClientIpc $clientIpc)
    {
        abort_if(in_array($clientIpc->status, ['approved', 'paid', 'rejected']), 403);
        $clientIpc->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'IPC approved. You can now record a payment against it.');
    }

    // ── Record Payment ────────────────────────────────────────────────────────────
    public function recordPayment(Request $request, ClientIpc $clientIpc)
    {
        abort_if($clientIpc->status !== 'approved', 403);

        $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_date'   => 'required|date',
            'payment_type'   => 'required|in:progress,advance,retention_release,final',
            'reference_number'=> 'nullable|string|max:100',
            'notes'          => 'nullable|string',
        ]);

        Payment::create([
            'project_id'       => $clientIpc->project_id,
            'client_ipc_id'    => $clientIpc->id,
            'reference_number' => $request->reference_number ?? 'PMT-' . date('Ymd') . '-' . rand(100, 999),
            'amount'           => $request->amount,
            'payment_date'     => $request->payment_date,
            'payment_type'     => $request->payment_type,
            'description'      => "Payment for IPC {$clientIpc->ipc_no}",
            'notes'            => $request->notes,
            'created_by'       => auth()->id(),
        ]);

        // Check if fully paid
        $totalPaid = $clientIpc->payments()->sum('amount');
        if ($totalPaid >= $clientIpc->net_amount) {
            $clientIpc->update(['status' => 'paid']);
        }

        return back()->with('success', 'Payment of ETB ' . number_format($request->amount, 2) . ' recorded.');
    }

    // ── AJAX: Get BOQ items for project (for dynamic form) ────────────────────────
    public function boqItems(Request $request)
    {
        $boq = Boq::with('items')->find($request->boq_id);
        if (!$boq) return response()->json([]);

        return response()->json($boq->items->map(fn($item) => [
            'id'          => $item->id,
            'description' => $item->description,
            'unit'        => $item->unit,
            'quantity'    => $item->quantity,
            'unit_rate'   => $item->unit_rate,
        ]));
    }
}
