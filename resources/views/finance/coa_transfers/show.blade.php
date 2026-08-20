@extends('layouts.app')

@section('title', 'Transfer Voucher #' . $coaTransfer->transfer_no)

@section('content')
<div class="container">
    {{-- Top Action Bar --}}
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <div class="d-flex align-items-center">
            <a href="{{ route('coa-transfers.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="h3 mb-0">Fund Transfer Voucher: {{ $coaTransfer->transfer_no }}</h1>
                <small class="text-muted">General Ledger Money Transfer Record</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-dark btn-sm" onclick="window.print()">
                <i class="fa-solid fa-print me-1"></i>Print Voucher
            </button>
            <a href="{{ route('coa-transfers.create') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus me-1"></i>New Transfer
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4 d-print-none" role="alert">
        <i class="fa-solid fa-circle-check fa-lg text-success"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Official Printable Transfer Voucher Card --}}
    <div class="card border shadow-sm p-4 p-md-5 bg-white">
        {{-- Voucher Header --}}
        <div class="row align-items-center pb-4 border-bottom mb-4">
            <div class="col-sm-7">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="fa-solid fa-building-shield fa-2x text-primary"></i>
                    <h3 class="fw-bold mb-0 text-dark">WECHECHA CONSTRUCTION</h3>
                </div>
                <div class="text-muted small">Enterprise Resource Planning • Finance & Accounting Division</div>
                <div class="text-muted small">Inter-Account Fund Transfer Voucher</div>
            </div>
            <div class="col-sm-5 text-sm-end mt-3 mt-sm-0">
                <div class="badge bg-primary fs-6 px-3 py-2 font-monospace">{{ $coaTransfer->transfer_no }}</div>
                <div class="small text-muted mt-2">
                    <strong>Date:</strong> {{ optional($coaTransfer->transfer_date)->format('d M Y') }}
                </div>
                @if($coaTransfer->reference_no)
                <div class="small text-muted">
                    <strong>Ref / Slip #:</strong> {{ $coaTransfer->reference_no }}
                </div>
                @endif
            </div>
        </div>

        {{-- Amount Banner --}}
        <div class="p-3 bg-light rounded border border-2 border-primary-subtle text-center mb-4">
            <small class="text-uppercase fw-bold text-muted d-block">Transferred Amount</small>
            <div class="fs-2 fw-bold text-primary font-monospace mt-1">
                {{ number_format($coaTransfer->amount, 2) }} ETB
            </div>
            <div class="small text-muted fst-italic mt-1">
                Status: <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Completed & Posted</span>
            </div>
        </div>

        {{-- Account Transfer Flow --}}
        <div class="row g-4 mb-4">
            {{-- Source Account (Credit) --}}
            <div class="col-md-6">
                <div class="card border border-danger-subtle bg-danger bg-opacity-10 h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-danger">SOURCE (FROM) ACCOUNT</span>
                        <small class="text-danger fw-bold">CREDIT</small>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">
                        <span class="badge bg-dark font-monospace me-1">{{ $coaTransfer->fromCoa->code ?? '—' }}</span>
                        {{ $coaTransfer->fromCoa->name ?? 'Account' }}
                    </h5>
                    <div class="small text-muted mt-1">
                        Type: <strong>{{ ucfirst($coaTransfer->fromCoa->type ?? 'General') }}</strong>
                        @if($coaTransfer->fromCoa->manager)
                            • Manager: <strong>{{ $coaTransfer->fromCoa->manager->name }}</strong>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Destination Account (Debit) --}}
            <div class="col-md-6">
                <div class="card border border-success-subtle bg-success bg-opacity-10 h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-success">DESTINATION (TO) ACCOUNT</span>
                        <small class="text-success fw-bold">DEBIT</small>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">
                        <span class="badge bg-dark font-monospace me-1">{{ $coaTransfer->toCoa->code ?? '—' }}</span>
                        {{ $coaTransfer->toCoa->name ?? 'Account' }}
                    </h5>
                    <div class="small text-muted mt-1">
                        Type: <strong>{{ ucfirst($coaTransfer->toCoa->type ?? 'General') }}</strong>
                        @if($coaTransfer->toCoa->manager)
                            • Manager: <strong>{{ $coaTransfer->toCoa->manager->name }}</strong>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Description / Remarks --}}
        <div class="mb-4">
            <h6 class="fw-bold small text-uppercase text-muted">Transfer Description & Reason:</h6>
            <div class="p-3 bg-light rounded border text-dark">
                {{ $coaTransfer->description }}
            </div>
        </div>

        {{-- Journal Entry Details --}}
        @if($coaTransfer->journalEntry)
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold small text-uppercase text-muted mb-0">
                    <i class="fa-solid fa-book-journal-whills me-1 text-primary"></i>Posted Journal Entry Record ({{ $coaTransfer->journalEntry->entry_no }})
                </h6>
                <a href="{{ route('journal-entries.show', $coaTransfer->journalEntry) }}" class="btn btn-xs btn-outline-primary d-print-none">
                    View in GL
                </a>
            </div>
            <div class="table-responsive border rounded">
                <table class="table table-sm table-striped align-middle mb-0 font-monospace small">
                    <thead class="table-light">
                        <tr>
                            <th>Account Code & Title</th>
                            <th>Description</th>
                            <th class="text-end">Debit (ETB)</th>
                            <th class="text-end">Credit (ETB)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coaTransfer->journalEntry->lines as $line)
                        <tr>
                            <td>
                                <strong>{{ $line->account->code ?? '' }}</strong> - {{ $line->account->name ?? '' }}
                            </td>
                            <td class="text-secondary">{{ $line->description }}</td>
                            <td class="text-end fw-bold {{ $line->debit > 0 ? 'text-primary' : 'text-muted' }}">
                                {{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}
                            </td>
                            <td class="text-end fw-bold {{ $line->credit > 0 ? 'text-danger' : 'text-muted' }}">
                                {{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Receipt / Slip Proof --}}
        @if($coaTransfer->attachment_path)
        <div class="mb-4 d-print-none">
            <h6 class="fw-bold small text-uppercase text-muted mb-2"><i class="fa-solid fa-paperclip me-1"></i>Attached Bank Slip / Receipt Proof:</h6>
            <div class="p-3 bg-light rounded border d-inline-block">
                @php
                    $isImg = in_array(strtolower(pathinfo($coaTransfer->attachment_path, PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp']);
                @endphp
                @if($isImg)
                    <a href="{{ $coaTransfer->attachment_url }}" target="_blank">
                        <img src="{{ $coaTransfer->attachment_url }}" alt="Transfer Slip Proof" style="max-height: 200px; max-width: 100%; border-radius: 6px; border: 1px solid #ced4da;">
                    </a>
                @else
                    <a href="{{ $coaTransfer->attachment_url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="fa-solid fa-file-pdf me-1"></i>View PDF Slip Document
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Signature Section --}}
        <div class="row mt-5 pt-4 border-top">
            <div class="col-4 text-center">
                <div class="border-bottom pb-4 mb-2"></div>
                <small class="fw-bold text-muted d-block">Prepared By</small>
                <small class="text-dark">{{ $coaTransfer->creator->name ?? 'Finance Officer' }}</small>
            </div>
            <div class="col-4 text-center">
                <div class="border-bottom pb-4 mb-2"></div>
                <small class="fw-bold text-muted d-block">Verified By</small>
                <small class="text-dark">Finance Head / Controller</small>
            </div>
            <div class="col-4 text-center">
                <div class="border-bottom pb-4 mb-2"></div>
                <small class="fw-bold text-muted d-block">Approved By</small>
                <small class="text-dark">General Manager (GM)</small>
            </div>
        </div>
    </div>
</div>
@endsection
