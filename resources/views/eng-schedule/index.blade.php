@extends('layouts.app')

@section('title', 'Engineer Work Schedule')

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-0 fw-bold" style="color:var(--brand-800)">
                <i class="fa-solid fa-calendar-days me-2 text-primary"></i>Engineer Work Schedule
            </h1>
            <p class="text-muted small mb-0">Assign, manage, and track field engineer work orders</p>
        </div>
        @can('create', App\Models\EngWorkOrder::class)
        <a href="{{ route('eng-schedule.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> New Work Order
        </a>
        @endcan
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- View Toggle --}}
    <div class="card shadow-sm mb-3 border-0">
        <div class="card-body py-2 px-3 d-flex align-items-center gap-3 flex-wrap">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-primary active" id="btn-calendar" onclick="showView('calendar')">
                    <i class="fa-solid fa-calendar-week me-1"></i> Calendar
                </button>
                <button type="button" class="btn btn-outline-primary" id="btn-list" onclick="showView('list')">
                    <i class="fa-solid fa-list me-1"></i> List
                </button>
            </div>

            {{-- Quick filters --}}
            <div class="d-flex gap-2 flex-wrap ms-auto">
                <select id="filter-engineer" class="form-select form-select-sm" style="min-width:150px;" onchange="applyCalendarFilter()">
                    <option value="">All Engineers</option>
                    @foreach($engineers as $eng)
                        <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                    @endforeach
                </select>
                <select id="filter-view" class="form-select form-select-sm" onchange="changeCalendarView(this.value)">
                    <option value="resourceTimelineDay">Day</option>
                    <option value="resourceTimelineWeek" selected>Week</option>
                    <option value="resourceTimelineMonth">Month</option>
                </select>
            </div>
        </div>
    </div>

    {{-- ── CALENDAR VIEW ────────────────────────────────────────────────── --}}
    <div id="view-calendar">
        <div class="card shadow-sm border-0">
            <div class="card-body p-2">
                <div id="eng-calendar" style="min-height:560px;"></div>
            </div>
        </div>
        {{-- Legend --}}
        <div class="d-flex gap-3 mt-2 flex-wrap small text-muted px-1">
            <span><span class="badge" style="background:#ef4444">●</span> Urgent</span>
            <span><span class="badge" style="background:#f97316">●</span> High</span>
            <span><span class="badge" style="background:#3b82f6">●</span> Medium</span>
            <span><span class="badge" style="background:#6b7280">●</span> Low</span>
        </div>
    </div>

    {{-- ── LIST VIEW ───────────────────────────────────────────────────── --}}
    <div id="view-list" style="display:none">

        {{-- Filters --}}
        <form method="GET" class="card shadow-sm border-0 mb-3">
            <div class="card-body py-2 px-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <select name="engineer_id" class="form-select form-select-sm">
                            <option value="">All Engineers</option>
                            @foreach($engineers as $eng)
                                <option value="{{ $eng->id }}" {{ request('engineer_id') == $eng->id ? 'selected' : '' }}>{{ $eng->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="project_id" class="form-select form-select-sm">
                            <option value="">All Projects</option>
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}" {{ request('project_id') == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            @foreach(['draft','assigned','accepted','declined','in_progress','on_hold','completed','cancelled'] as $s)
                                <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="priority" class="form-select form-select-sm">
                            <option value="">All Priority</option>
                            @foreach(['low','medium','high','urgent'] as $p)
                                <option value="{{ $p }}" {{ request('priority') == $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" placeholder="From">
                    </div>
                    <div class="col-md-1">
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" placeholder="To">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Title</th>
                                <th>Project</th>
                                <th>Assigned To</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($workOrders as $order)
                            <tr>
                                <td class="ps-3 fw-semibold">
                                    <a href="{{ route('eng-schedule.show', $order) }}" class="text-decoration-none text-dark">
                                        {{ $order->title }}
                                    </a>
                                    @if($order->location)
                                        <br><small class="text-muted"><i class="fa-solid fa-location-dot me-1"></i>{{ $order->location }}</small>
                                    @endif
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $order->project->name ?? '-' }}</span></td>
                                <td>
                                    @foreach($order->engineers->take(3) as $eng)
                                        <span class="badge bg-secondary me-1">{{ Str::words($eng->name, 1, '') }}</span>
                                    @endforeach
                                    @if($order->engineers->count() > 3)
                                        <span class="text-muted small">+{{ $order->engineers->count() - 3 }}</span>
                                    @endif
                                </td>
                                <td>{{ $order->start_datetime->format('M d, H:i') }}</td>
                                <td>{{ $order->end_datetime->format('M d, H:i') }}</td>
                                <td>
                                    @php $pColors = ['urgent'=>'danger','high'=>'warning','medium'=>'primary','low'=>'secondary'] @endphp
                                    <span class="badge bg-{{ $pColors[$order->priority] ?? 'secondary' }}">{{ ucfirst($order->priority) }}</span>
                                </td>
                                <td>
                                    @php $sColors = ['completed'=>'success','in_progress'=>'primary','accepted'=>'info','declined'=>'danger','on_hold'=>'warning','cancelled'=>'secondary','assigned'=>'purple','draft'=>'light'] @endphp
                                    <span class="badge bg-{{ $sColors[$order->status] ?? 'secondary' }} text-{{ in_array($order->status,['draft','on_hold']) ? 'dark' : 'white' }}">
                                        {{ ucwords(str_replace('_',' ',$order->status)) }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('eng-schedule.show', $order) }}" class="btn btn-sm btn-outline-secondary" title="View"><i class="fa-solid fa-eye"></i></a>
                                    @can('update', $order)
                                    <a href="{{ route('eng-schedule.edit', $order) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted py-5"><i class="fa-solid fa-calendar-xmark fa-2x d-block mb-2"></i>No work orders found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($workOrders->hasPages())
                <div class="px-3 py-2">{{ $workOrders->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- FullCalendar --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/resource@6.1.15/index.global.min.js"></script>

<style>
.fc-event { cursor: pointer; border-radius: 4px !important; font-size: 0.78rem; }
.fc-resource-timeline .fc-datagrid-cell-frame { min-height: 48px; }
.fc-toolbar-title { font-size: 1rem !important; font-weight: 600; }
</style>

<script>
let calendar;
let currentFilterEngineer = '';

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('eng-calendar');

    calendar = new FullCalendar.Calendar(calendarEl, {
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        initialView: 'resourceTimelineWeek',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  ''
        },
        resourceAreaHeaderContent: 'Engineers',
        resourceAreaWidth: '180px',
        resources: '{{ route("eng-schedule.engineer-resources") }}',
        events: function(info, successCallback, failureCallback) {
            let url = '{{ route("eng-schedule.calendar-feed") }}?start=' + info.startStr + '&end=' + info.endStr;
            if (currentFilterEngineer) url += '&engineer_id=' + currentFilterEngineer;
            fetch(url)
                .then(r => r.json())
                .then(data => successCallback(data))
                .catch(() => failureCallback());
        },
        editable: {{ auth()->user()->hasAnyRole(['planning_manager','planning','admin','global_admin']) ? 'true' : 'false' }},
        eventDrop: function(info) {
            fetch('{{ url("eng-schedule") }}/' + info.event.id + '/reschedule', {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ start_datetime: info.event.startStr, end_datetime: info.event.endStr })
            }).then(r => {
                if (!r.ok) { info.revert(); alert('Failed to reschedule.'); }
            });
        },
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            if (info.event.url) window.location.href = info.event.url;
        },
        eventDidMount: function(info) {
            const p = info.event.extendedProps;
            const tip = `${info.event.title}\n📍 ${p.location || 'N/A'}\n👷 ${p.engineer_name}\n🔖 ${p.status}`;
            info.el.setAttribute('title', tip);
        },
        slotMinTime: '06:00:00',
        slotMaxTime: '22:00:00',
        height: 'auto',
        nowIndicator: true,
    });

    calendar.render();
});

function showView(view) {
    document.getElementById('view-calendar').style.display = view === 'calendar' ? '' : 'none';
    document.getElementById('view-list').style.display = view === 'list' ? '' : 'none';
    document.getElementById('btn-calendar').classList.toggle('active', view === 'calendar');
    document.getElementById('btn-list').classList.toggle('active', view === 'list');
}

function applyCalendarFilter() {
    currentFilterEngineer = document.getElementById('filter-engineer').value;
    if (calendar) calendar.refetchEvents();
}

function changeCalendarView(viewName) {
    if (calendar) calendar.changeView(viewName);
}
</script>
@endsection
