@php
    $levelClass  = $level > 0 ? 'level-' . min($level, 2) : '';
    $statusClass = strtolower(str_replace([' ', '_'], '-', $task->status));
    $priorityCls = ucfirst(strtolower($task->priority));
@endphp

<div class="task-row {{ $levelClass }}">

    {{-- Indent indicator for child tasks --}}
    @if($level > 0)
        <i class="fa-solid fa-turn-down-right" style="color:var(--gray-300);font-size:.8rem;flex-shrink:0;"></i>
    @endif

    {{-- WBS Code --}}
    <span class="wbs-code">{{ $task->wbs_code }}</span>

    {{-- Task Name + Milestone tag --}}
    <div class="task-name">
        {{ $task->name }}
        @if($task->is_milestone)
            <span class="milestone-dot">
                <i class="fa-solid fa-diamond" style="font-size:.6rem;"></i> Milestone
            </span>
        @endif
    </div>

    {{-- Type --}}
    <span class="task-type-tag d-none d-md-inline">{{ $task->type }}</span>

    {{-- Priority --}}
    <span class="badge-priority {{ $priorityCls }}">{{ $task->priority }}</span>

    {{-- Status --}}
    <span class="badge-status {{ $statusClass }}">{{ str_replace('_', ' ', $task->status) }}</span>

    {{-- Dates --}}
    @if($task->start_date || $task->end_date)
        <span class="task-date-tag d-none d-lg-inline">
            <i class="fa-regular fa-calendar me-1" style="color:var(--gray-300);"></i>
            {{ optional($task->start_date)->format('d M') }} → {{ optional($task->end_date)->format('d M Y') }}
        </span>
    @endif

    {{-- Predecessor --}}
    @if($task->predecessor)
        <span class="pred-badge d-none d-xl-inline">
            <i class="fa-solid fa-link" style="color:var(--gray-300);"></i>
            {{ $task->predecessor->wbs_code }}
        </span>
    @endif

    <div class="ms-auto flex-shrink-0 d-flex gap-1">
        <button type="button" class="btn btn-sm btn-outline-primary" style="padding:4px 9px;" 
                data-bs-toggle="modal" 
                data-bs-target="#editTaskDatesModal" 
                data-action="{{ route('schedules.tasks.update', [$schedule, $task]) }}" 
                data-start="{{ optional($task->start_date)->format('Y-m-d') }}" 
                data-end="{{ optional($task->end_date)->format('Y-m-d') }}" 
                data-taskname="{{ $task->wbs_code }} - {{ $task->name }}"
                data-rawname="{{ $task->name }}"
                onclick="setupEditTaskDatesModal(this)">
            <i class="fa-solid fa-pen" style="font-size:.7rem;"></i>
        </button>
        <form method="POST" action="{{ route('schedules.tasks.destroy', [$schedule, $task]) }}"
              onsubmit="return confirm('Delete task \'{{ addslashes($task->name) }}\'?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger" style="padding:4px 9px;">
                <i class="fa-solid fa-trash" style="font-size:.7rem;"></i>
            </button>
        </form>
    </div>
</div>

{{-- Recursive children --}}
@if($task->children && $task->children->isNotEmpty())
    @foreach($task->children as $child)
        @include('schedules._wbs_task_row', ['task' => $child, 'level' => $level + 1, 'schedule' => $schedule])
    @endforeach
@endif
