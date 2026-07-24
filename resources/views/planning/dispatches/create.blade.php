@extends('layouts.app')
@section('title', 'Weekly Resource Planner')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-calendar-alt me-2"></i>Weekly Resource Planner</h1>
        <a href="{{ route('dispatches.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list me-1"></i>Manage Master Lists</a>
    </div>

    {{-- Filter Card --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form id="planner-form" onsubmit="event.preventDefault(); loadTasks();">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Select Project</label>
                        <select name="project_id" id="project_id" class="form-select" required>
                            <option value="">-- Select a Project --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Period Start</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Period End <small class="text-muted fw-normal">(7 days auto)</small></label>
                        <input type="date" name="end_date" id="end_date" class="form-control" required value="{{ date('Y-m-d', strtotime('+6 days')) }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold" id="generate-btn">
                            <i class="fas fa-sync-alt me-1"></i> Generate Plan
                        </button>
                    </div>
                </div>
            </form>
            <div class="mt-2 text-muted small" id="date-summary"></div>
        </div>
    </div>

    {{-- Smart Schedule Updates --}}
    <div class="card shadow-sm mb-4 border-0" style="border-left: 4px solid #dc3545 !important;">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="mb-0 text-danger fw-bold"><i class="fas fa-pencil-alt me-2"></i>Smart Schedule Updates</h6>
        </div>
        <div class="card-body bg-light" id="smart-updates-container">
            <div class="alert alert-warning mb-2 py-2 border-0 shadow-sm text-dark"><i class="fas fa-exclamation-triangle me-2 text-warning"></i> <strong>Project Schedule Extended:</strong> Delays have pushed the new estimated project end date.</div>
            <div class="alert alert-warning mb-2 py-2 border-0 shadow-sm text-dark"><i class="fas fa-exclamation-triangle me-2 text-warning"></i> Task is overdue and was extended.</div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="d-flex justify-content-end mb-3 gap-2">
        <button type="button" class="btn btn-outline-primary fw-bold bg-white"><i class="fas fa-brain me-1"></i> AI Analyze Plan</button>
        <button type="button" class="btn btn-success fw-bold"><i class="fas fa-paper-plane me-1"></i> Save & Send Plan</button>
    </div>

    <div class="row">
        {{-- Tasks Table --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-primary fw-bold">Active Tasks for this Week</h6>
                    <span class="badge bg-primary rounded-pill" id="task-count">0 Tasks</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted">
                            <tr>
                                <th>Task ID</th>
                                <th>Task Name</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Labor Type</th>
                                <th>Resources</th>
                            </tr>
                        </thead>
                        <tbody id="tasks-tbody">
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-search fa-2x mb-2" style="opacity: 0.3;"></i>
                                    <p>Select a project and dates, then click "Generate Plan"</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Manpower Summary --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 position-relative">
                <div class="card-header bg-white border-bottom py-3 text-info fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                    Manpower Summary
                </div>
                <div class="card-body" id="manpower-summary">
                    <p class="text-muted text-center py-4">No tasks loaded.</p>
                </div>
                
                {{-- Floating action button (matching screenshot) --}}
                <button class="btn btn-primary rounded-circle position-absolute shadow" style="bottom: 20px; right: 20px; width: 45px; height: 45px;">
                    <i class="fas fa-arrow-up"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const dateSummary = document.getElementById('date-summary');

        // Auto-calculate end date (start date + 6 days)
        startDateInput.addEventListener('change', function() {
            if (this.value) {
                const date = new Date(this.value);
                date.setDate(date.getDate() + 6);
                endDateInput.value = date.toISOString().split('T')[0];
                updateDateSummary();
            }
        });

        endDateInput.addEventListener('change', updateDateSummary);

        function updateDateSummary() {
            if (startDateInput.value && endDateInput.value) {
                const start = new Date(startDateInput.value).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
                const end = new Date(endDateInput.value).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
                
                const startDay = new Date(startDateInput.value).toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short' });
                const endDay = new Date(endDateInput.value).toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short' });
                
                dateSummary.innerHTML = `Showing tasks active between <strong>${start}</strong> and <strong>${end}</strong> | ${startDay} &rarr; ${endDay}`;
            }
        }
        updateDateSummary();
    });

    function loadTasks() {
        const projectId = document.getElementById('project_id').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const btn = document.getElementById('generate-btn');

        if (!projectId || !startDate || !endDate) return;

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Loading...';

        fetch(`{{ route('dispatches.active-tasks') }}?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('tasks-tbody');
                const taskCount = document.getElementById('task-count');
                const manpower = document.getElementById('manpower-summary');
                
                tbody.innerHTML = '';
                manpower.innerHTML = '';

                if (!data.tasks || data.tasks.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted">No active ERP plan tasks found for this period.</td></tr>`;
                    taskCount.innerText = '0 Tasks';
                    manpower.innerHTML = `<p class="text-muted text-center py-4">No Manpower</p>`;
                } else {
                    taskCount.innerText = `${data.tasks.length} Task${data.tasks.length > 1 ? 's' : ''}`;
                    
                    data.tasks.forEach(task => {
                        // Render task row
                        tbody.innerHTML += `
                            <tr>
                                <td>${task.id}</td>
                                <td class="fw-bold" style="max-width: 250px; white-space: normal;">${task.name}</td>
                                <td><div style="line-height:1.2;">${task.start_date.split(' ')[0]}<br><small class="text-muted">${task.start_date.split(' ')[1] || ''}</small></div></td>
                                <td><div style="line-height:1.2;">${task.end_date.split(' ')[0]}<br><small class="text-muted">${task.end_date.split(' ')[1] || ''}</small></div></td>
                                <td class="text-muted small">${task.labor_type}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-warning text-warning fw-bold border-warning" style="background:#fff3cd;">
                                        <i class="fas fa-plus me-1"></i> Add Resources
                                    </button>
                                </td>
                            </tr>
                        `;

                        // Render dummy manpower summary
                        manpower.innerHTML += `
                            <div class="mb-3 border-bottom pb-2">
                                <h6 class="fw-bold mb-1" style="font-size:13px;"><i class="fas fa-list-ul me-2 text-muted"></i>${task.name}</h6>
                                <p class="text-muted small ms-4 mb-0">No Manpower assigned</p>
                            </div>
                        `;
                    });
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('tasks-tbody').innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger">Error loading tasks. Please try again.</td></tr>`;
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Generate Plan';
            });
    }
</script>
@endpush
