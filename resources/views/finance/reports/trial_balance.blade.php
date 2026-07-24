@extends('layouts.app')
@section('title', 'Trial Balance')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold"><i class="fa-solid fa-scale-balanced me-2 text-primary"></i>Trial Balance</h1>
        <div>
            <button class="btn btn-light border shadow-sm px-3 me-2" onclick="window.print()"><i class="fas fa-print me-1"></i> Print</button>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left me-1"></i> Back</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
            <h6 class="m-0 fw-semibold text-secondary">As of {{ date('d M Y') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead class="bg-white text-center">
                        <tr style="border-bottom: 2px solid #f0f0f0;">
                            <th class="text-start text-muted small fw-bold text-uppercase py-3">Account Code</th>
                            <th class="text-start text-muted small fw-bold text-uppercase py-3">Account Name</th>
                            <th class="text-muted small fw-bold text-uppercase py-3">Account Type</th>
                            <th class="text-end text-muted small fw-bold text-uppercase py-3">Debit (ETB)</th>
                            <th class="text-end text-muted small fw-bold text-uppercase py-3">Credit (ETB)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalDebit = 0; $totalCredit = 0; @endphp
                        @forelse($accounts as $acc)
                            @php
                                $debit  = $acc->computed_debit;
                                $credit = $acc->computed_credit;
                                $totalDebit  += $debit;
                                $totalCredit += $credit;
                            @endphp
                            <tr>
                                <td class="fw-semibold text-secondary">{{ $acc->code }}</td>
                                <td>{{ $acc->name }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ ucfirst($acc->type) }}</span>
                                </td>
                                <td class="text-end">{{ $debit > 0 ? number_format($debit, 2) : '-' }}</td>
                                <td class="text-end">{{ $credit > 0 ? number_format($credit, 2) : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state py-4">
                                        <div class="mb-3">
                                            <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 80px; height: 80px;">
                                                <i class="fa-solid fa-file-invoice-dollar text-muted" style="font-size: 32px; opacity: 0.5;"></i>
                                            </div>
                                        </div>
                                        <h6 class="text-dark fw-bold mb-1">No Journal Entries Found</h6>
                                        <p class="text-muted small mb-0">The trial balance will be populated once transactions are recorded.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-white">
                        <tr>
                            <td colspan="3" class="text-end text-uppercase fw-bold align-middle pe-4">Total:</td>
                            <td class="text-end fw-bold align-middle fs-6" style="border: 2px solid #212529 !important; padding: 12px 15px;">{{ number_format($totalDebit, 2) }}</td>
                            <td class="text-end fw-bold align-middle fs-6" style="border: 2px solid #212529 !important; padding: 12px 15px;">{{ number_format($totalCredit, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
            
            @if(round($totalDebit, 2) !== round($totalCredit, 2))
            <div class="alert alert-danger mt-3">
                <i class="fas fa-exclamation-triangle me-2"></i> <strong>Warning:</strong> The Trial Balance is unbalanced by ETB {{ number_format(abs($totalDebit - $totalCredit), 2) }}.
            </div>
            @endif
    </div>
</div>

<style>
@media print {
    .sidebar, .navbar, .btn { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
    .container-fluid { padding: 0 !important; }
}
</style>
@endsection
