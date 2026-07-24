<?php

namespace App\Http\Controllers;

use App\Models\MaterialDamageReport;
use App\Models\Project;
use App\Models\Product;
use Illuminate\Http\Request;

class MaterialDamageReportController extends Controller
{
    public function index()
    {
        $reports = MaterialDamageReport::with(['project', 'product', 'reporter'])->latest()->get();
        return view('material-damage-reports.index', compact('reports'));
    }

    public function create()
    {
        $projects = Project::where('status', 'active')->get();
        $products = Product::where('status', 'active')->get();
        return view('material-damage-reports.create', compact('projects', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'damage_reason' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['reported_by'] = auth()->id();
        $validated['status'] = 'pending';

        MaterialDamageReport::create($validated);

        return redirect()->route('material-damage-reports.index')->with('success', 'Damage report submitted successfully.');
    }

    public function show(MaterialDamageReport $materialDamageReport)
    {
        $materialDamageReport->load(['project', 'product', 'reporter']);
        return view('material-damage-reports.show', compact('materialDamageReport'));
    }
}
