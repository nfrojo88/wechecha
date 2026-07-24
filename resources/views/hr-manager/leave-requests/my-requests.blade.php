@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="h3 mb-0">
                <i class="fas fa-calendar-alt me-2"></i>My Leave Requests
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('leave-requests.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>New Request
            </a>
        </div>
    </div>

    <!-- Status Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filter by Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Requests</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('leave-requests.my-requests') }}" class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Leave Requests List -->
    <div class="row">
        @forelse ($leaveRequests as $request)
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="mb-1">{{ $request->leaveType->name }}</h6>
                                <small class="text-muted">Requested: {{ $request->created_at->format('M d, Y') }}</small>
                            </div>
                            <span class="badge bg-{{ $request->status_badge }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </div>

                        <!-- Dates -->
                        <div class="mb-3">
                            <p class="mb-1">
                                <i class="fas fa-calendar-days text-primary me-2"></i>
                                <strong>{{ $request->start_date->format('M d, Y') }}</strong> to
                                <strong>{{ $request->end_date->format('M d, Y') }}</strong>
                            </p>
                            <p class="mb-0 text-muted">
                                <strong class="text-dark">{{ $request->days_requested }}</strong> day(s)
                            </p>
                        </div>

                        <!-- Reason -->
                        <div class="mb-3">
                            <p class="small mb-0">
                                <strong>Reason:</strong><br>
                                {{ Str::limit($request->reason, 100) }}
                            </p>
                        </div>

                        <!-- Status Details -->
                        @if ($request->status === 'approved')
                            <div class="alert alert-success py-2 small mb-3">
                                <i class="fas fa-check-circle me-1"></i>
                                Approved on {{ $request->approved_at->format('M d, Y') }}
                                @if ($request->approvedByUser)
                                    by {{ $request->approvedByUser->name }}
                                @endif
                            </div>
                        @elseif ($request->status === 'rejected')
                            <div class="alert alert-danger py-2 small mb-3">
                                <i class="fas fa-times-circle me-1"></i>
                                <strong>Rejection Reason:</strong><br>
                                {{ $request->rejection_reason }}
                            </div>
                        @elseif ($request->status === 'pending')
                            <div class="alert alert-warning py-2 small mb-3">
                                <i class="fas fa-hourglass-half me-1"></i>
                                Awaiting approval from HR Manager
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="d-flex gap-2">
                            <a href="{{ route('leave-requests.show', $request->id) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                <i class="fas fa-eye me-1"></i>View Details
                            </a>
                            @if ($request->status === 'pending')
                                <form method="POST" action="{{ route('leave-requests.destroy', $request->id) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this request?')">
                                        <i class="fas fa-trash me-1"></i>Cancel
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted mb-2">No Leave Requests</h5>
                        <p class="text-muted small mb-4">You haven't submitted any leave requests yet.</p>
                        <a href="{{ route('leave-requests.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Submit New Request
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($leaveRequests->hasPages())
        <div class="mt-4">
            {{ $leaveRequests->links() }}
        </div>
    @endif
</div>
@endsection
