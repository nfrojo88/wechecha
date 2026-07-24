@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="h3 mb-0">
                <i class="fas fa-chart-line me-2"></i>Forecast Details
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('manpower-forecast.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Forecast Info -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Forecast Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Project</label>
                        <p class="mb-0"><strong>{{ $forecast->project->name }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Week Starting</label>
                        <p class="mb-0"><strong>{{ $forecast->week_starting->format('F d, Y (l)') }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Designation</label>
                        <p class="mb-0"><strong>{{ $forecast->designation->name }}</strong></p>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted small">Forecasted Headcount</label>
                            <p class="mb-0"><strong class="text-primary fs-5">{{ $forecast->forecasted_headcount }}</strong></p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small">Forecasted Hours</label>
                            <p class="mb-0"><strong class="text-info fs-5">{{ $forecast->forecasted_hours }}</strong></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Status</label>
                        <p class="mb-0">
                            @if ($forecast->status === 'draft')
                                <span class="badge bg-secondary">Draft</span>
                            @elseif ($forecast->status === 'submitted')
                                <span class="badge bg-warning">Submitted</span>
                            @elseif ($forecast->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </p>
                    </div>
                    @if ($forecast->notes)
                        <div class="mb-3">
                            <label class="text-muted small">Notes</label>
                            <p class="mb-0 small">{{ $forecast->notes }}</p>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    @if ($forecast->status === 'draft')
                        <div class="d-grid gap-2">
                            <form method="POST" action="{{ route('manpower-forecast.submit', $forecast->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-paper-plane me-1"></i>Submit for Approval
                                </button>
                            </form>
                        </div>
                    @elseif ($forecast->status === 'submitted')
                        <div class="d-grid gap-2">
                            <form method="POST" action="{{ route('manpower-forecast.approve', $forecast->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-check me-1"></i>Approve
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger btn-sm" onclick="showRejectModal()">
                                <i class="fas fa-times me-1"></i>Reject
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Assignments -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Assigned Resources</h6>
                    @if ($forecast->status === 'draft')
                        <button type="button" class="btn btn-light btn-sm" onclick="showAssignmentModal()">
                            <i class="fas fa-plus me-1"></i>Assign Employee
                        </button>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Employee</th>
                                <th>Hours</th>
                                <th>Billable</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($forecast->assignments as $assignment)
                                <tr>
                                    <td>
                                        <strong>{{ $assignment->employee->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $assignment->employee->code }}</small>
                                    </td>
                                    <td>
                                        {{ $assignment->hours_assigned }} hrs
                                    </td>
                                    <td>
                                        @if ($assignment->billable)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($assignment->status) }}</span>
                                    </td>
                                    <td class="text-end">
                                        @if ($forecast->status === 'draft')
                                            <form method="POST" action="{{ route('manpower-assignment.remove', $assignment->id) }}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this assignment?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No employees assigned yet
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-light">
                    <small class="text-muted">
                        Assigned: <strong>{{ $forecast->assignments()->count() }}</strong> / Needed: <strong>{{ $forecast->forecasted_headcount }}</strong>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assignment Modal -->
<div class="modal fade" id="assignmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('manpower-forecast.assignEmployee', $forecast->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Employee *</label>
                        <select name="employee_id" id="employee_id" class="form-select" required>
                            <option value="">Select Employee</option>
                            @foreach ($availableEmployees as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->name }} ({{ $emp->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="hours_assigned" class="form-label">Hours to Assign *</label>
                        <input type="number" name="hours_assigned" id="hours_assigned" class="form-control" 
                               step="0.5" min="1" max="168" value="40" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="billable" id="billable" class="form-check-input" checked>
                            <label class="form-check-label" for="billable">
                                Billable Hours
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-1"></i>Assign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Forecast</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('manpower-forecast.reject', $forecast->id) }}">
                @csrf
                <div class="modal-body">
                    <label for="rejection_reason" class="form-label">Rejection Reason *</label>
                    <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="4" required></textarea>
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
function showAssignmentModal() {
    new bootstrap.Modal(document.getElementById('assignmentModal')).show();
}

function showRejectModal() {
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endsection
