@extends('layouts.app')

@section('title', 'COA Fund Transfers — Finance Head')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-0"><i class="fa-solid fa-money-bill-transfer text-primary me-2"></i>COA Money Transfers</h1>
            <p class="text-muted small mb-0">Direct fund transfer between Chart of Accounts with automatic Double-Entry General Ledger postings</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('coa.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-sitemap me-1"></i>Chart of Accounts
            </a>
            <a href="{{ route('coa-transfers.create') }}" class="btn btn-primary btn-sm fw-bold shadow-sm">
                <i class="fa-solid fa-plus me-1"></i>New Fund Transfer
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
        <i class="fa-solid fa-circle-check fa-lg text-success"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-white-50 text-uppercase fw-semibold">Total Transferred Volume</small>
                            <h4 class="mb-0 fw-bold mt-1">{{ number_format($stats['total_amount'], 2) }} ETB</h4>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded p-3">
                            <i class="fa-solid fa-vault fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-white-50 text-uppercase fw-semibold">Total Transfer Count</small>
                            <h4 class="mb-0 fw-bold mt-1">{{ number_format($stats['total_count']) }} Transfers</h4>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded p-3">
                            <i class="fa-solid fa-list-check fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-white-50 text-uppercase fw-semibold">Today's Transfers</small>
                            <h4 class="mb-0 fw-bold mt-1">{{ number_format($stats['today_amount'], 2) }} ETB</h4>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded p-3">
                            <i class="fa-solid fa-calendar-day fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-dark text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-white-50 text-uppercase fw-semibold">Active COA Accounts</small>
                            <h4 class="mb-0 fw-bold mt-1">{{ $accounts->count() }} Accounts</h4>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded p-3">
                            <i class="fa-solid fa-building-columns fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('coa-transfers.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Search Keyword</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Transfer #, Ref, Account, Notes..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Source Account (From)</label>
                    <select name="from_coa_id" class="form-select form-select-sm">
                        <option value="">-- All Source Accounts --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" @selected(request('from_coa_id') == $acc->id)>
                                {{ $acc->code }} - {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Destination (To)</label>
                    <select name="to_coa_id" class="form-select form-select-sm">
                        <option value="">-- All Destinations --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" @selected(request('to_coa_id') == $acc->id)>
                                {{ $acc->code }} - {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100" title="Apply Filter">
                        <i class="fa-solid fa-filter"></i>
                    </button>
                    @if(request()->hasAny(['search', 'from_coa_id', 'to_coa_id', 'date_from', 'date_to']))
                    <a href="{{ route('coa-transfers.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear Filters">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Transfers Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="table-dark small text-uppercase">
                        <tr>
                            <th>Transfer #</th>
                            <th>Date</th>
                            <th>Source Account (From)</th>
                            <th>Destination Account (To)</th>
                            <th class="text-end">Amount (ETB)</th>
                            <th>Reference / Notes</th>
                            <th>Posted By</th>
                            <th>Receipt</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $trf)
                        <tr>
                            <td class="fw-bold font-monospace text-primary">
                                <a href="{{ route('coa-transfers.show', $trf) }}" class="text-decoration-none">
                                    {{ $trf->transfer_no }}
                                </a>
                            </td>
                            <td>
                                <div>{{ optional($trf->transfer_date)->format('d M Y') }}</div>
                                <small class="text-muted">{{ $trf->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">
                                    <span class="badge bg-secondary font-monospace me-1">{{ $trf->fromCoa->code ?? '—' }}</span>
                                    {{ $trf->fromCoa->name ?? 'Deleted Account' }}
                                </div>
                                <small class="text-muted">{{ ucfirst($trf->fromCoa->type ?? '') }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-success">
                                    <span class="badge bg-success bg-opacity-25 text-dark font-monospace me-1">{{ $trf->toCoa->code ?? '—' }}</span>
                                    {{ $trf->toCoa->name ?? 'Deleted Account' }}
                                </div>
                                <small class="text-muted">{{ ucfirst($trf->toCoa->type ?? '') }}</small>
                            </td>
                            <td class="text-end fw-bold text-dark fs-6 font-monospace">
                                {{ number_format($trf->amount, 2) }}
                            </td>
                            <td>
                                @if($trf->reference_no)
                                    <div><span class="badge bg-light text-dark border">Ref: {{ $trf->reference_no }}</span></div>
                                @endif
                                <small class="text-secondary d-block text-truncate" style="max-width: 220px;" title="{{ $trf->description }}">
                                    {{ $trf->description }}
                                </small>
                            </td>
                            <td>
                                <small class="fw-semibold text-dark">{{ $trf->creator->name ?? 'System' }}</small>
                                @if($trf->journalEntry)
                                    <div>
                                        <a href="{{ route('journal-entries.show', $trf->journalEntry) }}" class="badge bg-info text-dark text-decoration-none">
                                            <i class="fa-solid fa-book-journal-whills me-1"></i>{{ $trf->journalEntry->entry_no }}
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($trf->attachment_path)
                                    <a href="{{ $trf->attachment_url }}" target="_blank" class="btn btn-xs btn-outline-success py-1 px-2 rounded" title="View Transfer Slip">
                                        <i class="fa-solid fa-paperclip me-1"></i>Slip
                                    </a>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('coa-transfers.show', $trf) }}" class="btn btn-sm btn-outline-primary" title="View Transfer Voucher">
                                    <i class="fa-solid fa-file-invoice me-1"></i>Voucher
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-money-bill-transfer fa-3x mb-3 opacity-50"></i>
                                <h6 class="fw-bold">No COA Money Transfers Found</h6>
                                <p class="small mb-3">Transfer money seamlessly between Chart of Accounts to balance registers or fund projects.</p>
                                <a href="{{ route('coa-transfers.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-plus me-1"></i>Make First Transfer
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transfers->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
            <small class="text-muted">Showing {{ $transfers->firstItem() }} to {{ $transfers->lastItem() }} of {{ $transfers->total() }} transfers</small>
            <div>{{ $transfers->links() }}</div>
        </div>
        @endif
    </div>
</div>
@endsection
