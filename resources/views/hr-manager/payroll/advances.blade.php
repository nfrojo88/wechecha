@extends('layouts.app')
@section('title', 'Salary Advance Loans')

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="fa-solid fa-hand-holding-dollar text-primary me-2"></i>Salary Advance Loans
            </h1>
            <p class="text-muted small mb-0">Request salary advances, manage GM approvals, and Finance disbursement & payroll recovery.</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#requestLoanModal">
            <i class="fa-solid fa-plus-circle me-1"></i>Request Advance Loan
        </button>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('payroll.advances') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <select name="employee_id" class="form-select form-select-sm">
                        <option value="">-- All Employees --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->full_name }} ({{ $emp->employee_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- All Statuses --</option>
                        <option value="pending"   {{ request('status') == 'pending' ? 'selected' : '' }}>Pending GM Approval</option>
                        <option value="approved"  {{ request('status') == 'approved' ? 'selected' : '' }}>Approved (Awaiting Finance)</option>
                        <option value="disbursed" {{ request('status') == 'disbursed' ? 'selected' : '' }}>Disbursed (Active Recovery)</option>
                        <option value="rejected"  {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected by GM</option>
                        <option value="recovered" {{ request('status') == 'recovered' ? 'selected' : '' }}>Fully Recovered</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                        <i class="fa-solid fa-filter me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Loans List Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0"><i class="fa-solid fa-list-ul text-primary me-2"></i>Advance Loan Requests</h6>
            <span class="badge bg-primary-subtle text-primary">{{ $advances->total() }} Record(s)</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Employee</th>
                        <th>Amount</th>
                        <th>Installments</th>
                        <th>Monthly Deduction</th>
                        <th>Reason</th>
                        <th>Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($advances as $adv)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-semibold">{{ $adv->employee->full_name ?? 'N/A' }}</div>
                            <div class="text-muted" style="font-size:.75rem">{{ $adv->employee->employee_code ?? '' }} • Basic: {{ number_format($adv->employee->basic_salary ?? 0, 2) }} ETB</div>
                        </td>
                        <td class="fw-bold text-dark">{{ number_format($adv->amount, 2) }} ETB</td>
                        <td>{{ $adv->installments }} month(s)</td>
                        <td class="fw-semibold text-danger">{{ number_format($adv->monthly_deduction, 2) }} ETB/mo</td>
                        <td class="text-muted">{{ Str::limit($adv->reason ?? '—', 30) }}</td>
                        <td class="text-muted">{{ $adv->advance_date ? $adv->advance_date->format('M d, Y') : $adv->created_at->format('M d, Y') }}</td>
                        <td class="text-center">
                            @if($adv->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3">Pending GM Approval</span>
                            @elseif($adv->status === 'approved')
                                <span class="badge bg-info-subtle text-info rounded-pill px-3">Approved (Pending Payment)</span>
                            @elseif($adv->status === 'disbursed')
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3">Disbursed (Deducting)</span>
                            @elseif($adv->status === 'rejected')
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Rejected</span>
                            @elseif($adv->status === 'recovered')
                                <span class="badge bg-success-subtle text-success rounded-pill px-3">Fully Recovered</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            {{-- GM Actions --}}
                            @if($adv->status === 'pending' && (auth()->user()->hasRole('gm') || auth()->user()->hasAnyRole(['admin', 'global_admin'])))
                                <button class="btn btn-sm btn-success rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#approveModal{{ $adv->id }}">
                                    <i class="fa-solid fa-check me-1"></i>Approve (GM)
                                </button>
                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $adv->id }}">
                                    <i class="fa-solid fa-xmark me-1"></i>Reject
                                </button>
                            @endif

                            {{-- Finance Actions --}}
                            @if($adv->status === 'approved' && (auth()->user()->hasAnyRole(['Finance head', 'finance_head', 'finance', 'admin', 'global_admin'])))
                                <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#disburseModal{{ $adv->id }}">
                                    <i class="fa-solid fa-coins me-1"></i>Pay / Disburse
                                </button>
                            @endif

                            @if($adv->status === 'disbursed' || $adv->status === 'recovered')
                                <span class="text-muted small"><i class="fa-solid fa-circle-check text-success me-1"></i>Active</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-hand-holding-dollar fa-2x mb-2 d-block opacity-25"></i>
                            No salary advance loan requests found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($advances->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $advances->links() }}
        </div>
        @endif
    </div>

</div>

{{-- ── Request Loan Modal ───────────────────────────────────────────────── --}}
<div class="modal fade" id="requestLoanModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('payroll.advance-request') }}" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-hand-holding-dollar text-primary me-2"></i>New Salary Advance Request
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if(auth()->user()->hasAnyRole(['admin', 'global_admin', 'hr_manager', 'hr_officer', 'Finance head', 'finance_head']))
                <div class="mb-3">
                    <label class="form-label fw-semibold">Select Employee <span class="text-danger">*</span></label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">-- Choose Employee --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }}) - Salary: {{ number_format($emp->basic_salary ?? 0, 2) }} ETB</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-semibold">Requested Amount (ETB) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="100" name="amount" class="form-control" placeholder="e.g. 5000" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Repayment Period (Installment Months) <span class="text-danger">*</span></label>
                    <select name="installments" class="form-select" required>
                        <option value="1">1 Month (Deduct full next salary)</option>
                        <option value="2">2 Months</option>
                        <option value="3">3 Months</option>
                        <option value="4">4 Months</option>
                        <option value="5">5 Months</option>
                        <option value="6">6 Months</option>
                        <option value="12">12 Months</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Reason for Advance Loan</label>
                    <textarea name="reason" class="form-control" rows="2" placeholder="e.g. Emergency medical expenses..."></textarea>
                </div>

                <div class="alert alert-info border-0 rounded-3 mb-0 small">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    Once requested, the loan is sent to the <strong>General Manager (GM)</strong> for approval. Upon approval, Finance disburses the money and monthly installments auto-deduct from payroll.
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="fa-solid fa-paper-plane me-1"></i>Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Action Modals (Approve / Reject / Disburse) ─────────────────────── --}}
@foreach($advances as $adv)
    {{-- GM Approve Modal --}}
    @if($adv->status === 'pending')
    <div class="modal fade" id="approveModal{{ $adv->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('payroll.advance-approve', $adv->id) }}" class="modal-content rounded-4 border-0 shadow">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-success"><i class="fa-solid fa-check-circle me-2"></i>GM Approve Loan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Approve salary advance loan request of <strong>{{ number_format($adv->amount, 2) }} ETB</strong> for <strong>{{ $adv->employee->full_name ?? 'N/A' }}</strong>?</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">GM Remarks (Optional)</label>
                        <textarea name="gm_notes" class="form-control" rows="2" placeholder="Approval notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Approve Loan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- GM Reject Modal --}}
    <div class="modal fade" id="rejectModal{{ $adv->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('payroll.advance-reject', $adv->id) }}" class="modal-content rounded-4 border-0 shadow">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-danger"><i class="fa-solid fa-times-circle me-2"></i>GM Reject Loan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-danger">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="gm_notes" class="form-control" rows="2" placeholder="Specify reason..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Reject Request</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Finance Disburse Modal --}}
    @if($adv->status === 'approved')
    <div class="modal fade" id="disburseModal{{ $adv->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('payroll.advance-disburse', $adv->id) }}" class="modal-content rounded-4 border-0 shadow">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-coins me-2"></i>Finance Disburse Loan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info border-0 rounded-3 small mb-3">
                        Disbursing <strong>{{ number_format($adv->amount, 2) }} ETB</strong> to <strong>{{ $adv->employee->full_name ?? 'N/A' }}</strong>.<br>
                        Monthly deduction of <strong>{{ number_format($adv->monthly_deduction, 2) }} ETB/month</strong> will automatically apply on future payroll runs.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment / Disbursement Remarks</label>
                        <textarea name="finance_notes" class="form-control" rows="2" placeholder="Bank ref, check no, or cash slip..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Disburse Payment</button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endforeach

@endsection
