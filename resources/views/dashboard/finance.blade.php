@extends('layouts.app')
@section('title', 'Finance Head Dashboard')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-chart-line me-2"></i>Finance Head Dashboard</h1>
    </div>

    <!-- KPI Row -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-white shadow-sm">
                <div class="stat-icon bg-primary text-white"><i class="fas fa-arrow-trend-up"></i></div>
                <div class="stat-value text-primary">ETB {{ number_format($kpi['total_income'] ?? 0) }}</div>
                <div class="stat-label text-uppercase">Total Income (YTD)</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-white shadow-sm">
                <div class="stat-icon bg-danger text-white"><i class="fas fa-arrow-trend-down"></i></div>
                <div class="stat-value text-danger">ETB {{ number_format($kpi['total_expense'] ?? 0) }}</div>
                <div class="stat-label text-uppercase">Total Expenses (YTD)</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-white shadow-sm">
                <div class="stat-icon bg-success text-white"><i class="fas fa-sack-dollar"></i></div>
                <div class="stat-value text-success">ETB {{ number_format(($kpi['total_income'] ?? 0) - ($kpi['total_expense'] ?? 0)) }}</div>
                <div class="stat-label text-uppercase">Net Profit</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-white shadow-sm">
                <div class="stat-icon bg-info text-white"><i class="fas fa-wallet"></i></div>
                <div class="stat-value text-info">ETB {{ number_format($kpi['cash_balance'] ?? 0) }}</div>
                <div class="stat-label text-uppercase">Cash Balance</div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Income vs Expense -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Income vs Expenses (Last 6 Months)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="height: 300px;">
                        <canvas id="incomeExpenseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- Expense Breakdown -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Expense Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie" style="height: 300px;">
                        <canvas id="expensePieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Journals Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Recent Transactions & Journals</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Date</th>
                            <th>Reference ID</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentJournals ?? [] as $journal)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($journal->date ?? now())->format('d M, Y') }}</td>
                            <td><strong>{{ $journal->reference ?? 'JRN-001' }}</strong></td>
                            <td>{{ $journal->description ?? 'Record Payment' }}</td>
                            <td><strong>ETB {{ number_format($journal->amount ?? 15000) }}</strong></td>
                            <td><span class="badge bg-success">Posted</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td>10 Jul, 2026</td>
                            <td><strong>JRN-2026-045</strong></td>
                            <td>Monthly Payroll - Bole Site</td>
                            <td><strong>ETB 450,000</strong></td>
                            <td><span class="badge bg-success">Posted</span></td>
                        </tr>
                        <tr>
                            <td>09 Jul, 2026</td>
                            <td><strong>INV-2026-112</strong></td>
                            <td>Equipment Rental - Excavator</td>
                            <td><strong>ETB 120,000</strong></td>
                            <td><span class="badge bg-warning">Pending</span></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bar Chart: Income vs Expense
        const ctxIE = document.getElementById('incomeExpenseChart').getContext('2d');
        new Chart(ctxIE, {
            type: 'bar',
            data: {
                labels: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [
                    {
                        label: 'Income',
                        data: [1900000, 1500000, 2200000, 1800000, 2500000, 2450000],
                        backgroundColor: '#10b981',
                        borderRadius: 4
                    },
                    {
                        label: 'Expense',
                        data: [1100000, 950000, 1300000, 1100000, 1400000, 1150000],
                        backgroundColor: '#ef4444',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Pie Chart: Expenses
        const ctxPie = document.getElementById('expensePieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: ['Materials', 'Payroll', 'Subcontractors', 'Equipment', 'Overhead'],
                datasets: [{
                    data: [45, 25, 15, 10, 5],
                    backgroundColor: ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#64748b'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });
</script>
@endsection
