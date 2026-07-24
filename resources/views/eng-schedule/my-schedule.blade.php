@extends('layouts.app')

@section('title', 'My Work Schedule')

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-0 fw-bold" style="color:var(--brand-800)">
                <i class="fa-solid fa-calendar-check me-2 text-primary"></i>My Work Schedule
            </h1>
            <p class="text-muted small mb-0">Welcome, {{ auth()->user()->name }} — here's your schedule for today and upcoming.</p>
        </div>
        <a href="{{ route('eng-schedule.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-calendar-days me-1"></i> Full Calendar
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- Summary KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #3b82f6 !important;">
                <div class="card-body py-3">
                    <p class="text-muted small mb-1">Today's Tasks</p>
                    <h2 class="fw-bold mb-0 text-primary">{{ $today->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #10b981 !important;">
                <div class="card-body py-3">
                    <p class="text-muted small mb-1">Upcoming</p>
                    <h2 class="fw-bold mb-0 text-success">{{ $upcoming->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #ef4444 !important;">
                <div class="card-body py-3">
                    <p class="text-muted small mb-1">Overdue</p>
                    <h2 class="fw-bold mb-0 text-danger">{{ $overdue->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #6b7280 !important;">
                <div class="card-body py-3">
                    <p class="text-muted small mb-1">Completed</p>
                    <h2 class="fw-bold mb-0 text-secondary">{{ $completed->count() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">

            {{-- TODAY'S TASKS --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-semibold py-3 d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded-pill">{{ $today->count() }}</span>
                    <span><i class="fa-solid fa-sun text-warning me-1"></i>Today's Tasks</span>
                    <small class="text-muted ms-auto">{{ now()->format('D, M d Y') }}</small>
                </div>
                <div class="card-body p-0">
                    @forelse($today as $order)
                    @php $pColors = ['urgent'=>'danger','high'=>'warning','medium'=>'primary','low'=>'secondary']; @endphp
                    <div class="d-flex align-items-start gap-3 px-3 py-3 border-bottom task-row" style="border-left:4px solid {{ $order->priorityColor() }} !important;">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between flex-wrap gap-1">
                                <a href="{{ route('eng-schedule.show', $order) }}" class="fw-semibold text-decoration-none text-dark">
                                    {{ $order->title }}
                                </a>
                                <span class="badge bg-{{ $pColors[$order->priority] ?? 'secondary' }}">{{ ucfirst($order->priority) }}</span>
                            </div>
                            <p class="text-muted small mb-1">
                                <i class="fa-solid fa-clock me-1"></i>{{ $order->start_datetime->format('H:i') }} – {{ $order->end_datetime->format('H:i') }}
                                @if($order->location) · <i class="fa-solid fa-location-dot me-1"></i>{{ $order->location }} @endif
                            </p>
                            <p class="small mb-2 text-muted">{{ Str::limit($order->description, 80) }}</p>

                            {{-- Quick status update --}}
                            @if(!in_array($order->status, ['completed','cancelled','declined']))
                            <form method="POST" action="{{ route('eng-schedule.update-status', $order) }}" class="d-flex gap-2 align-items-center">
                                @csrf @method('PATCH')
                                <select name="status" class="form-select form-select-sm" style="max-width:160px;">
                                    @foreach(['accepted','in_progress','on_hold','completed'] as $s)
                                        <option value="{{ $s }}" {{ $order->status == $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                            </form>
                            @else
                            <span class="badge bg-{{ $order->status === 'completed' ? 'success' : 'secondary' }}">
                                {{ ucwords(str_replace('_',' ',$order->status)) }}
                            </span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        <i class="fa-solid fa-mug-hot fa-2x mb-2 d-block"></i>
                        No tasks scheduled for today. Enjoy your day!
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- UPCOMING --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-semibold py-3">
                    <span class="badge bg-success rounded-pill me-2">{{ $upcoming->count() }}</span>
                    <i class="fa-solid fa-forward text-success me-1"></i>Upcoming Tasks
                </div>
                <div class="card-body p-0">
                    @forelse($upcoming as $order)
                    <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                        <div class="text-center" style="min-width:52px;">
                            <p class="mb-0 fw-bold text-primary" style="font-size:1.2rem;">{{ $order->start_datetime->format('d') }}</p>
                            <p class="mb-0 text-muted small">{{ $order->start_datetime->format('M') }}</p>
                        </div>
                        <div class="flex-grow-1">
                            <a href="{{ route('eng-schedule.show', $order) }}" class="fw-semibold text-decoration-none text-dark small">{{ $order->title }}</a>
                            <p class="mb-0 text-muted" style="font-size:0.75rem;">
                                {{ $order->start_datetime->format('H:i') }} – {{ $order->end_datetime->format('H:i') }}
                                @if($order->project) · {{ $order->project->name }} @endif
                            </p>
                        </div>
                        @php $pColors = ['urgent'=>'danger','high'=>'warning','medium'=>'primary','low'=>'secondary'] @endphp
                        <span class="badge bg-{{ $pColors[$order->priority] ?? 'secondary' }}">{{ ucfirst($order->priority) }}</span>
                    </div>
                    @empty
                    <p class="text-center text-muted py-4 small">No upcoming tasks.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="col-lg-4">

            {{-- OVERDUE --}}
            @if($overdue->isNotEmpty())
            <div class="card shadow-sm border-0 mb-4 border-danger" style="border-left:4px solid #ef4444 !important;">
                <div class="card-header bg-danger bg-opacity-10 fw-semibold py-2 text-danger">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>Overdue ({{ $overdue->count() }})
                </div>
                <div class="card-body p-0">
                    @foreach($overdue as $order)
                    <div class="px-3 py-2 border-bottom">
                        <a href="{{ route('eng-schedule.show', $order) }}" class="fw-semibold text-decoration-none text-danger small d-block">{{ $order->title }}</a>
                        <p class="mb-0 text-muted" style="font-size:0.72rem;">
                            Was due: {{ $order->end_datetime->format('M d, H:i') }}
                            ({{ $order->end_datetime->diffForHumans() }})
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- RECENTLY COMPLETED --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-semibold py-2">
                    <i class="fa-solid fa-circle-check text-success me-2"></i>Recently Completed
                </div>
                <div class="card-body p-0">
                    @forelse($completed as $order)
                    <div class="px-3 py-2 border-bottom d-flex align-items-center gap-2">
                        <i class="fa-solid fa-check-circle text-success"></i>
                        <div>
                            <a href="{{ route('eng-schedule.show', $order) }}" class="small fw-semibold text-decoration-none text-dark">{{ $order->title }}</a>
                            <p class="mb-0 text-muted" style="font-size:0.72rem;">{{ $order->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted py-4 small">No completed tasks yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.task-row:hover { background-color: var(--gray-50); transition: background 0.15s; }
</style>
@endsection
