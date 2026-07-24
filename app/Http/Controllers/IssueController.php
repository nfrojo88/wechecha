<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function index()
    {
        $issues = Issue::with(['project', 'reportedBy', 'assignedTo'])->latest()->get();
        return view('operational.issues.index', compact('issues'));
    }

    public function create()
    {
        $projects = \App\Models\Project::where('status', 'active')->get();
        return view('operational.issues.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'category' => 'required|in:safety,quality,schedule,material,equipment,other',
        ]);
        $data['reported_by'] = auth()->id();
        $issue = Issue::create($data);
        return redirect()->route('issues.index')->with('success', 'Issue reported successfully.');
    }

    public function show(Issue $issue)
    {
        $issue->load(['comments.user', 'project', 'reportedBy', 'assignedTo', 'task']);
        return view('operational.issues.show', compact('issue'));
    }
}
