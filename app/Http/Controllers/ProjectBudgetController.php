<?php

namespace App\Http\Controllers;

use App\Models\ProjectBudget;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectBudgetController extends Controller
{
    public function index()
    {
        $budgets = ProjectBudget::with('project')->orderBy('project_id')->paginate(30);
        return view('finance.budgets.index', compact('budgets'));
    }

    public function create()
    {
        $projects = Project::where('status', 'active')->get();
        return view('finance.budgets.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'      => 'required|exists:projects,id',
            'category'        => 'required|string|max:50',
            'budgeted_amount' => 'required|numeric|min:0',
            'period_type'     => 'required|in:monthly,quarterly,yearly,total',
            'period_start'    => 'nullable|date',
            'period_end'      => 'nullable|date|after_or_equal:period_start',
        ]);

        ProjectBudget::create($data);
        return redirect()->route('budgets.index')->with('success', 'Project Budget added.');
    }

    public function edit(ProjectBudget $budget)
    {
        $projects = Project::where('status', 'active')->get();
        return view('finance.budgets.edit', compact('budget', 'projects'));
    }

    public function update(Request $request, ProjectBudget $budget)
    {
        $data = $request->validate([
            'budgeted_amount' => 'required|numeric|min:0',
        ]);

        $budget->update($data);
        return redirect()->route('budgets.index')->with('success', 'Project Budget updated.');
    }
}
