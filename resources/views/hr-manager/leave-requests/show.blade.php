@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-4">
                <a href="{{ route('leave-requests.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Leave Request Details
                    </h5>
                </div>

                <div class="card-body">
                    <!-- Status Badge -->
                    <div class="mb-4">
                        <span class="badge bg-{{ $leaveRequest->status_badge }} fs-6">
                            {{ ucfirst($leaveRequest->status) }}
                        </span>
                    </div>

                    <!-- Employee Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-2">Employee Name</h6>
                            <p class="mb-0"><strong>{{ $leaveRequest->employee->name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-2">Employee Code</h6>
                            <p class="mb-0"><strong>{{ $leaveRequest->employee->code }}</strong></p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-2">Department</h6>
                            <p class="mb-0"><strong>{{ $leaveRequest->employee->department?->name ?? 'N/A' }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-2">Designation</h6>
                            <p class="mb-0"><strong>{{ $leaveRequest->employee->designation?->name ?? 'N/A' }}</strong></p>
                        </div>
                    </div>

                    <hr>

                    <!-- Leave Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-2">Leave Type</h6>
                            <p class="mb-0"><strong>{{ $leaveRequest->leaveType->name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-2">Paid Leave</h6>
                            <p class="mb-0">
                                @if ($leaveRequest->leaveType->is_paid)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-warning">No (Unpaid)</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-2">From Date</h6>
                            <p class="mb-0"><strong>{{ $leaveRequest->start_date->format('F d, Y (l)') }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-2">To Date</h6>
                            <p class="mb-0"><strong>{{ $leaveRequest->end_date->format('F d, Y (l)') }}</strong></p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-2">Total Days</h6>
                            <p class="mb-0"><strong class="text-primary">{{ $leaveRequest->days_requested }} day(s)</strong></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-2">Submitted On</h6>
                            <p class="mb-0"><strong>{{ $leaveRequest->created_at->format('F d, Y H:i A') }}</strong></p>
                        </div>
                    </div>

                    <hr>

                    <!-- Reason -->
                    <div class="mb-4">
                        <h6 class="text-muted small mb-2">Reason for Leave</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $leaveRequest->reason }}
                        </div>
                    </div>

                    <!-- Attachment -->
                    @if ($leaveRequest->attachment)
                        <div class="mb-4">
                            <h6 class="text-muted small mb-2">Supporting Document</h6>
                            <a href="{{ Storage::url($leaveRequest->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i>Download Attachment
                            </a>
                        </div>
                    @endif

                    <!-- Approval Information -->
                    @if ($leaveRequest->status !== 'pending')
                        <hr>
                        <div class="p-3 bg-light rounded">
                            <h6 class="mb-3">
                                @if ($leaveRequest->status === 'approved')
                                    <i class="fas fa-check-circle text-success me-2"></i>Approval Details
                                @else
                                    <i class="fas fa-times-circle text-danger me-2"></i>Rejection Details
                                @endif
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted small mb-2">Processed By</h6>
                                    <p class="mb-0"><strong>{{ $leaveRequest->approvedByUser?->name ?? 'System' }}</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted small mb-2">Date</h6>
                                    <p class="mb-0"><strong>{{ $leaveRequest->approved_at?->format('F d, Y H:i A') }}</strong></p>
                                </div>
                            </div>
                            @if ($leaveRequest->status === 'rejected' && $leaveRequest->rejection_reason)
                                <div class="mt-3">
                                    <h6 class="text-muted small mb-2">Rejection Reason</h6>
                                    <p class="mb-0">{{ $leaveRequest->rejection_reason }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Footer Actions -->
                @if ($leaveRequest->status === 'pending' && Auth::user()->hasRole(['hr_manager', 'hr_officer', 'admin']))
                    <div class="card-footer bg-light d-flex gap-2">
                        <button type="button" class="btn btn-success" onclick="approveRequest()">
                            <i class="fas fa-check me-1"></i>Approve
                        </button>
                        <button type="button" class="btn btn-danger" onclick="showRejectForm()">
                            <i class="fas fa-times me-1"></i>Reject
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Approve Confirmation Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Leave Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve this leave request?</p>
                <div class="alert alert-info small">
                    <strong>{{ $leaveRequest->employee->name }}</strong> will be marked on leave for
                    <strong>{{ $leaveRequest->days_requested }} day(s)</strong> from
                    <strong>{{ $leaveRequest->start_date->format('M d, Y') }}</strong>.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('leave-requests.approve', $leaveRequest->id) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>Approve
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Form Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Leave Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('leave-requests.reject', $leaveRequest->id) }}">
                @csrf
                <div class="modal-body">
                    <label class="form-label">Rejection Reason</label>
                    <textarea name="rejection_reason" class="form-control" rows="4" placeholder="Please provide reasons for rejection..." required></textarea>
                    <small class="text-muted">Minimum 10 characters</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i>Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approveRequest() {
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function showRejectForm() {
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endsection
