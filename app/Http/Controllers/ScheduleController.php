<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Project;
use App\Models\ScheduleTask;
use App\Models\ScheduleBaseline;
use App\Models\ErpPlanHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Schedule::class);
        
        $query = Schedule::with(['project', 'creator'])->latest();
        
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if ($user && ($user->hasRole('planning') || $user->hasRole('site_engineer')) && !$user->hasAnyRole(['planning_manager', 'admin', 'global_admin', 'coordinator', 'Coordinator'])) {
            $assignedProjectIds = $user->projects()->pluck('projects.id');
            if ($user->store && $user->store->project_id) {
                $assignedProjectIds->push($user->store->project_id);
            }
            $query->whereIn('project_id', $assignedProjectIds->unique());
        }
        
        if ($request->has('project_id') && $request->project_id != '') {
            $query->where('project_id', $request->project_id);
        }
        
        $schedules = $query->paginate(15);
        $projects = Project::where('status', '!=', 'cancelled')->get();
        
        return view('schedules.index', compact('schedules', 'projects'));
    }

    public function create()
    {
        Gate::authorize('create', Schedule::class);
        $query = Project::where('status', '!=', 'cancelled');
        
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if ($user && ($user->hasRole('planning') || $user->hasRole('site_engineer')) && !$user->hasAnyRole(['planning_manager', 'admin', 'global_admin', 'coordinator', 'Coordinator'])) {
            $assignedProjectIds = $user->projects()->pluck('projects.id');
            if ($user->store && $user->store->project_id) {
                $assignedProjectIds->push($user->store->project_id);
            }
            $query->whereIn('id', $assignedProjectIds->unique());
        }
        
        $projects = $query->get();
        return view('schedules.create', compact('projects'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Schedule::class);
        
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name'       => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'status'     => 'required|in:draft,active,delayed,completed',
            'progress'   => 'required|numeric|min:0|max:100',
        ]);
        
        $validated['created_by'] = auth()->id();
        Schedule::create($validated);
        
        return redirect()->route('schedules.index')->with('success', 'Project schedule created successfully.');
    }

    public function edit(Schedule $schedule)
    {
        Gate::authorize('update', $schedule);

        if ($schedule->sent_to_coordinator) {
            return redirect()->route('schedules.show', $schedule)
                ->with('info', 'This schedule is read-only — it has been sent to the coordinator.');
        }

        $query = Project::where('status', '!=', 'cancelled');
        
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if ($user && $user->hasRole('planning') && !$user->hasAnyRole(['planning_manager', 'admin', 'global_admin', 'coordinator', 'Coordinator'])) {
            $assignedProjectIds = $user->projects()->pluck('projects.id');
            $query->whereIn('id', $assignedProjectIds);
        }
        
        $projects = $query->get();
        return view('schedules.edit', compact('schedule', 'projects'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        Gate::authorize('update', $schedule);

        if ($schedule->sent_to_coordinator) {
            return redirect()->route('schedules.index')
                ->with('error', 'This schedule is read-only and cannot be edited.');
        }
        
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name'       => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'status'     => 'required|in:draft,active,delayed,completed',
            'progress'   => 'required|numeric|min:0|max:100',
        ]);
        
        $schedule->update($validated);
        
        return redirect()->route('schedules.index')->with('success', 'Project schedule updated successfully.');
    }

    public function destroy(Schedule $schedule)
    {
        Gate::authorize('delete', $schedule);

        // Unlink any ERP plan that references this schedule
        ErpPlanHeader::where('schedule_id', $schedule->id)
            ->update(['schedule_id' => null]);

        $schedule->delete();
        return redirect()->route('schedules.index')->with('success', 'Schedule deleted.');
    }

    public function sendToCoordinator(Schedule $schedule)
    {
        Gate::authorize('update', $schedule);

        if ($schedule->sent_to_coordinator) {
            return redirect()->route('schedules.index')
                ->with('info', 'This schedule has already been sent to the coordinator.');
        }

        $schedule->update([
            'sent_to_coordinator' => true,
            'sent_at'             => now(),
            'sent_by'             => auth()->id(),
        ]);

        return redirect()->route('schedules.index')
            ->with('success', 'Schedule "' . $schedule->name . '" has been sent to the coordinator. It is now read-only.');
    }

    public function show(Schedule $schedule)
    {
        Gate::authorize('view', $schedule);
        $allTasks = $schedule->tasks()->with(['parent', 'predecessor'])->get();
        return view('schedules.show', compact('schedule', 'allTasks'));
    }

    /* ── WBS Management ──────────────────────────────────────────── */

    public function wbs(Schedule $schedule)
    {
        Gate::authorize('view', $schedule);

        // If sent to coordinator, WBS is locked — redirect to Gantt view
        if ($schedule->sent_to_coordinator) {
            return redirect()->route('schedules.show', $schedule)
                ->with('info', 'This schedule has been sent to the coordinator and is now read-only. Only the Gantt chart is accessible.');
        }

        $tasks     = $schedule->tasks()->with(['children', 'predecessor'])->whereNull('parent_task_id')->get();
        $allTasks  = $schedule->tasks()->get();
        $baselines = $schedule->baselines;
        return view('schedules.wbs', compact('schedule', 'tasks', 'allTasks', 'baselines'));
    }

    public function storeTask(Request $request, Schedule $schedule)
    {
        Gate::authorize('update', $schedule);

        if ($schedule->sent_to_coordinator) {
            return redirect()->route('schedules.show', $schedule)
                ->with('error', 'This schedule is read-only — it has been sent to the coordinator.');
        }

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'parent_task_id' => 'nullable|exists:schedule_tasks,id',
            'status'         => 'required|string',
            'is_milestone'   => 'nullable|boolean',
            'predecessor_id' => 'nullable|exists:schedule_tasks,id',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
        ]);

        $validated['schedule_id']  = $schedule->id;
        $validated['is_milestone'] = $request->boolean('is_milestone');
        $validated['type']         = 'Normal Task';
        $validated['priority']     = 'Medium';
        $validated['planned_cost'] = 0;
        $validated['wbs_code']     = $this->generateWbsCode($schedule, $validated['parent_task_id'] ?? null);

        ScheduleTask::create($validated);

        return redirect()->route('schedules.wbs', $schedule)->with('success', 'Task added successfully.');
    }

    public function updateTask(Request $request, Schedule $schedule, ScheduleTask $task)
    {
        Gate::authorize('update', $schedule);

        if ($schedule->sent_to_coordinator) {
            return redirect()->route('schedules.show', $schedule)
                ->with('error', 'This schedule is read-only — it has been sent to the coordinator.');
        }

        $validated = $request->validate([
            'name'           => 'nullable|string|max:255',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
        ]);

        $task->update($validated);

        return redirect()->route('schedules.wbs', $schedule)->with('success', 'Task updated successfully.');
    }

    public function destroyTask(Schedule $schedule, ScheduleTask $task)
    {
        Gate::authorize('update', $schedule);

        if ($schedule->sent_to_coordinator) {
            return redirect()->route('schedules.show', $schedule)
                ->with('error', 'This schedule is read-only — it has been sent to the coordinator.');
        }

        $task->delete();
        return redirect()->route('schedules.wbs', $schedule)->with('success', 'Task deleted.');
    }

    public function storeBaseline(Request $request, Schedule $schedule)
    {
        Gate::authorize('update', $schedule);

        if ($schedule->sent_to_coordinator) {
            return redirect()->route('schedules.show', $schedule)
                ->with('error', 'This schedule is read-only — it has been sent to the coordinator.');
        }

        $request->validate([
            'version_name'          => 'required|string|max:255',
            'up_to_parent_task_id'  => 'nullable|exists:schedule_tasks,id',
        ]);

        $query = $schedule->tasks();

        if ($request->filled('up_to_parent_task_id')) {
            $parentId = $request->input('up_to_parent_task_id');
            $query->where(function ($q) use ($parentId) {
                $q->where('id', $parentId)
                  ->orWhere('parent_task_id', $parentId);
            });
        }

        $snapshot = $query->get()->map(fn($t) => [
            'id'         => $t->id,
            'wbs_code'   => $t->wbs_code,
            'name'       => $t->name,
            'status'     => $t->status,
            'start_date' => optional($t->start_date)->format('Y-m-d'),
            'end_date'   => optional($t->end_date)->format('Y-m-d'),
        ])->toArray();

        ScheduleBaseline::create([
            'schedule_id'   => $schedule->id,
            'version_name'  => $request->version_name,
            'snapshot_data' => $snapshot,
        ]);

        return redirect()->route('schedules.wbs', $schedule)->with('success', 'Baseline saved successfully.');
    }

    /* ── Helpers ─────────────────────────────────────────────────── */

    private function generateWbsCode(Schedule $schedule, ?int $parentId): string
    {
        if (!$parentId) {
            $count = ScheduleTask::where('schedule_id', $schedule->id)->whereNull('parent_task_id')->count();
            return (string)($count + 1);
        }

        $parent = ScheduleTask::find($parentId);
        $count  = ScheduleTask::where('schedule_id', $schedule->id)->where('parent_task_id', $parentId)->count();
        return $parent->wbs_code . '.' . ($count + 1);
    }
}
