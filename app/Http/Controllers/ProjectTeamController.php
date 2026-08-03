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

        // Fetch users eligible to be assigned — try role filter, fallback to all users
        try {
            $planners = User::role([
                'planning',
                'planning_manager',
                'technical_manager',
                'site_engineer',
                'coordinator',
            ])->with('employee')->get();

            // If empty, fall back to all users
            if ($planners->isEmpty()) {
                $planners = User::with('employee')->orderBy('name')->get();
            }
        } catch (\Throwable $e) {
            $planners = User::with('employee')->orderBy('name')->get();
        }

        return view('planning.team-assignment', compact('projects', 'planners'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'user_ids'   => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        try {
            $project->team()->sync($request->input('user_ids', []));
            return redirect()->back()->with('success', 'Team assigned to project "' . $project->name . '" successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['team' => 'Failed to assign team: ' . $e->getMessage()]);
        }
    }
}
