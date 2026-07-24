<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\PurchaseOrder;
use App\Models\EmergencyFund;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class ApprovalHubController extends Controller
{
    public function index(Request $request)
    {
        // Gate::authorize('finance.view'); // Optional based on your authorization logic

        $items = new Collection();

        // 1. Fetch Expenses
        $expenses = Expense::with(['project'])->get()->map(function ($exp) {
            return (object) [
                'id_raw'      => $exp->id,
                'id_formatted'=> 'EXP-' . str_pad($exp->id, 5, '0', STR_PAD_LEFT),
                'type'        => 'expense',
                'date'        => $exp->expense_date,
                'project'     => $exp->project ? $exp->project->name : 'N/A',
                'category'    => ucfirst($exp->category),
                'description' => $exp->description,
                'base_amount' => (float) $exp->amount,
                'vat_amount'  => 0,
                'net_amount'  => (float) $exp->amount,
                'status'      => $exp->status ?? 'pending',
                'color'       => 'primary',
                'route_show'  => route('expenses.show', $exp->id),
                'route_approve' => route('expenses.approve', $exp->id),
                'route_reject'  => route('expenses.reject', $exp->id),
            ];
        });
        $items = $items->concat($expenses);

        // 2. Fetch Purchase Orders
        $pos = PurchaseOrder::with(['project'])->get()->map(function ($po) {
            return (object) [
                'id_raw'      => $po->id,
                'id_formatted'=> $po->reference_number ?? 'PUR-' . str_pad($po->id, 5, '0', STR_PAD_LEFT),
                'type'        => 'purchase_order',
                'date'        => $po->issued_date ?? $po->created_at,
                'project'     => $po->project ? $po->project->name : 'N/A',
                'category'    => 'Purchase',
                'description' => 'Supplier: ' . $po->supplier_name,
                'base_amount' => (float) $po->total_amount,
                'vat_amount'  => 0,
                'net_amount'  => (float) $po->total_amount,
                'status'      => $po->status === 'draft' ? 'pending' : $po->status, // Adjust based on your workflow
                'color'       => 'success',
                'route_show'  => route('purchase-orders.show', $po->id),
                'route_approve' => route('purchase-orders.issue', $po->id), // Example: issuing PO acts as approval
                'route_reject'  => '#', // Implement rejection route if exists
            ];
        });
        $items = $items->concat($pos);

        // 3. Fetch Emergency Funds if exists
        if (class_exists(EmergencyFund::class)) {
            $efs = EmergencyFund::with(['project'])->get()->map(function ($ef) {
                return (object) [
                    'id_raw'      => $ef->id,
                    'id_formatted'=> 'EMR-' . str_pad($ef->id, 5, '0', STR_PAD_LEFT),
                    'type'        => 'emergency_fund',
                    'date'        => $ef->created_at,
                    'project'     => $ef->project ? $ef->project->name : 'N/A',
                    'category'    => 'Manpower', // Just an example based on screenshot
                    'description' => $ef->justification ?? 'Emergency Manpower Release',
                    'base_amount' => (float) $ef->requested_amount,
                    'vat_amount'  => 0,
                    'net_amount'  => (float) $ef->requested_amount,
                    'status'      => $ef->status,
                    'color'       => 'danger',
                    'route_show'  => route('emergency-funds.show', $ef->id),
                    'route_approve' => route('emergency-funds.approve', $ef->id),
                    'route_reject'  => route('emergency-funds.reject', $ef->id),
                ];
            });
            $items = $items->concat($efs);
        }

        // Apply Status Filter if provided
        if ($request->filled('status') && $request->status !== 'all') {
            $status = strtolower($request->status);
            $items = $items->filter(function ($item) use ($status) {
                return strtolower($item->status) === $status;
            });
        }

        // Apply Project Filter if provided
        if ($request->filled('project')) {
            $projectName = strtolower($request->project);
            $items = $items->filter(function ($item) use ($projectName) {
                return strtolower($item->project) === $projectName || $projectName === 'all projects';
            });
        }

        // Sorting
        $items = $items->sortByDesc('date');

        // Manual Pagination
        $perPage = 15;
        $page = $request->input('page', 1);
        $paginatedItems = new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $projects = \App\Models\Project::all();

        return view('finance.approvals.index', compact('paginatedItems', 'projects'));
    }
}
