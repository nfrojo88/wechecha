@extends('layouts.app')

@section('title', 'Asset Utilization Report')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0">Asset Utilization Report</h1>
        <small class="text-muted">Daily asset assignment and status tracking</small>
    </div>
    <div class="gap-2 d-flex">
        <a href="{{ route('asset-reports.export-utilization') }}" class="btn btn-sm btn-outline-primary">
            <i class="fa-solid fa-download me-2"></i>Export CSV
        </a>
        <a href="{{ route('assets.dashboard') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Assignment Date</th>
                        <th class="text-end">Total Assigned</th>
                        <th class="text-end">Still Active</th>
                        <th class="text-end">Returned</th>
                        <th class="text-end">Damaged</th>
                        <th class="text-end">Active %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row->assignment_date)->format('d M Y') }}</td>
                        <td class="text-end"><strong>{{ $row->total_assigned }}</strong></td>
                        <td class="text-end">
                            <span class="badge bg-success">{{ $row->still_active }}</span>
                        </td>
                        <td class="text-end">
                            <span class="badge bg-warning">{{ $row->returned }}</span>
                        </td>
                        <td class="text-end">
                            <span class="badge bg-danger">{{ $row->damaged }}</span>
                        </td>
                        <td class="text-end">
                            @php
                                $activePercent = $row->total_assigned > 0 ? ($row->still_active / $row->total_assigned) * 100 : 0;
                            @endphp
                            <strong>{{ round($activePercent, 1) }}%</strong>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No data available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
