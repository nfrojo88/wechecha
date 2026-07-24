@extends('layouts.app')

@section('title', 'Employee Asset Allocation Report')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0">Employee Asset Allocation Report</h1>
        <small class="text-muted">Asset distribution across employees and departments</small>
    </div>
    <div class="gap-2 d-flex">
        <a href="{{ route('asset-reports.export-employee-allocation') }}" class="btn btn-sm btn-outline-primary">
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
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Role/Designation</th>
                        <th class="text-end">Active Assets</th>
                        <th class="text-end">Total Value (Br)</th>
                        <th>Date of Joining</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr>
                        <td>
                            <a href="{{ route('employees.show', $employee) }}" class="text-decoration-none">
                                <strong>{{ $employee->full_name }}</strong>
                            </a>
                            <br>
                            <small class="text-muted">{{ $employee->employee_code }}</small>
                        </td>
                        <td>{{ $employee->department }}</td>
                        <td>{{ $employee->role_title ?? 'N/A' }}</td>
                        <td class="text-end">
                            <span class="badge bg-info">{{ $employee->active_asset_count ?? 0 }}</span>
                        </td>
                        <td class="text-end">
                            <strong>Br {{ number_format($employee->total_asset_value ?? 0, 2) }}</strong>
                        </td>
                        <td>{{ $employee->date_of_joining->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No employees with active assets</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $employees->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>

@endsection
