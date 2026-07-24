<?php

namespace App\Http\Controllers;

use App\Models\Waste;
use App\Services\OperationalService;
use Illuminate\Http\Request;

class WasteController extends Controller
{
    protected OperationalService $operationalService;

    public function __construct(OperationalService $operationalService)
    {
        $this->operationalService = $operationalService;
    }

    public function index()
    {
        $wasteRecords = Waste::with(['project', 'store', 'recordedBy'])->latest()->get();
        return view('operational.waste.index', compact('wasteRecords'));
    }

    public function create()
    {
        $projects = \App\Models\Project::where('status', 'active')->get();
        $stores = \App\Models\Store::where('status', 'active')->get();
        $products = \App\Models\Product::where('status', 'active')->get();
        return view('operational.waste.create', compact('projects', 'stores', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'store_id' => 'required|exists:stores,id',
            'waste_date' => 'required|date',
            'reason' => 'required|in:damage,excess_cutting,quality_reject,theft,other',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);
        
        $wasteData = \Illuminate\Support\Arr::except($data, ['items']);
        $wasteData['recorded_by'] = auth()->id();
        $wasteData['status'] = 'reported';

        \Illuminate\Support\Facades\DB::transaction(function () use ($wasteData, $data) {
            $waste = Waste::create($wasteData);
            
            foreach ($data['items'] as $item) {
                $waste->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit' => \App\Models\Product::find($item['product_id'])->unit ?? 'pcs',
                ]);
            }
        });

        return redirect()->route('waste.index')->with('success', 'Waste record created successfully.');
    }

    public function show(Waste $waste)
    {
        $waste->load(['items.product', 'project', 'store', 'recordedBy']);
        return view('operational.waste.show', compact('waste'));
    }

    public function verify(Waste $waste)
    {
        if ($waste->status === 'reported') {
            $this->operationalService->recordWaste($waste);
        }
        return redirect()->back()->with('success', 'Waste record verified and inventory deducted.');
    }
}
