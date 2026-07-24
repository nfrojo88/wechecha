@extends('layouts.app')
@section('title', 'Income Statement')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold"><i class="fa-solid fa-chart-line me-2 text-success"></i>Income Statement</h1>
        <div>
            <button class="btn btn-light border shadow-sm px-3 me-2" onclick="window.print()"><i class="fas fa-print me-1"></i> Print</button>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left me-1"></i> Back</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.income-statement') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted small text-uppercase fw-bold">Start Date</label>
                    <input type="date" name="start_date" class="form-control bg-light" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small text-uppercase fw-bold">End Date</label>
                    <input type="date" name="end_date" class="form-control bg-light" value="{{ $endDate }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-2"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <!-- REVENUE -->
                    <thead class="bg-dark text-white">
                        <tr>
                            <th colspan="2" class="py-3 px-4 text-uppercase fw-bold" style="letter-spacing: 1px;">Revenue</th>
                            <th class="py-3 px-4 text-end text-uppercase fw-bold">Balance (ETB)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revenues as $acc)
                            <tr>
                                <td class="px-4 text-muted" style="width: 80px;"><span class="badge bg-secondary">{{ $acc->code }}</span></td>
                                <td><span class="text-primary fw-semibold">{{ $acc->name }}</span> <span class="ms-2 small text-muted">{{ $acc->subtype }}</span></td>
                                <td class="px-4 text-end fw-semibold text-success">{{ number_format($acc->computed_balance, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-4 text-muted">No revenue recorded in this period.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="2" class="text-end fw-bold py-3 text-uppercase">Total Revenue</td>
                            <td class="text-end px-4 fw-bold text-success" style="font-size: 16px;">{{ number_format($totalRevenue, 2) }}</td>
                        </tr>
                    </tfoot>

                    <!-- EXPENSES -->
                    <thead class="bg-dark text-white" style="border-top: 4px solid #fff;">
                        <tr>
                            <th colspan="2" class="py-3 px-4 text-uppercase fw-bold" style="letter-spacing: 1px;">Expenses & Costs</th>
                            <th class="py-3 px-4 text-end text-uppercase fw-bold">Balance (ETB)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $acc)
                            <tr>
                                <td class="px-4 text-muted"><span class="badge bg-secondary">{{ $acc->code }}</span></td>
                                <td><span class="text-primary fw-semibold">{{ $acc->name }}</span> <span class="ms-2 small text-muted">{{ $acc->subtype }}</span></td>
                                <td class="px-4 text-end fw-semibold text-danger">{{ number_format($acc->computed_balance, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-4 text-muted">No expenses recorded in this period.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="2" class="text-end fw-bold py-3 text-uppercase">Total Expenses</td>
                            <td class="text-end px-4 fw-bold text-danger" style="font-size: 16px;">{{ number_format($totalExpense, 2) }}</td>
                        </tr>
                    </tfoot>

                    <!-- NET INCOME -->
                    <tfoot style="border-top: 3px solid #212529;">
                        <tr>
                            <td colspan="2" class="text-center fw-bold py-4 text-uppercase" style="font-size: 20px;">
                                {{ $netIncome >= 0 ? 'NET INCOME' : 'NET LOSS' }}
                            </td>
                            <td class="text-end px-4 fw-bold py-4 {{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 20px;">
                                {{ number_format(abs($netIncome), 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .navbar, .btn, form { display: none !important; }
    .card { border: none !important; box-shadow: none !important; margin: 0 !important; }
    .container-fluid { padding: 0 !important; }
}
</style>
@endsection
