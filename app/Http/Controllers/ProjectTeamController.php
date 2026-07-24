<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectTeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $projects = Project::with('team')->get();
        // Fetch users who are planners
        $planners = User::role(['planning', 'planning_manager', 'technical_manager'])->get();

        return view('planning.team-assignment', compact('projects', 'planners'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $project->team()->sync($request->input('user_ids', []));

        return redirect()->back()->with('success', 'Team assigned to project successfully.');
    }
}
