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
        
        // Filter by user's store if they are restricted
        if (auth()->user()->store_id) {
            $query->where('destination_store_id', auth()->user()->store_id);
        }
        
        $requests = $query->paginate(15);
        
        return view('procurement.requests.index', compact('requests'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', MaterialRequest::class);
        
        $projects = Project::where('status', '!=', 'cancelled')->with('stores')->get();
        $stores = Store::where('is_active', true);
        
        if (auth()->user()->store_id) {
            $stores->where('id', auth()->user()->store_id);
        }
        $stores = $stores->get();

        $selectedProjectId = $request->query('project_id');
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
            'projects', 'stores', 'selectedProjectId', 'dateNeeded', 'materialName', 'quantity', 'unit', 'source', 'redirectBack'
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
        $validated['status'] = 'draft';

        // Check if source column exists in database table dynamically to avoid error before migration runs
        if (!\Illuminate\Support\Facades\Schema::hasColumn('material_requests', 'source')) {
            unset($validated['source']);
        }
        
        $materialName = $request->input('material_name');
        $quantity = $request->input('quantity');
        $unit = $request->input('unit');

        $mr = MaterialRequest::create($validated);

        if (!empty($materialName)) {
            // Find product matching material name
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
            return redirect($request->input('redirect_back'))->with('success', 'Draft request created successfully with material line.');
        }
        
        return redirect()->route('material-requests.show', $mr)->with('success', 'Draft request created.');
    }

    public function show(MaterialRequest $materialRequest)
    {
        Gate::authorize('view', $materialRequest);
        $materialRequest->load(['project', 'store', 'creator', 'items.product']);
        $products = \App\Models\Product::where('is_active', true)->get();
        
        return view('procurement.requests.show', compact('materialRequest', 'products'));
    }
    
    public function updateStatus(Request $request, MaterialRequest $materialRequest)
    {
        $status = $request->input('status');
        
        if ($status === 'submitted') {
            Gate::authorize('update', $materialRequest);
            $materialRequest->update(['status' => 'submitted']);
            return back()->with('success', 'Request submitted for approval.');
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
}
