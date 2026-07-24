<?php

namespace App\Http\Controllers;

use App\Models\MaterialPlan;
use Illuminate\Http\Request;

class MaterialPlanController extends Controller
{
    public function index()
    {
        $plans = MaterialPlan::with(['project', 'createdBy'])->latest()->get();
        return view('operational.material-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('operational.material-plans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'plan_week_start' => 'required|date',
            'plan_week_end' => 'required|date|after_or_equal:plan_week_start',
        ]);
        $data['created_by'] = auth()->id();
        $plan = MaterialPlan::create($data);
        return redirect()->route('material-plans.index')->with('success', 'Material Plan created successfully.');
    }

    public function show(MaterialPlan $materialPlan)
    {
        $materialPlan->load(['items.product', 'items.task', 'items.store', 'project', 'createdBy']);
        return view('operational.material-plans.show', compact('materialPlan'));
    }
}
