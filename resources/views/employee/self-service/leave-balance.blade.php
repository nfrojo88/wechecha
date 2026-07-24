@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Leave Balance</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    @if ($balances->isEmpty())
    <div class="alert alert-info">
        <i class="fa-solid fa-info-circle"></i> No leave balance records found for this year.
    </div>
    @else
    <div class="row">
        @foreach ($balances as $balance)
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ $balance->leaveType->name ?? 'Leave Type' }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center border-right">
                            <h6 class="text-muted small">Total Entitled</h6>
                            <h3 class="text-primary">{{ $balance->total_days }}</h3>
                        </div>
                        <div class="col-md-4 text-center border-right">
                            <h6 class="text-muted small">Used</h6>
                            <h3 class="text-danger">{{ $balance->used_days }}</h3>
                        </div>
                        <div class="col-md-4 text-center">
                            <h6 class="text-muted small">Available</h6>
                            <h3 class="text-success">{{ $balance->available_days }}</h3>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6 class="text-muted small mb-2">Usage Progress</h6>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" role="progressbar" 
                                 style="width: {{ ($balance->used_days / $balance->total_days) * 100 }}%"
                                 title="Used: {{ $balance->used_days }} days">
                            </div>
                        </div>
                        <small class="text-muted">{{ number_format(($balance->used_days / $balance->total_days) * 100, 1) }}% used</small>
                    </div>

                    @if ($balance->leaveType && $balance->leaveType->description)
                    <div class="mt-3">
                        <h6 class="text-muted small">Description</h6>
                        <p class="text-sm mb-0">{{ $balance->leaveType->description }}</p>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-light">
                    <small class="text-muted">
                        Last updated: {{ $balance->updated_at->format('M d, Y') }}
                    </small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Summary -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Annual Leave Summary - {{ now()->year }}</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h6 class="text-muted">Total Entitled Days</h6>
                            <h3 class="text-primary">{{ $balances->sum('total_days') }}</h3>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Total Used Days</h6>
                            <h3 class="text-danger">{{ $balances->sum('used_days') }}</h3>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Total Available Days</h6>
                            <h3 class="text-success">{{ $balances->sum('available_days') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Request Button -->
    <div class="row mt-4 mb-4">
        <div class="col-md-12">
            <a href="{{ route('leave-requests.create') }}" class="btn btn-lg btn-primary">
                <i class="fa-solid fa-plus"></i> Request New Leave
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
