<?php

namespace App\Http\Controllers;

use App\Models\EngWorkOrder;
use App\Models\EngWorkOrderAssignee;
use App\Models\EngWorkOrderComment;
use App\Models\EngWorkOrderStatusHistory;
use App\Models\Project;
use App\Models\Schedule;
use App\Models\User;
use App\Notifications\WorkOrderAssigned;
use App\Notifications\WorkOrderStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class EngScheduleController extends Controller
{
    // ── Planning Manager: Calendar + List View ─────────────────────────────────

    public function index(Request $request)
    {
        Gate::authorize('viewAny', EngWorkOrder::class);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Planners see all; engineers are redirected to their own view
        if ($user->hasAnyRole(['site_engineer', 'foreman']) && !$user->hasAnyRole(['planning_manager', 'planning', 'admin', 'global_admin', 'technical_manager'])) {
            return redirect()->route('eng-schedule.my');
        }

        $engineers = User::role(['site_engineer', 'foreman', 'planning'])->orderBy('name')->get();
        $projects  = Project::where('status', 'active')->orderBy('name')->get();
        $schedules = Schedule::with('project')->latest()->take(50)->get();

        // Filters for list view
        $query = EngWorkOrder::with(['project', 'assignedBy', 'engineers'])
                             ->latest('start_datetime');

        if ($request->engineer_id) {
            $query->whereHas('engineers', fn($q) => $q->where('users.id', $request->engineer_id));
        }
        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->priority) {
            $query->where('priority', $request->priority);
        }
        if ($request->date_from) {
            $query->whereDate('start_datetime', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('start_datetime', '<=', $request->date_to);
        }

        $workOrders = $query->paginate(20)->withQueryString();

        return view('eng-schedule.index', compact('engineers', 'projects', 'schedules', 'workOrders'));
    }

    // ── FullCalendar JSON Feed ──────────────────────────────────────────────────

    public function calendarFeed(Request $request)
    {
        Gate::authorize('viewAny', EngWorkOrder::class);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        $query = EngWorkOrder::with(['engineers', 'project'])
                             ->whereNotIn('status', ['cancelled', 'draft']);

        // Engineers only see their own
        if ($user->hasAnyRole(['site_engineer', 'foreman']) && !$user->hasAnyRole(['planning_manager', 'planning', 'admin', 'global_admin'])) {
            $query->forEngineer($user->id);
        }

        if ($request->engineer_id) {
            $query->whereHas('engineers', fn($q) => $q->where('users.id', $request->engineer_id));
        }

        $start = $request->start ?? now()->startOfMonth();
        $end   = $request->end   ?? now()->endOfMonth();
        $query->whereBetween('start_datetime', [$start, $end]);

        $events = $query->get()->flatMap(function ($order) {
            return $order->engineers->map(function ($engineer) use ($order) {
                return [
                    'id'            => $order->id,
                    'title'         => $order->title,
                    'start'         => $order->start_datetime->toIso8601String(),
                    'end'           => $order->end_datetime->toIso8601String(),
                    'color'         => $order->priorityColor(),
                    'resourceId'    => $engineer->id,
                    'extendedProps' => [
                        'status'        => $order->status,
                        'priority'      => $order->priority,
                        'project'       => $order->project->name ?? '',
                        'location'      => $order->location,
                        'engineer_name' => $engineer->name,
                        'engineer_status' => $engineer->pivot->status ?? '',
                    ],
                    'url' => route('eng-schedule.show', $order->id),
                ];
            });
        });

        return response()->json($events);
    }

    // ── Engineer Resources for FullCalendar ────────────────────────────────────

    public function engineerResources()
    {
        $engineers = User::role(['site_engineer', 'foreman', 'planning'])
                         ->orderBy('name')
                         ->get(['id', 'name'])
                         ->map(fn($e) => ['id' => $e->id, 'title' => $e->name]);

        return response()->json($engineers);
    }

    // ── Create Work Order Form ─────────────────────────────────────────────────

    public function create(Request $request)
    {
        Gate::authorize('create', EngWorkOrder::class);

        $engineers = User::role(['site_engineer', 'foreman'])->orderBy('name')->get();
        $projects  = Project::where('status', 'active')->orderBy('name')->get();
        $schedules = Schedule::with('project')->latest()->take(100)->get();

        // Pre-fill from calendar slot click
        $prefill = [
            'engineer_id'    => $request->engineer_id,
            'start_datetime' => $request->start,
            'end_datetime'   => $request->end,
        ];

        return view('eng-schedule.create', compact('engineers', 'projects', 'schedules', 'prefill'));
    }

    // ── Store New Work Order ───────────────────────────────────────────────────

    public function store(Request $request)
    {
        Gate::authorize('create', EngWorkOrder::class);

        $validated = $request->validate([
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string',
            'project_id'           => 'required|exists:projects,id',
            'schedule_id'          => 'nullable|exists:schedules,id',
            'start_datetime'       => 'required|date',
            'end_datetime'         => 'required|date|after:start_datetime',
            'priority'             => 'required|in:low,medium,high,urgent',
            'location'             => 'nullable|string|max:255',
            'category'             => 'nullable|string|max:100',
            'notes'                => 'nullable|string',
            'engineer_ids'         => 'required|array|min:1',
            'engineer_ids.*'       => 'exists:users,id',
            'recurrence_type'      => 'nullable|in:none,daily,weekly,monthly',
            'recurrence_interval'  => 'nullable|integer|min:1|max:30',
            'recurrence_end_date'  => 'nullable|date|after:start_datetime',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $order = EngWorkOrder::create([
                ...$validated,
                'assigned_by'         => auth()->id(),
                'status'              => 'assigned',
                'recurrence_type'     => $validated['recurrence_type']     ?? 'none',
                'recurrence_interval' => $validated['recurrence_interval'] ?? 1,
                'recurrence_end_date' => $validated['recurrence_end_date'] ?? null,
            ]);

            // Attach engineers
            foreach ($validated['engineer_ids'] as $engineerId) {
                EngWorkOrderAssignee::create([
                    'work_order_id' => $order->id,
                    'user_id'       => $engineerId,
                    'status'        => 'pending',
                ]);
            }

            // Log status history
            EngWorkOrderStatusHistory::create([
                'work_order_id' => $order->id,
                'changed_by'    => auth()->id(),
                'from_status'   => null,
                'to_status'     => 'assigned',
                'notes'         => 'Work order created and assigned.',
            ]);

            // Notify each assigned engineer
            $engineers = User::whereIn('id', $validated['engineer_ids'])->get();
            foreach ($engineers as $engineer) {
                try {
                    $engineer->notify(new WorkOrderAssigned($order));
                } catch (\Exception $e) {
                    // Notification failure should not block creation
                }
            }
        });

        return redirect()->route('eng-schedule.index')->with('success', 'Work order created and engineers notified!');
    }

    // ── Show Work Order Detail ─────────────────────────────────────────────────

    public function show(EngWorkOrder $engSchedule)
    {
        Gate::authorize('view', $engSchedule);

        $engSchedule->load(['project', 'schedule', 'assignedBy', 'engineers', 'comments.user', 'statusHistory.changedBy']);

        return view('eng-schedule.show', compact('engSchedule'));
    }

    // ── Edit Work Order ────────────────────────────────────────────────────────

    public function edit(EngWorkOrder $engSchedule)
    {
        Gate::authorize('update', $engSchedule);

        $engineers = User::role(['site_engineer', 'foreman'])->orderBy('name')->get();
        $projects  = Project::where('status', 'active')->orderBy('name')->get();
        $schedules = Schedule::with('project')->latest()->take(100)->get();
        $assigned  = $engSchedule->engineers->pluck('id')->toArray();

        return view('eng-schedule.edit', compact('engSchedule', 'engineers', 'projects', 'schedules', 'assigned'));
    }

    // ── Update Work Order ──────────────────────────────────────────────────────

    public function update(Request $request, EngWorkOrder $engSchedule)
    {
        Gate::authorize('update', $engSchedule);

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'project_id'     => 'required|exists:projects,id',
            'schedule_id'    => 'nullable|exists:schedules,id',
            'start_datetime' => 'required|date',
            'end_datetime'   => 'required|date|after:start_datetime',
            'priority'       => 'required|in:low,medium,high,urgent',
            'location'       => 'nullable|string|max:255',
            'category'       => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
            'engineer_ids'   => 'required|array|min:1',
            'engineer_ids.*' => 'exists:users,id',
        ]);

        DB::transaction(function () use ($validated, $engSchedule) {
            $engSchedule->update($validated);

            // Sync engineers — add new, remove removed
            $current  = $engSchedule->assignees->pluck('user_id')->toArray();
            $new      = $validated['engineer_ids'];
            $toAdd    = array_diff($new, $current);
            $toRemove = array_diff($current, $new);

            foreach ($toAdd as $userId) {
                EngWorkOrderAssignee::create([
                    'work_order_id' => $engSchedule->id,
                    'user_id'       => $userId,
                    'status'        => 'pending',
                ]);
                try {
                    User::find($userId)?->notify(new WorkOrderAssigned($engSchedule));
                } catch (\Exception $e) {}
            }

            EngWorkOrderAssignee::where('work_order_id', $engSchedule->id)
                                ->whereIn('user_id', $toRemove)
                                ->delete();
        });

        return redirect()->route('eng-schedule.show', $engSchedule)->with('success', 'Work order updated.');
    }

    // ── Update Status (Engineer or Planner) ───────────────────────────────────

    public function updateStatus(Request $request, EngWorkOrder $engSchedule)
    {
        Gate::authorize('updateStatus', $engSchedule);

        $request->validate([
            'status'         => 'required|in:accepted,declined,in_progress,on_hold,completed,cancelled,assigned',
            'notes'          => 'nullable|string|max:500',
            'decline_reason' => 'nullable|string|max:500',
            'actual_hours'   => 'nullable|numeric|min:0',
        ]);

        $oldStatus = $engSchedule->status;
        $newStatus = $request->status;

        DB::transaction(function () use ($request, $engSchedule, $oldStatus, $newStatus) {
            // Update overall order status
            $engSchedule->update(['status' => $newStatus]);

            // Update per-engineer assignee row
            $assignee = EngWorkOrderAssignee::where('work_order_id', $engSchedule->id)
                                            ->where('user_id', auth()->id())
                                            ->first();
            if ($assignee) {
                $assignee->update([
                    'status'         => $newStatus,
                    'decline_reason' => $request->decline_reason,
                    'actual_hours'   => $request->actual_hours,
                    'accepted_at'    => in_array($newStatus, ['accepted']) ? now() : $assignee->accepted_at,
                    'completed_at'   => $newStatus === 'completed' ? now() : $assignee->completed_at,
                ]);
            }

            // Log history
            EngWorkOrderStatusHistory::create([
                'work_order_id' => $engSchedule->id,
                'changed_by'    => auth()->id(),
                'from_status'   => $oldStatus,
                'to_status'     => $newStatus,
                'notes'         => $request->notes ?? $request->decline_reason,
            ]);

            // Notify planner on important changes
            if (in_array($newStatus, ['declined', 'completed', 'on_hold'])) {
                try {
                    $engSchedule->assignedBy->notify(new WorkOrderStatusChanged($engSchedule, $newStatus, auth()->user()));
                } catch (\Exception $e) {}
            }
        });

        return back()->with('success', 'Status updated to ' . ucwords(str_replace('_', ' ', $newStatus)) . '.');
    }

    // ── Add Comment ────────────────────────────────────────────────────────────

    public function addComment(Request $request, EngWorkOrder $engSchedule)
    {
        Gate::authorize('comment', $engSchedule);

        $request->validate(['body' => 'required|string|max:2000']);

        EngWorkOrderComment::create([
            'work_order_id' => $engSchedule->id,
            'user_id'       => auth()->id(),
            'body'          => $request->body,
        ]);

        return back()->with('success', 'Comment added.');
    }

    // ── Engineer's Personal Schedule View ─────────────────────────────────────

    public function mySchedule()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $base = EngWorkOrder::forEngineer($user->id)->with(['project', 'assignedBy']);

        $today     = (clone $base)->today()->get();
        $upcoming  = (clone $base)->upcoming()->where('start_datetime', '>', now())->take(10)->get();
        $overdue   = (clone $base)->overdue()->take(10)->get();
        $completed = (clone $base)->where('status', 'completed')->latest('updated_at')->take(10)->get();

        return view('eng-schedule.my-schedule', compact('today', 'upcoming', 'overdue', 'completed'));
    }

    // ── Conflict Check (AJAX) ─────────────────────────────────────────────────

    public function conflictCheck(Request $request)
    {
        $request->validate([
            'engineer_ids'   => 'required|array',
            'start_datetime' => 'required|date',
            'end_datetime'   => 'required|date',
            'exclude_id'     => 'nullable|integer',
        ]);

        $conflicts = EngWorkOrder::whereHas('engineers', fn($q) =>
                         $q->whereIn('users.id', $request->engineer_ids))
                     ->where(function ($q) use ($request) {
                         $q->whereBetween('start_datetime', [$request->start_datetime, $request->end_datetime])
                           ->orWhereBetween('end_datetime', [$request->start_datetime, $request->end_datetime])
                           ->orWhere(function ($q2) use ($request) {
                               $q2->where('start_datetime', '<=', $request->start_datetime)
                                  ->where('end_datetime', '>=', $request->end_datetime);
                           });
                     })
                     ->whereNotIn('status', ['cancelled', 'declined'])
                     ->when($request->exclude_id, fn($q) => $q->where('id', '!=', $request->exclude_id))
                     ->with(['engineers', 'project'])
                     ->get();

        return response()->json([
            'has_conflicts' => $conflicts->isNotEmpty(),
            'conflicts'     => $conflicts->map(fn($c) => [
                'id'             => $c->id,
                'title'          => $c->title,
                'start_datetime' => $c->start_datetime->format('M d, Y H:i'),
                'end_datetime'   => $c->end_datetime->format('M d, Y H:i'),
                'engineers'      => $c->engineers->pluck('name')->join(', '),
            ]),
        ]);
    }

    // ── Drag & Drop Reschedule ─────────────────────────────────────────────────

    public function reschedule(Request $request, EngWorkOrder $engSchedule)
    {
        Gate::authorize('update', $engSchedule);

        $request->validate([
            'start_datetime' => 'required|date',
            'end_datetime'   => 'required|date|after:start_datetime',
        ]);

        $engSchedule->update([
            'start_datetime' => $request->start_datetime,
            'end_datetime'   => $request->end_datetime,
        ]);

        return response()->json(['success' => true]);
    }

    // ── Cancel/Delete ──────────────────────────────────────────────────────────

    public function destroy(EngWorkOrder $engSchedule)
    {
        Gate::authorize('delete', $engSchedule);

        $engSchedule->update(['status' => 'cancelled']);
        $engSchedule->delete();

        return redirect()->route('eng-schedule.index')->with('success', 'Work order cancelled.');
    }
}
