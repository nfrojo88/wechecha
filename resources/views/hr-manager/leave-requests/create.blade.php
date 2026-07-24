@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-plus me-2"></i>Submit Leave Request
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('leave-requests.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Employee Info -->
                        <div class="mb-3 p-3 bg-light rounded">
                            <h6 class="mb-2">Employee Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Name</label>
                                    <p class="mb-0"><strong>{{ $employee->name }}</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Employee Code</label>
                                    <p class="mb-0"><strong>{{ $employee->code }}</strong></p>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Department</label>
                                    <p class="mb-0"><strong>{{ $employee->department?->name ?? 'N/A' }}</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Designation</label>
                                    <p class="mb-0"><strong>{{ $employee->designation?->name ?? 'N/A' }}</strong></p>
                                </div>
                            </div>
                        </div>

                        <!-- Leave Balance -->
                        @if ($balances->count() > 0)
                            <div class="mb-3 p-3 bg-light rounded">
                                <h6 class="mb-3">Your Leave Balance (Current Year)</h6>
                                <div class="row">
                                    @foreach ($balances as $balance)
                                        <div class="col-md-6 mb-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted">{{ $balance->leaveType->name }}:</span>
                                                <span>
                                                    <strong class="text-success">{{ $balance->remaining_days }}</strong> / {{ $balance->total_days }}
                                                </span>
                                            </div>
                                            <div class="progress" style="height: 4px;">
                                                <div class="progress-bar" 
                                                     style="width: {{ ($balance->remaining_days / $balance->total_days) * 100 }}%">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Leave Type -->
                        <div class="mb-3">
                            <label for="leave_type_id" class="form-label">Leave Type *</label>
                            <select name="leave_type_id" id="leave_type_id" class="form-select @error('leave_type_id') is-invalid @enderror" required>
                                <option value="">Select Leave Type</option>
                                @foreach ($leaveTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }} ({{ $type->days_allowed }} days/year)
                                        @if (!$type->is_paid)
                                            - Unpaid
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('leave_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date Range -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">From Date *</label>
                                <input type="date" name="start_date" id="start_date" 
                                       class="form-control @error('start_date') is-invalid @enderror" 
                                       value="{{ old('start_date') }}" 
                                       min="{{ date('Y-m-d') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">To Date *</label>
                                <input type="date" name="end_date" id="end_date" 
                                       class="form-control @error('end_date') is-invalid @enderror" 
                                       value="{{ old('end_date') }}" 
                                       min="{{ date('Y-m-d') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Total Days Display -->
                        <div class="mb-3">
                            <label class="form-label">Total Days</label>
                            <div class="input-group">
                                <input type="number" id="totalDays" class="form-control" readonly value="0">
                                <span class="input-group-text">days</span>
                            </div>
                        </div>

                        <!-- Reason -->
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for Leave *</label>
                            <textarea name="reason" id="reason" 
                                      class="form-control @error('reason') is-invalid @enderror" 
                                      rows="4" placeholder="Please provide details..." 
                                      value="{{ old('reason') }}" required></textarea>
                            <small class="text-muted">Minimum 10 characters</small>
                            @error('reason')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Attachment -->
                        <div class="mb-3">
                            <label for="attachment" class="form-label">Supporting Document (Optional)</label>
                            <input type="file" name="attachment" id="attachment" 
                                   class="form-control @error('attachment') is-invalid @enderror"
                                   accept=".pdf,.doc,.docx">
                            <small class="text-muted">PDF, DOC, or DOCX (Max 2MB)</small>
                            @error('attachment')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Terms -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    I confirm that all information provided is accurate and complete.
                                </label>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Submit Request
                            </button>
                            <a href="{{ route('leave-requests.my-requests') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('start_date').addEventListener('change', calculateDays);
document.getElementById('end_date').addEventListener('change', calculateDays);

function calculateDays() {
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(document.getElementById('end_date').value);
    
    if (startDate && endDate && endDate >= startDate) {
        const days = Math.floor((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
        document.getElementById('totalDays').value = days;
    } else {
        document.getElementById('totalDays').value = 0;
    }
}

// Set min date for end_date based on start_date
document.getElementById('start_date').addEventListener('change', function() {
    document.getElementById('end_date').min = this.value;
});
</script>
@endsection
