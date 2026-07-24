@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">My Contracts</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    @if ($contracts->isEmpty())
    <div class="alert alert-info">
        <i class="fa-solid fa-info-circle"></i> No employment contracts found.
    </div>
    @else
    <div class="row">
        @foreach ($contracts as $contract)
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="mb-1">Contract #{{ $contract->contract_number }}</h5>
                            <small class="text-muted">{{ $contract->contract_type }}</small>
                        </div>
                        <span class="badge 
                            @if ($contract->status === 'active') badge-success
                            @elseif ($contract->status === 'expired') badge-danger
                            @elseif ($contract->status === 'pending') badge-warning
                            @else badge-secondary
                            @endif">
                            {{ ucfirst($contract->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted small">Dates</h6>
                        <p class="mb-1">
                            <i class="fa-solid fa-calendar"></i>
                            <strong>Start:</strong> {{ $contract->start_date->format('M d, Y') }}
                        </p>
                        <p class="mb-0">
                            <i class="fa-solid fa-calendar"></i>
                            <strong>End:</strong> {{ $contract->end_date->format('M d, Y') }}
                        </p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted small">Position Details</h6>
                        <p class="mb-1">
                            <strong>Position:</strong> {{ $contract->position }}
                        </p>
                        <p class="mb-0">
                            <strong>Department:</strong> {{ $contract->department }}
                        </p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted small">Compensation</h6>
                        <p class="mb-1">
                            <strong>Annual CTC:</strong> {{ number_format($contract->annual_ctc, 2) }}
                        </p>
                        <p class="mb-0">
                            <strong>Monthly Salary:</strong> {{ number_format($contract->monthly_salary, 2) }}
                        </p>
                    </div>

                    @if ($contract->benefits)
                    <div class="mb-3">
                        <h6 class="text-muted small">Benefits</h6>
                        <p class="mb-0">{{ $contract->benefits }}</p>
                    </div>
                    @endif

                    @if ($contract->contract_file)
                    <div class="alert alert-light">
                        <a href="{{ route('employee.contract.download', $contract) }}" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-download"></i> Download Contract PDF
                        </a>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-light">
                    <small class="text-muted">
                        Last updated: {{ $contract->updated_at->format('M d, Y') }}
                    </small>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
