@extends('layouts.app')
@section('title', 'Daily Reports')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-calendar-day me-2"></i>Daily Field Reports</h1>
        <a href="{{ route('daily-reports.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Report</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr><th>Date</th><th>Project</th><th>Weather</th><th>Manpower</th><th>Status</th><th>Submitted By</th><th class="text-center">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td><strong>{{ \Carbon\Carbon::parse($report->report_date)->format('d M, Y') }}</strong></td>
                        <td>{{ $report->project->name }}</td>
                        <td>{{ $report->weather_conditions ?? 'N/A' }}</td>
                        <td>{{ $report->total_manpower }}</td>
                        <td><span class="badge bg-{{ $report->status == 'approved' ? 'success' : 'secondary' }}">{{ ucfirst($report->status) }}</span></td>
                        <td>{{ $report->createdBy->name ?? 'Unknown' }}</td>
                        <td class="text-center"><a href="{{ route('daily-reports.show', $report) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4">No daily reports found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
