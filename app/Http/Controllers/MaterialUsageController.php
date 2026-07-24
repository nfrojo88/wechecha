<?php

namespace App\Http\Controllers;

use App\Models\MaterialUsage;
use App\Services\OperationalService;
use Illuminate\Http\Request;

class MaterialUsageController extends Controller
{
    protected OperationalService $operationalService;

    public function __construct(OperationalService $operationalService)
    {
        $this->operationalService = $operationalService;
    }

    public function index()
    {
        $usages = MaterialUsage::with(['project', 'store', 'createdBy'])->latest()->get();
        return view('operational.material-usages.index', compact('usages'));
    }

    public function create()
    {
        $projects = \App\Models\Project::where('status', 'active')->get();
        $stores = \App\Models\Store::where('status', 'active')->get();
        $products = \App\Models\Product::where('status', 'active')->get();
        return view('operational.material-usages.create', compact('projects', 'stores', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'usage_no' => 'required|string|unique:material_usages',
            'project_id' => 'required|exists:projects,id',
            'store_id' => 'required|exists:stores,id',
            'usage_date' => 'required|date',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);
        
        $usageData = \Illuminate\Support\Arr::except($data, ['items']);
        $usageData['created_by'] = auth()->id();
        $usageData['status'] = 'draft';

        \Illuminate\Support\Facades\DB::transaction(function () use ($usageData, $data) {
            $usage = MaterialUsage::create($usageData);
            
            foreach ($data['items'] as $item) {
                $usage->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit' => \App\Models\Product::find($item['product_id'])->unit ?? 'pcs',
                ]);
            }
        });

        return redirect()->route('material-usages.index')->with('success', 'Material Usage logged successfully.');
    }

    public function show(MaterialUsage $materialUsage)
    {
        $materialUsage->load(['items.product', 'project', 'store', 'task', 'createdBy']);
        return view('operational.material-usages.show', compact('materialUsage'));
    }

    public function confirm(MaterialUsage $materialUsage)
    {
        if ($materialUsage->status === 'draft') {
            $this->operationalService->recordMaterialUsage($materialUsage);
        }
        return redirect()->back()->with('success', 'Usage confirmed and inventory updated.');
    }
}
