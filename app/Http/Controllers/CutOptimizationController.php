<?php

namespace App\Http\Controllers;

use App\Models\CutOptimization;
use Illuminate\Http\Request;

class CutOptimizationController extends Controller
{
    public function index()
    {
        $optimizations = CutOptimization::with(['project', 'createdBy'])->latest()->get();
        return view('operational.cut-optimizations.index', compact('optimizations'));
    }

    public function create()
    {
        return view('operational.cut-optimizations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'material_type' => 'required|string|max:50',
            'standard_length' => 'required|string|max:20',
        ]);
        $data['created_by'] = auth()->id();
        $optimization = CutOptimization::create($data);
        return redirect()->route('cut-optimizations.index')->with('success', 'Cut Optimization created successfully.');
    }

    public function show(CutOptimization $cutOptimization)
    {
        $cutOptimization->load(['items.product', 'project', 'createdBy']);
        return view('operational.cut-optimizations.show', compact('cutOptimization'));
    }
}
