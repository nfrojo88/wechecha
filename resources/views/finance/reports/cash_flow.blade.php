@extends('layouts.app')
@section('title', 'Cash Flow Statement')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold"><i class="fa-solid fa-water me-2 text-info"></i>Cash Flow Statement</h1>
            <p class="text-muted mb-0">Indirect Method — {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
        <div>
            <button class="btn btn-light border shadow-sm px-3 me-2" onclick="window.print()"><i class="fas fa-print me-1"></i> Print</button>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left me-1"></i> Reports</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.cash-flow') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted small text-uppercase fw-bold">From Date</label>
                    <input type="date" name="start_date" class="form-control bg-light" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small text-uppercase fw-bold">To Date</label>
                    <input type="date" name="end_date" class="form-control bg-light" value="{{ $endDate }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-dark w-100"><i class="fa-solid fa-filter me-2"></i>Generate</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th class="py-3 px-4 fw-bold">Cash Flows from Activities</th>
                            <th class="py-3 px-4 text-end fw-bold">Amount (ETB)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- OPERATING -->
                        <tr class="bg-light">
                            <td colspan="2" class="px-4 py-3 fw-bold"><i class="fas fa-cogs text-primary me-2"></i>Cash Flows from Operating Activities</td>
                        </tr>
                        <tr>
                            <td class="px-5 py-3">Net Profit / (Loss) for the Period</td>
                            <td class="px-4 text-end fw-semibold">{{ number_format($netProfit, 2) }}</td>
                        </tr>
                        
                        @if(count($operatingAdjustments) > 0)
                        <tr>
                            <td colspan="2" class="px-5 text-muted fst-italic small pb-1">Adjustments for changes in working capital:</td>
                        </tr>
                        @foreach($operatingAdjustments as $adj)
                        <tr>
                            <td class="px-5 ps-5"><span class="badge bg-secondary me-2">{{ $adj['code'] }}</span> {{ $adj['name'] }}</td>
                            <td class="px-4 text-end">{{ number_format($adj['amount'], 2) }}</td>
                        </tr>
                        @endforeach
                        @endif
                        
                        <tr>
                            <td class="px-4 py-3 fw-bold text-primary">Net Cash from Operating Activities</td>
                            <td class="px-4 text-end fw-bold text-primary">ETB {{ number_format($netCashOperating, 2) }}</td>
                        </tr>

                        <!-- INVESTING -->
                        <tr class="bg-light" style="border-top: 2px solid #dee2e6;">
                            <td colspan="2" class="px-4 py-3 fw-bold"><i class="fas fa-chart-line text-warning me-2"></i>Cash Flows from Investing Activities</td>
                        </tr>
                        @forelse($investingAdjustments as $adj)
                        <tr>
                            <td class="px-5 ps-5"><span class="badge bg-secondary me-2">{{ $adj['code'] }}</span> {{ $adj['name'] }}</td>
                            <td class="px-4 text-end">{{ number_format($adj['amount'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-5 text-muted fst-italic py-3">No investing activities.</td>
                        </tr>
                        @endforelse
                        <tr>
                            <td class="px-4 py-3 fw-bold text-warning">Net Cash from Investing Activities</td>
                            <td class="px-4 text-end fw-bold text-warning">ETB {{ number_format($totalInvesting, 2) }}</td>
                        </tr>

                        <!-- FINANCING -->
                        <tr class="bg-light" style="border-top: 2px solid #dee2e6;">
                            <td colspan="2" class="px-4 py-3 fw-bold"><i class="fas fa-university text-success me-2"></i>Cash Flows from Financing Activities</td>
                        </tr>
                        @forelse($financingAdjustments as $adj)
                        <tr>
                            <td class="px-5 ps-5"><span class="badge bg-secondary me-2">{{ $adj['code'] }}</span> {{ $adj['name'] }}</td>
                            <td class="px-4 text-end">{{ number_format($adj['amount'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-5 text-muted fst-italic py-3">No financing activities.</td>
                        </tr>
                        @endforelse
                        <tr>
                            <td class="px-4 py-3 fw-bold text-success">Net Cash from Financing Activities</td>
                            <td class="px-4 text-end fw-bold text-success">ETB {{ number_format($totalFinancing, 2) }}</td>
                        </tr>

                        <!-- TOTAL -->
                        <tr class="bg-dark text-white">
                            <td class="px-4 py-4 fw-bold text-uppercase fs-5">Net Increase / (Decrease) in Cash</td>
                            <td class="px-4 py-4 text-end fw-bold fs-5">ETB {{ number_format($netCashOperating + $totalInvesting + $totalFinancing, 2) }}</td>
                        </tr>
                    </tbody>
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
