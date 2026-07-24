@extends('layouts.app')
@section('title', 'Weekly Manpower Report')
@section('content')

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-chart-bar me-2 text-primary"></i>Weekly Manpower Report
            </h1>
            <p class="text-muted mt-1">Week #{{ $weekNumber }} - {{ Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('weekly-manpower.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
               class="btn btn-outline-secondary">
                <i class="fas fa-download me-1"></i>Export CSV
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendGMModal">
                <i class="fas fa-envelope me-1"></i>Send to GM
            </button>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('weekly-manpower.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-select">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}" @selected(request('project_id')==$project->id)>
                            {{ $project->project_name ?? $project->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i>Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Mandays</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['total_mandays'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Avg Daily Manpower</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['avg_daily_manpower'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Peak Manpower Day</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['peak_manpower_day'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Projects</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['projects_count'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Breakdown Chart -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h6 class="mb-0 font-weight-bold">Daily Manpower Trend</h6>
                </div>
                <div class="card-body">
                    <canvas id="manpowerChart" height="80"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h6 class="mb-0 font-weight-bold">Manpower by Project</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Project</th>
                                <th>Manpower</th>
                                <th>Avg/Day</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($summary['by_project'] ?? [] as $proj)
                            <tr>
                                <td><small>{{ $proj['name'] }}</small></td>
                                <td><strong>{{ $proj['manpower'] }}</strong></td>
                                <td><small class="text-muted">{{ $proj['avg_daily'] }}</small></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Breakdown Table -->
    <div class="card shadow mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0 font-weight-bold">Daily Breakdown</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th class="text-center">
                                <i class="fas fa-people-group me-1"></i>Manpower
                            </th>
                            <th class="text-center">Reports</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($summary['daily_breakdown'] ?? [] as $date => $day)
                        <tr>
                            <td class="fw-semibold">{{ $day['date'] }}, {{ Carbon\Carbon::parse($date)->format('M d') }}</td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $day['manpower'] }} workers</span>
                            </td>
                            <td class="text-center">{{ $day['reports'] }}</td>
                            <td>
                                @if($day['manpower'] > 0)
                                <span class="badge bg-success">Reported</span>
                                @else
                                <span class="badge bg-secondary">No Data</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- All Reports Table -->
    <div class="card shadow">
        <div class="card-header bg-light">
            <h6 class="mb-0 font-weight-bold">All Submitted Reports</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Project</th>
                            <th>Report Date</th>
                            <th class="text-center">Manpower</th>
                            <th>Items</th>
                            <th>Reported By</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                        <tr>
                            <td><small>{{ $report->project->project_name ?? $report->project->name }}</small></td>
                            <td>{{ $report->report_date->format('M d, Y') }}</td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $report->total_manpower }}</span>
                            </td>
                            <td>{{ $report->items->count() }}</td>
                            <td><small>{{ $report->createdBy->name ?? 'System' }}</small></td>
                            <td>
                                <span class="badge bg-success">Approved</span>
                            </td>
                            <td>
                                <a href="{{ route('daily-reports.show', $report) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No reports found for this period</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Send to GM Modal -->
<div class="modal fade" id="sendGMModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Weekly Report to GM</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('weekly-manpower.sendGM') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="start_date" value="{{ $startDate }}">
                    <input type="hidden" name="end_date" value="{{ $endDate }}">
                    
                    <div class="mb-3">
                        <label class="form-label">GM Email <span class="text-danger">*</span></label>
                        <input type="email" name="gm_email" class="form-control" required 
                               placeholder="gm@company.com">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="include_details" id="includeDetails">
                        <label class="form-check-label" for="includeDetails">
                            Include daily breakdown details
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i>Send Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dailyData = {!! json_encode($summary['daily_breakdown'] ?? []) !!};
    
    const labels = Object.values(dailyData).map(d => d.date);
    const data = Object.values(dailyData).map(d => d.manpower);

    const ctx = document.getElementById('manpowerChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Daily Manpower',
                    data: data,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 5,
                    pointBackgroundColor: '#0d6efd',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 10
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush

@endsection
