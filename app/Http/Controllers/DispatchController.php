<?php

namespace App\Http\Controllers;

use App\Models\WeeklyPlanDispatch;
use App\Models\Project;
use App\Models\ErpPlanHeader;
use App\Models\ErpPlanTask;
use Illuminate\Http\Request;

class DispatchController extends Controller
{
    public function index()
    {
        $query = WeeklyPlanDispatch::with(['project', 'dispatchedTo'])->latest();

        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if ($user && $user->hasRole('site_engineer') && !$user->hasAnyRole(['admin', 'global_admin', 'planning_manager', 'planning'])) {
            $assignedProjectIds = $user->projects()->pluck('projects.id');
            if ($user->store && $user->store->project_id) {
                $assignedProjectIds->push($user->store->project_id);
            }
            $query->whereIn('project_id', $assignedProjectIds->unique());
        }

        $dispatches = $query->get();
        return view('planning.dispatches.index', compact('dispatches'));
    }

    public function create()
    {
        // Show all projects that might have a plan, or at least ones not cancelled/completed
        $query = Project::whereNotIn('status', ['cancelled', 'completed']);

        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if ($user && $user->hasRole('site_engineer') && !$user->hasAnyRole(['admin', 'global_admin', 'planning_manager', 'planning'])) {
            $assignedProjectIds = $user->projects()->pluck('projects.id');
            if ($user->store && $user->store->project_id) {
                $assignedProjectIds->push($user->store->project_id);
            }
            $query->whereIn('id', $assignedProjectIds->unique());
        }

        $projects = $query->get();
        return view('planning.dispatches.create', compact('projects'));
    }

    public function getActiveTasks(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Find the approved ERP Plan for the project
        $erpPlan = ErpPlanHeader::where('project_id', $request->project_id)
            ->whereNotNull('approved_at')
            ->latest()
            ->first();

        if (!$erpPlan) {
            return response()->json(['tasks' => []]);
        }

        // Fetch tasks that overlap with the selected period
        // Task start <= period end AND task end >= period start
        $tasks = ErpPlanTask::where('plan_header_id', $erpPlan->id)
            ->whereDate('start_date', '<=', $request->end_date)
            ->whereDate('end_date', '>=', $request->start_date)
            ->where('status', '!=', 'completed')
            ->orderBy('start_date')
            ->get();

        // Format for frontend
        $formattedTasks = $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'name' => $task->name,
                'start_date' => $task->start_date ? $task->start_date->format('d M') : '-',
                'end_date' => $task->end_date ? $task->end_date->format('d M') : '-',
                // Just extracting a dummy labor type if not directly available on the task.
                // Depending on the schema, we might get this from resources later.
                'labor_type' => 'Standard',
            ];
        });

        return response()->json(['tasks' => $formattedTasks]);
    }

    public function store(Request $request)
    {
        // Business logic will be implemented later
        return redirect()->route('dispatches.index')->with('success', 'Dispatch created.');
    }

    public function show(WeeklyPlanDispatch $dispatch)
    {
        $dispatch->load(['project', 'dispatchedTo', 'tasks.scheduleTask']);
        return view('planning.dispatches.show', compact('dispatch'));
    }
}
