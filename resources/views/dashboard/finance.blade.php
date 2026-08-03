@extends('layouts.app')
@section('title', 'Finance Head Dashboard')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Top Header Banner --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="fa-solid fa-chart-pie text-primary me-2"></i>Finance Head Dashboard
            </h1>
            <p class="text-muted small mb-0">Live financial overview, revenue intelligence, expenses, and cash flow tracking.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('finance.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="fa-solid fa-rotate me-1"></i>Refresh Data
            </a>
        </div>
    </div>

    {{-- KPI Stat Cards Row --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small fw-bold text-muted mb-1">Total Revenue / Income</div>
                        <h3 class="fw-bold text-success mb-0">ETB {{ number_format($kpi['total_income'] ?? 0, 2) }}</h3>
                        <div class="small text-muted mt-1"><i class="fa-solid fa-circle-check text-success me-1"></i>Payments & Certified IPCs</div>
                    </div>
                    <div class="bg-success-subtle text-success rounded-4 p-3 fs-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small fw-bold text-muted mb-1">Total Expenses</div>
                        <h3 class="fw-bold text-danger mb-0">ETB {{ number_format($kpi['total_expense'] ?? 0, 2) }}</h3>
                        <div class="small text-muted mt-1"><i class="fa-solid fa-wallet text-danger me-1"></i>Site Expenses & Payroll</div>
                    </div>
                    <div class="bg-danger-subtle text-danger rounded-4 p-3 fs-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="fa-solid fa-arrow-trend-down"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small fw-bold text-muted mb-1">Net Cash Position</div>
                        @php $net = ($kpi['total_income'] ?? 0) - ($kpi['total_expense'] ?? 0); @endphp
                        <h3 class="fw-bold {{ $net >= 0 ? 'text-primary' : 'text-danger' }} mb-0">ETB {{ number_format($net, 2) }}</h3>
                        <div class="small text-muted mt-1"><i class="fa-solid fa-scale-balanced me-1"></i>Net Operating Margin</div>
                    </div>
                    <div class="bg-primary-subtle text-primary rounded-4 p-3 fs-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="fa-solid fa-vault"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small fw-bold text-muted mb-1">Pending Approvals</div>
                        <h3 class="fw-bold text-warning mb-0">{{ number_format($kpi['pending_payments'] ?? 0) }}</h3>
                        <div class="small text-muted mt-1"><i class="fa-solid fa-clock-rotate-left text-warning me-1"></i>Expenses & Payrolls</div>
                    </div>
                    <div class="bg-warning-subtle text-warning rounded-4 p-3 fs-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row g-4 mb-4">
        {{-- Income vs Expense 6-Month Chart --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-chart-column text-primary me-2"></i>Real Monthly Cash Flow (Income vs Expenses)
                    </h6>
                    <span class="badge bg-light text-muted border">Live Data</span>
                </div>
                <div style="height: 290px;">
                    <canvas id="liveIncomeExpenseChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Real Expense Category Breakdown --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-chart-pie text-danger me-2"></i>Expense Distribution
                    </h6>
                    <span class="badge bg-light text-muted border">By Category</span>
                </div>
                <div style="height: 290px;">
                    <canvas id="liveExpensePieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Real Live Transactions & Expenses Tables Row --}}
    <div class="row g-4 mb-4">
        {{-- Real Project Client Payments --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
                    <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-receipt text-success me-2"></i>Recent Client Payments</h6>
                    <span class="badge bg-success-subtle text-success fw-bold">Income Receipts</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Date</th>
                                <th>Ref #</th>
                                <th>Project</th>
                                <th>Amount (ETB)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions ?? [] as $pmt)
                            <tr>
                                <td class="small">{{ $pmt->payment_date ? $pmt->payment_date->format('M d, Y') : 'N/A' }}</td>
                                <td><span class="fw-semibold text-dark">{{ $pmt->reference_number ?: 'PMT-' . $pmt->id }}</span></td>
                                <td class="small">{{ $pmt->project->name ?? 'General Project' }}</td>
                                <td class="fw-bold text-success">ETB {{ number_format($pmt->amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted small">No payment receipts recorded yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Real Site Expenses --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
                    <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-file-invoice-dollar text-danger me-2"></i>Recent Site Expenses</h6>
                    <span class="badge bg-danger-subtle text-danger fw-bold">Outflow Entries</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Notes / Source</th>
                                <th>Amount (ETB)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentExpenses ?? [] as $exp)
                            <tr>
                                <td class="small">{{ isset($exp->expense_date) ? \Carbon\Carbon::parse($exp->expense_date)->format('M d, Y') : 'N/A' }}</td>
                                <td><span class="badge bg-secondary-subtle text-secondary">{{ $exp->category ?? 'Expense' }}</span></td>
                                <td class="small text-muted text-truncate" style="max-width: 160px;">{{ $exp->title ?? $exp->description ?? 'Site Expense Entry' }}</td>
                                <td class="fw-bold text-danger">ETB {{ number_format($exp->amount ?? 0, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted small">No site expense records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const labels = {!! json_encode($monthlyAnalytics['labels'] ?? ['Jan','Feb','Mar','Apr','May','Jun']) !!};
    const incomes = {!! json_encode($monthlyAnalytics['incomes'] ?? [0,0,0,0,0,0]) !!};
    const expenses = {!! json_encode($monthlyAnalytics['expenses'] ?? [0,0,0,0,0,0]) !!};

    const ctxIE = document.getElementById('liveIncomeExpenseChart').getContext('2d');
    new Chart(ctxIE, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Income (ETB)',
                    data: incomes,
                    backgroundColor: '#10b981',
                    borderRadius: 6
                },
                {
                    label: 'Expenses (ETB)',
                    data: expenses,
                    backgroundColor: '#ef4444',
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    @php
        $expLabels = [];
        $expTotals = [];
        foreach($expenseCategories ?? [] as $ec) {
            $expLabels[] = $ec->category ?: 'General';
            $expTotals[] = (float) $ec->total;
        }
        if (empty($expLabels)) {
            $expLabels = ['Materials', 'Payroll', 'Overhead'];
            $expTotals = [1, 1, 1];
        }
    @endphp

    const expLabels = {!! json_encode($expLabels) !!};
    const expTotals = {!! json_encode($expTotals) !!};

    const ctxPie = document.getElementById('liveExpensePieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: expLabels,
            datasets: [{
                data: expTotals,
                backgroundColor: ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#64748b'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>
@endpush
@endsection

