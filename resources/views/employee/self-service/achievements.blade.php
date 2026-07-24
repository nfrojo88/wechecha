@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Achievements & Recognition</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    @if ($achievements->isEmpty())
    <div class="alert alert-info">
        <i class="fa-solid fa-info-circle"></i> No achievements or recognition yet. Keep up the great work!
    </div>
    @else
    <div class="row">
        @foreach ($achievements as $achievement)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="mb-0">{{ $achievement->title }}</h5>
                        <i class="fa-solid fa-trophy text-warning fa-lg"></i>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">{{ $achievement->description }}</p>

                    @if ($achievement->recognition_type)
                    <p class="mb-2">
                        <strong>Recognition Type:</strong>
                        <span class="badge badge-info">{{ ucfirst($achievement->recognition_type) }}</span>
                    </p>
                    @endif

                    @if ($achievement->award_amount)
                    <p class="mb-2">
                        <strong>Award Amount:</strong>
                        <span class="text-success">{{ number_format($achievement->award_amount, 2) }}</span>
                    </p>
                    @endif

                    @if ($achievement->awarded_by)
                    <p class="mb-2">
                        <strong>Awarded By:</strong>
                        {{ $achievement->awardedBy->name ?? 'N/A' }}
                    </p>
                    @endif

                    <p class="mb-0">
                        <strong>Date:</strong>
                        {{ $achievement->achievement_date->format('M d, Y') }}
                    </p>
                </div>
                <div class="card-footer bg-light">
                    <small class="text-muted">
                        Added on: {{ $achievement->created_at->format('M d, Y') }}
                    </small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if ($achievements->hasPages())
    <div class="row mt-4">
        <div class="col-md-12">
            {{ $achievements->links() }}
        </div>
    </div>
    @endif
    @endif
</div>
@endsection
