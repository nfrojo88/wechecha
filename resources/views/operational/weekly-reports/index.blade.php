@extends('layouts.app')
@section('title', 'Weekly Reports')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-calendar-week me-2"></i>Weekly Progress Reports</h1>
        <a href="{{ route('weekly-reports.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Report</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr><th>Week Range</th><th>Project</th><th>Planned %</th><th>Actual %</th><th>Status</th><th class="text-center">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td><strong>{{ \Carbon\Carbon::parse($report->week_start)->format('d M') }} - {{ \Carbon\Carbon::parse($report->week_end)->format('d M, Y') }}</strong></td>
                        <td>{{ $report->project->name }}</td>
                        <td>{{ $report->planned_progress_percent }}%</td>
                        <td>{{ $report->actual_progress_percent }}%</td>
                        <td><span class="badge bg-{{ $report->status == 'approved' ? 'success' : 'secondary' }}">{{ ucfirst($report->status) }}</span></td>
                        <td class="text-center"><a href="{{ route('weekly-reports.show', $report) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4">No weekly reports found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
