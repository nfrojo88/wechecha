@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary me-3"><i class="fa-solid fa-arrow-left"></i></a>
    <h1 class="h3 mb-0">Payment: {{ $payment->reference_number }}</h1>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white py-3 text-center">
                <div class="small opacity-75">{{ \App\Models\Payment::TYPES[$payment->payment_type] ?? $payment->payment_type }}</div>
                <div class="fs-1 fw-bold">{{ number_format($payment->amount, 2) }} <small class="fs-5">ETB</small></div>
                <div class="small opacity-75">Received on {{ $payment->payment_date->format('d M Y') }}</div>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-muted w-35">Reference</td>
                        <td class="font-monospace fw-semibold">{{ $payment->reference_number }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Project</td>
                        <td class="fw-semibold">
                            <a href="{{ route('projects.show', $payment->project) }}" class="text-decoration-none">
                                {{ $payment->project->name }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Type</td>
                        <td><span class="badge bg-info bg-opacity-75">{{ \App\Models\Payment::TYPES[$payment->payment_type] }}</span></td>
                    </tr>
                    @if($payment->description)
                    <tr>
                        <td class="text-muted">Description</td>
                        <td>{{ $payment->description }}</td>
                    </tr>
                    @endif
                    @if($payment->notes)
                    <tr>
                        <td class="text-muted">Notes</td>
                        <td class="text-muted small">{{ $payment->notes }}</td>
                    </tr>
                    @endif
                    <tr class="border-top">
                        <td class="text-muted">Recorded By</td>
                        <td class="small">{{ $payment->creator->name }} · {{ $payment->created_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
