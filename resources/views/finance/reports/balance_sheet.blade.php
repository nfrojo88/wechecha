@extends('layouts.app')
@section('title', 'Balance Sheet')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold"><i class="fa-solid fa-building me-2 text-primary"></i>Balance Sheet</h1>
        <div>
            <button class="btn btn-light border shadow-sm px-3 me-2" onclick="window.print()"><i class="fas fa-print me-1"></i> Print</button>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left me-1"></i> Back</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.balance-sheet') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted small text-uppercase fw-bold">As Of Date</label>
                    <input type="date" name="as_of_date" class="form-control bg-light" value="{{ $asOfDate }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-sync-alt me-2"></i>Update</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <!-- ASSETS -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-top: 4px solid #10b981 !important;">
                <div class="card-header bg-white border-0 pt-4 pb-2">
                    <h5 class="text-success fw-bold m-0 text-uppercase" style="letter-spacing: 1px;">Assets</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                @forelse($assets as $acc)
                                    <tr>
                                        <td class="px-4 text-muted border-0" style="width: 80px;"><span class="badge bg-secondary">{{ $acc->code }}</span></td>
                                        <td class="border-0"><span class="text-primary fw-semibold">{{ $acc->name }}</span></td>
                                        <td class="px-4 text-end fw-semibold border-0">{{ number_format($acc->computed_balance, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-4 text-muted border-0">No assets found.</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="2" class="text-end fw-bold py-3 text-uppercase">Total Assets</td>
                                    <td class="text-end px-4 fw-bold" style="font-size: 16px; border: 2px solid #212529 !important;">{{ number_format($totalAssets, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- LIABILITIES & EQUITY -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-top: 4px solid #ef4444 !important;">
                <div class="card-header bg-white border-0 pt-4 pb-2">
                    <h5 class="text-danger fw-bold m-0 text-uppercase" style="letter-spacing: 1px;">Liabilities</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                @forelse($liabilities as $acc)
                                    <tr>
                                        <td class="px-4 text-muted border-0" style="width: 80px;"><span class="badge bg-secondary">{{ $acc->code }}</span></td>
                                        <td class="border-0"><span class="text-primary fw-semibold">{{ $acc->name }}</span></td>
                                        <td class="px-4 text-end fw-semibold border-0">{{ number_format($acc->computed_balance, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-4 text-muted border-0">No liabilities found.</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot style="background: #fee2e2;">
                                <tr>
                                    <td colspan="2" class="text-end fw-bold py-3 text-uppercase text-dark">Total Liabilities</td>
                                    <td class="text-end px-4 fw-bold text-dark" style="font-size: 15px; border: 1px solid #991b1b !important;">{{ number_format($totalLiabilities, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="card-header bg-white border-0 pt-4 pb-2 mt-2" style="border-top: 4px solid #3b82f6 !important;">
                        <h5 class="text-info fw-bold m-0 text-uppercase" style="letter-spacing: 1px;">Equity</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                @forelse($equity as $acc)
                                    <tr>
                                        <td class="px-4 text-muted border-0" style="width: 80px;"><span class="badge bg-secondary">{{ $acc->code }}</span></td>
                                        <td class="border-0"><span class="text-primary fw-semibold">{{ $acc->name }}</span></td>
                                        <td class="px-4 text-end fw-semibold border-0">{{ number_format($acc->computed_balance, 2) }}</td>
                                    </tr>
                                @empty
                                @endforelse
                                
                                <tr style="background: #f8fafc;">
                                    <td class="px-4 text-muted border-0"><span class="badge bg-info text-white">AUTO</span></td>
                                    <td class="border-0"><span class="text-secondary fw-semibold">Current Year Net Income</span></td>
                                    <td class="px-4 text-end fw-semibold border-0 text-success">{{ number_format($netIncome, 2) }}</td>
                                </tr>
                            </tbody>
                            <tfoot style="background: #dbeafe;">
                                <tr>
                                    <td colspan="2" class="text-end fw-bold py-3 text-uppercase text-dark">Total Equity</td>
                                    <td class="text-end px-4 fw-bold text-dark" style="font-size: 15px; border: 1px solid #1e40af !important;">{{ number_format($totalEquity, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TOTAL ROW -->
    <div class="row mt-4">
        <div class="col-lg-6 offset-lg-6">
            <div class="card bg-dark text-white border-0 shadow" style="border-radius: 12px;">
                <div class="card-body d-flex justify-content-between align-items-center py-4">
                    <h4 class="m-0 fw-bold text-uppercase" style="letter-spacing: 2px;">Total L & E</h4>
                    <h3 class="m-0 fw-bold">{{ number_format($totalLiabilities + $totalEquity, 2) }}</h3>
                </div>
            </div>
            
            @if(round($totalAssets, 2) !== round($totalLiabilities + $totalEquity, 2))
            <div class="alert alert-danger mt-3 py-3" style="border-radius: 10px;">
                <i class="fas fa-exclamation-triangle me-2"></i> <strong>WARNING:</strong> Balance Sheet is out of balance by ETB {{ number_format(abs($totalAssets - ($totalLiabilities + $totalEquity)), 2) }}
            </div>
            @endif
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
