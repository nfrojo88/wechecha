@extends('layouts.app')
@section('title', 'Company Income Track')
@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark"><i class="fas fa-hand-holding-dollar text-success me-2"></i>Company Income Track</h1>
            <p class="text-muted mb-0">Monitor company revenues across Transportation, Rental, and Construction divisions</p>
        </div>
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#recordIncomeModal">
            <i class="fas fa-plus me-1"></i> Record Income
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col">
            <div class="card shadow-sm border-0 h-100" style="border-bottom: 3px solid #10b981 !important;">
                <div class="card-body">
                    <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.75rem;">Total Balance (Bank + Cash)</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold text-success">ETB {{ number_format($totalBalance, 2) }}</h4>
                        <i class="fas fa-university text-success opacity-50 fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card shadow-sm border-0 h-100" style="border-bottom: 3px solid #6366f1 !important;">
                <div class="card-body">
                    <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.75rem;">Total Revenue</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold text-dark">ETB {{ number_format($totalRevenue, 2) }}</h4>
                        <i class="fas fa-wallet text-secondary opacity-50 fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card shadow-sm border-0 h-100" style="border-bottom: 3px solid #0ea5e9 !important;">
                <div class="card-body">
                    <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.75rem;">Transportation</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold text-info">ETB {{ number_format($transportationTotal, 2) }}</h4>
                        <i class="fas fa-truck text-info opacity-50 fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card shadow-sm border-0 h-100" style="border-bottom: 3px solid #f59e0b !important;">
                <div class="card-body">
                    <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.75rem;">Rental</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold text-warning">ETB {{ number_format($rentalTotal, 2) }}</h4>
                        <i class="fas fa-tractor text-warning opacity-50 fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card shadow-sm border-0 h-100" style="border-bottom: 3px solid #8b5cf6 !important;">
                <div class="card-body">
                    <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.75rem;">Construction</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold text-primary" style="color: #8b5cf6 !important;">ETB {{ number_format($constructionTotal, 2) }}</h4>
                        <i class="fas fa-building text-primary opacity-50 fs-3" style="color: #8b5cf6 !important;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0 pt-4 pb-0">
            <h6 class="fw-bold mb-0"><i class="fas fa-filter me-2 text-muted"></i>Filter Records</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted" style="font-size: 0.85rem;">Income Source</label>
                    <select class="form-select bg-light border-0 text-dark">
                        <option>All Divisions</option>
                        <option>Transportation</option>
                        <option>Rental</option>
                        <option>Construction</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted" style="font-size: 0.85rem;">Project / Site</label>
                    <select class="form-select bg-light border-0 text-dark">
                        <option>All Sites</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted" style="font-size: 0.85rem;">Date Range</label>
                    <div class="input-group">
                        <input type="date" class="form-control bg-light border-0 text-dark">
                        <span class="input-group-text bg-light border-0">-</span>
                        <input type="date" class="form-control bg-light border-0 text-dark">
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100 me-2 shadow-sm rounded-3"><i class="fas fa-search me-1"></i> Filter</button>
                    <button class="btn btn-light border shadow-sm rounded-3"><i class="fas fa-redo"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary" style="font-size: 0.85rem;">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>DATE</th>
                            <th>SOURCE</th>
                            <th>PROJECT / SITE</th>
                            <th>DESCRIPTION / NOTES</th>
                            <th>GROSS AMOUNT</th>
                            <th>VAT (15%)</th>
                            <th>WHT (3%)</th>
                            <th>NET AMOUNT</th>
                            <th>RECORDED BY</th>
                            <th class="text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.95rem;">
                        @forelse($incomes as $inc)
                        @php
                            $gross = $inc->amount;
                            $vat = $gross * 0.15;
                            $wht = $gross * 0.03;
                            $net = $gross + $vat - $wht;
                            
                            $badgeColor = 'bg-secondary';
                            $icon = 'fa-tag';
                            if($inc->category == 'transportation') { $badgeColor = 'bg-info'; $icon = 'fa-truck'; }
                            if($inc->category == 'rental') { $badgeColor = 'bg-warning text-dark'; $icon = 'fa-tractor'; }
                            if($inc->category == 'construction') { $badgeColor = 'bg-primary'; $icon = 'fa-building'; }
                        @endphp
                        <tr>
                            <td class="ps-4 text-muted"><strong>#{{ substr($inc->income_no, -5) }}</strong></td>
                            <td class="text-nowrap">{{ \Carbon\Carbon::parse($inc->income_date)->format('d M Y') }}</td>
                            <td>
                                <span class="badge rounded-pill {{ $badgeColor }} px-3 py-2 fw-normal shadow-sm">
                                    <i class="fas {{ $icon }} me-1"></i> {{ ucfirst($inc->category) }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $inc->project->name ?? '—' }}</td>
                            <td class="text-muted text-truncate" style="max-width: 150px;">{{ $inc->description ?: 'No notes' }}</td>
                            <td><strong class="text-dark">ETB<br>{{ number_format($gross, 2) }}</strong></td>
                            <td class="text-success">+{{ number_format($vat, 2) }}</td>
                            <td class="text-danger">-{{ number_format($wht, 2) }}</td>
                            <td><strong class="text-primary">ETB<br>{{ number_format($net, 2) }}</strong></td>
                            <td class="text-muted" style="font-size: 0.85rem;">
                                {{ explode(' ', $inc->createdBy->name)[0] ?? 'Unknown' }}<br>
                                {{ explode(' ', $inc->createdBy->name)[1] ?? '' }}
                            </td>
                            <td class="text-center">
                                @if($inc->status == 'draft')
                                    <form action="{{ route('income.confirm', $inc) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirm this income record?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success rounded-circle shadow-sm" style="width: 32px; height: 32px;"><i class="fas fa-check"></i></button>
                                    </form>
                                @else
                                    <i class="fas fa-check-circle text-success fs-5"></i>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">No income records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ method_exists($incomes, 'links') ? $incomes->links() : '' }}
        </div>
    </div>
</div>

<!-- Record Income Modal -->
<div class="modal fade" id="recordIncomeModal" tabindex="-1" aria-labelledby="recordIncomeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="recordIncomeModalLabel"><i class="fas fa-hand-holding-dollar text-success me-2"></i>Record Company Income</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 p-md-5 pt-3">
                <form action="{{ route('income.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="payment_method" value="bank_transfer">
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">Income Source <span class="text-danger">*</span></label>
                        <select name="category" class="form-select form-select-lg bg-light border-0 text-dark" required style="border-radius: 12px; font-size: 0.95rem;">
                            <option value="">Select Source...</option>
                            <option value="transportation">Transportation</option>
                            <option value="rental">Rental</option>
                            <option value="construction">Construction</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">Project / Site (Optional)</label>
                        <select name="project_id" class="form-select form-select-lg bg-light border-0 text-dark" style="border-radius: 12px; font-size: 0.95rem;">
                            <option value="">Select Project Site...</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">Deposit to Bank/Cash Account <span class="text-danger">*</span></label>
                        <select name="bank_account_id" class="form-select form-select-lg bg-light border-0 text-dark" required style="border-radius: 12px; font-size: 0.95rem;">
                            <option value="">Select Bank or Cash Account...</option>
                            @foreach($bankAccounts as $b)
                                <option value="{{ $b->id }}">{{ $b->bank_name }} ({{ $b->account_number }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">Amount (ETB) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" id="modalAmount" class="form-control form-control-lg bg-light border-0 text-dark fw-bold" required style="border-radius: 12px; font-size: 0.95rem;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">Income Date <span class="text-danger">*</span></label>
                            <input type="date" name="income_date" class="form-control form-control-lg bg-light border-0 text-dark" required value="{{ date('Y-m-d') }}" style="border-radius: 12px; font-size: 0.95rem;">
                        </div>
                    </div>

                    <!-- Computed Box -->
                    <div class="p-4 mb-4 bg-white border rounded-4 shadow-sm" style="border-left: 4px solid #10b981 !important;">
                        <div class="row g-3">
                            <div class="col-6 border-end">
                                <span class="text-muted" style="font-size: 0.85rem;">Gross Amount:</span>
                                <h5 class="fw-bold text-dark mt-1" id="lblGross">ETB 0.00</h5>
                            </div>
                            <div class="col-6">
                                <span class="text-muted" style="font-size: 0.85rem;">VAT Amount:</span>
                                <div class="mt-1 text-success fw-bold" id="lblVat">0.00</div>
                            </div>
                            <div class="col-6 border-end border-top pt-3">
                                <span class="text-muted" style="font-size: 0.85rem;">Withholding Tax:</span>
                                <div class="mt-1 text-danger fw-bold" id="lblWht">0.00</div>
                            </div>
                            <div class="col-6 border-top pt-3">
                                <span class="text-muted" style="font-size: 0.85rem;">Net Amount:</span>
                                <h5 class="fw-bold text-primary mt-1" id="lblNet">ETB 0.00</h5>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">Description / Notes</label>
                        <textarea name="description" class="form-control bg-light border-0 text-dark p-3" rows="3" placeholder="Enter income details, references, or billing details..." style="border-radius: 12px; resize: none; font-size: 0.95rem;"></textarea>
                    </div>

                    <div class="text-end mt-2">
                        <button type="button" class="btn btn-light border rounded-pill px-4 me-2 shadow-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background-color: #8b5cf6; border: none;"><i class="fas fa-save me-1"></i> Save Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('modalAmount');
    const lblGross = document.getElementById('lblGross');
    const lblVat = document.getElementById('lblVat');
    const lblWht = document.getElementById('lblWht');
    const lblNet = document.getElementById('lblNet');

    function updateCalculations() {
        const gross = parseFloat(amountInput.value) || 0;
        const vat = gross * 0.15;
        const wht = gross * 0.03;
        const net = gross + vat - wht;

        lblGross.textContent = 'ETB ' + gross.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        lblVat.textContent = vat.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        lblWht.textContent = wht.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        lblNet.textContent = 'ETB ' + net.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    amountInput.addEventListener('input', updateCalculations);
});
</script>
@endsection
