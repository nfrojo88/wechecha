@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Leave History</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">All Leave Requests</h5>
                        <form method="GET" class="form-inline">
                            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Leave Type</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Days</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Submitted On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($leaves as $leave)
                                <tr>
                                    <td>
                                        <strong>{{ $leave->leaveType->name ?? 'N/A' }}</strong>
                                    </td>
                                    <td>{{ $leave->from_date->format('M d, Y') }}</td>
                                    <td>{{ $leave->to_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $leave->duration }} days</span>
                                    </td>
                                    <td>{{ Str::limit($leave->reason, 40) }}</td>
                                    <td>
                                        @if ($leave->status === 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif ($leave->status === 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @elseif ($leave->status === 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @elseif ($leave->status === 'cancelled')
                                            <span class="badge badge-secondary">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>{{ $leave->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('leave-requests.show', $leave) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No leave requests found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($leaves->hasPages())
                    <div class="mt-4">
                        {{ $leaves->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Leave Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h6 class="text-muted">Total Requests</h6>
                            <h3 class="text-primary">{{ $leaves->total() }}</h3>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Approved</h6>
                            <h3 class="text-success">{{ $employee->leaveRequests()->where('status', 'approved')->count() }}</h3>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Pending</h6>
                            <h3 class="text-warning">{{ $employee->leaveRequests()->where('status', 'pending')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
