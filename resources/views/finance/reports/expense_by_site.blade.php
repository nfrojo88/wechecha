@extends('layouts.app')
@section('title', 'Expense Report by Site')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold"><i class="fa-solid fa-map-location-dot me-2 text-danger"></i>Expense Report by Site</h1>
        <div>
            <button class="btn btn-light border shadow-sm px-3 me-2" onclick="window.print()"><i class="fas fa-print me-1"></i> Print</button>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left me-1"></i> Back</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.expense-by-site') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted small text-uppercase fw-bold">Start Date</label>
                    <input type="date" name="start_date" class="form-control bg-light" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small text-uppercase fw-bold">End Date</label>
                    <input type="date" name="end_date" class="form-control bg-light" value="{{ $endDate }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-2"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th class="py-3 px-4 fw-bold">Account Code</th>
                            <th class="py-3 px-4 fw-bold">Expense Account</th>
                            <th class="py-3 px-4 text-end fw-bold">Total Expense (ETB)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; @endphp
                        @forelse($expenses as $exp)
                            @php $grandTotal += $exp->total_amount; @endphp
                            <tr>
                                <td class="px-4 text-muted" style="width: 120px;"><span class="badge bg-secondary">{{ $exp->account_code }}</span></td>
                                <td class="px-4"><span class="text-primary fw-semibold">{{ $exp->account_name }}</span></td>
                                <td class="px-4 text-end fw-semibold text-danger">{{ number_format($exp->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-5 text-muted">No expenses found for this period.</td></tr>
                        @endforelse
                    </tbody>
                    @if($grandTotal > 0)
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="2" class="text-end fw-bold py-3 text-uppercase">Grand Total</td>
                            <td class="text-end px-4 fw-bold text-danger" style="font-size: 18px;">{{ number_format($grandTotal, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
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
