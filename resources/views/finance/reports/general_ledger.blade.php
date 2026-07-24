@extends('layouts.app')
@section('title', 'General Ledger')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold"><i class="fa-solid fa-book-open me-2 text-primary"></i>General Ledger</h1>
        <div>
            <button class="btn btn-light border shadow-sm px-3 me-2" onclick="window.print()"><i class="fas fa-print me-1"></i> Print</button>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left me-1"></i> Back</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.general-ledger') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted small text-uppercase fw-bold">From Date</label>
                    <input type="date" name="start_date" class="form-control bg-light" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small text-uppercase fw-bold">To Date</label>
                    <input type="date" name="end_date" class="form-control bg-light" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small text-uppercase fw-bold">Account</label>
                    <select name="account_id" class="form-select bg-light">
                        <option value="">All Accounts</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ $accountId == $acc->id ? 'selected' : '' }}>
                                {{ $acc->code }} - {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
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
                            <th class="py-3 px-4 fw-bold">Date</th>
                            <th class="py-3 px-4 fw-bold">Account</th>
                            <th class="py-3 px-4 text-end fw-bold">Debit (ETB)</th>
                            <th class="py-3 px-4 text-end fw-bold">Credit (ETB)</th>
                            <th class="py-3 px-4 fw-bold">Reference</th>
                            <th class="py-3 px-4 fw-bold">Description</th>
                            <th class="py-3 px-4 fw-bold">Created By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                            <tr>
                                <td class="px-4 text-muted">{{ \Carbon\Carbon::parse($entry->entry_date)->format('M d, Y') }}</td>
                                <td class="px-4">
                                    <span class="text-primary fw-semibold">{{ $entry->account->name ?? 'N/A' }}</span>
                                    @if($entry->side === 'credit')
                                        <br><small class="text-muted ms-3">↳ {{ $entry->account->name ?? 'N/A' }}</small>
                                    @endif
                                </td>
                                <td class="px-4 text-end fw-semibold text-success">
                                    {{ $entry->side === 'debit' ? number_format($entry->amount, 2) : '-' }}
                                </td>
                                <td class="px-4 text-end fw-semibold text-danger">
                                    {{ $entry->side === 'credit' ? number_format($entry->amount, 2) : '-' }}
                                </td>
                                <td class="px-4">
                                    <span class="badge bg-dark">{{ $entry->reference ?? '-' }}</span>
                                </td>
                                <td class="px-4 text-muted">{{ $entry->je_description ?? '-' }}</td>
                                <td class="px-4 text-muted">{{ $entry->journalEntry->creator->name ?? 'System' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-5 text-muted">No journal entries found for this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($entries->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $entries->links() }}
        </div>
        @endif
    </div>
</div>

<style>
@media print {
    .sidebar, .navbar, .btn, form, .pagination { display: none !important; }
    .card { border: none !important; box-shadow: none !important; margin: 0 !important; }
    .container-fluid { padding: 0 !important; }
}
</style>
@endsection
