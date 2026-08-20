<?php

namespace App\Http\Controllers;

use App\Models\MaterialRequest;
use App\Models\Store;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MaterialRequestController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', MaterialRequest::class);
        
        $query = MaterialRequest::with(['project', 'store', 'creator'])->latest();

        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        if ($user && $user->hasRole('site_engineer') && !$user->hasAnyRole(['admin', 'global_admin', 'coordinator', 'store_manager', 'purchase_manager'])) {
            $assignedProjectIds = $user->projects()->pluck('projects.id');
            if ($user->employee?->project_id) {
                $assignedProjectIds->push($user->employee->project_id);
            }
            if ($user->store && $user->store->project_id) {
                $assignedProjectIds->push($user->store->project_id);
            }
            $query->where(function($q) use ($user, $assignedProjectIds) {
                $q->whereIn('project_id', $assignedProjectIds->unique())
                  ->orWhere('created_by', $user->id);
            });
        } elseif ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }
        
        $requests = $query->paginate(15);
        
        return view('procurement.requests.index', compact('requests'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', MaterialRequest::class);
        
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        $projectsQuery = Project::where('status', '!=', 'cancelled')->with('stores');
        if ($user && $user->hasRole('site_engineer') && !$user->hasAnyRole(['admin', 'global_admin', 'store_manager', 'purchase_manager'])) {
            $assignedProjectIds = $user->projects()->pluck('projects.id');
            if ($user->employee?->project_id) {
                $assignedProjectIds->push($user->employee->project_id);
            }
            if ($user->store && $user->store->project_id) {
                $assignedProjectIds->push($user->store->project_id);
            }
            $projectsQuery->whereIn('id', $assignedProjectIds->unique());
        }
        $projects = $projectsQuery->get();

        $stores = Store::where('is_active', true);
        if ($user && $user->store_id) {
            $stores->where('id', $user->store_id);
        }
        $stores = $stores->get();

        $selectedProjectId = $request->query('project_id');
        if (!$selectedProjectId && $user) {
            $selectedProjectId = $user->projects()->pluck('projects.id')->first()
                ?? $user->employee?->project_id
                ?? $user->store?->project_id;
        }
        if (!$selectedProjectId && $projects->count() >= 1) {
            $selectedProjectId = $projects->first()->id;
        }

        $selectedStoreId = $request->query('destination_store_id');
        if (!$selectedStoreId && $selectedProjectId) {
            $selectedStoreId = $stores->where('project_id', $selectedProjectId)->first()?->id
                ?? $projects->where('id', $selectedProjectId)->first()?->stores->first()?->id;
        }
        if (!$selectedStoreId && $user) {
            $selectedStoreId = $user->store_id
                ?? $user->employee?->project?->stores->first()?->id
                ?? $user->projects()->first()?->stores->first()?->id;
        }
        if (!$selectedStoreId && $stores->count() >= 1) {
            $selectedStoreId = $stores->first()->id;
        }

        $dateNeeded = $request->query('date_needed');
        $materialName = $request->query('material_name');
        $quantity = $request->query('quantity');
        $unit = $request->query('unit');
        $rawSource = $request->query('source');
        $redirectBack = $request->query('redirect_back');
        
        if ($rawSource) {
            $source = $rawSource;
        } else {
            $source = 'Manual Creation';
        }
        
        return view('procurement.requests.create', compact(
            'projects', 'stores', 'selectedProjectId', 'selectedStoreId', 'dateNeeded', 'materialName', 'quantity', 'unit', 'source', 'redirectBack'
        ));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', MaterialRequest::class);
        
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'destination_store_id' => 'required|exists:stores,id',
            'reference_number' => 'required|string|unique:material_requests',
            'source' => 'nullable|string|max:255',
            'required_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        
        $validated['source'] = $request->input('source', 'Manual Creation');
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'pending_planning';

        if (!\Illuminate\Support\Facades\Schema::hasColumn('material_requests', 'source')) {
            unset($validated['source']);
        }
        
        $materialName = $request->input('material_name');
        $quantity = $request->input('quantity');
        $unit = $request->input('unit');

        $mr = MaterialRequest::create($validated);

        if (!empty($materialName)) {
            $product = \App\Models\Product::where('name', 'like', "%{$materialName}%")->first();
            if ($product) {
                $mr->items()->create([
                    'product_id' => $product->id,
                    'quantity_requested' => $quantity ?? 1,
                    'notes' => 'Auto-added from Forecast Demand (' . ($unit ?? '') . ')'
                ]);
            }
        }

        if ($request->filled('redirect_back')) {
            return redirect($request->input('redirect_back'))->with('success', 'Material Request created and sent directly to Planning Manager.');
        }
        
        return redirect()->route('material-requests.show', $mr)->with('success', 'Material Request created and sent directly to Planning Manager.');
    }

    public function show(MaterialRequest $materialRequest)
    {
        Gate::authorize('view', $materialRequest);
        $materialRequest->load([
            'project', 'store', 'creator', 'items.product',
            'purchaseRequests.receipt.uploadedBy', 'purchaseRequests.payment'
        ]);
        $products = \App\Models\Product::where('is_active', true)->get();
        
        return view('procurement.requests.show', compact('materialRequest', 'products'));
    }
    
    public function updateStatus(Request $request, MaterialRequest $materialRequest)
    {
        $status = $request->input('status');
        
        if ($status === 'submitted' || $status === 'pending_planning') {
            Gate::authorize('update', $materialRequest);
            $materialRequest->update(['status' => 'pending_planning']);
            return back()->with('success', 'Request sent to Planning Manager for approval.');
        }
        
        if ($status === 'approved' || $status === 'rejected') {
            Gate::authorize('approve', $materialRequest);
            $materialRequest->update([
                'status' => $status,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            return back()->with('success', "Request {$status}.");
        }
        
        return back()->with('error', 'Invalid status transition.');
    }

    // ─── Emergency & Standard MR Approval by Planning Team ──────────────────
    public function planningApprove(MaterialRequest $materialRequest)
    {
        Gate::authorize('approvePlanning', $materialRequest);
        $materialRequest->update([
            'planning_approval_status' => 'approved',
            'planning_approved_by'      => auth()->id(),
            'planning_approved_at'      => now(),
            'status'                    => 'planning_approved', // Move to Coordinator
        ]);
        return back()->with('success', 'Material Request approved by Planning Team and sent to Coordinator.');
    }

    public function planningReject(Request $request, MaterialRequest $materialRequest)
    {
        Gate::authorize('approvePlanning', $materialRequest);
        $request->validate(['rejection_reason' => 'required|string']);
        $materialRequest->update([
            'planning_approval_status' => 'rejected',
            'planning_approved_by'      => auth()->id(),
            'planning_approved_at'      => now(),
            'planning_rejection_reason' => $request->rejection_reason,
            'status'                    => 'rejected',
        ]);
        return back()->with('success', 'Material Request rejected by Planning Team.');
    }

    // ─── Dispatch by Coordinator to Store Manager ────────────────────────────
    public function coordinatorDispatch(MaterialRequest $materialRequest)
    {
        Gate::authorize('dispatchCoordinator', $materialRequest);
        $materialRequest->update([
            'status' => 'sent_to_store_manager',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'Material Request sent to Store Manager.');
    }

    // ─── Store Manager Actions: Send to PR or Create Transfer ────────────────
    public function sendToPr(MaterialRequest $materialRequest)
    {
        Gate::authorize('actionStoreManager', $materialRequest);
        $materialRequest->update(['status' => 'sent_to_pr']);

        return redirect()->route('purchase-requests.create', [
            'material_request_id' => $materialRequest->id,
            'project_id' => $materialRequest->project_id,
            'store_id' => $materialRequest->destination_store_id,
        ])->with('success', 'Material Request routed to Purchase Request creation.');
    }

    public function createTransfer(MaterialRequest $materialRequest)
    {
        Gate::authorize('actionStoreManager', $materialRequest);
        $materialRequest->update(['status' => 'transfer_created']);

        return redirect()->route('transfers.create', [
            'material_request_id' => $materialRequest->id,
            'project_id' => $materialRequest->project_id,
            'destination_store_id' => $materialRequest->destination_store_id,
        ])->with('success', 'Material Request routed to Store Transfer creation.');
    }
}

