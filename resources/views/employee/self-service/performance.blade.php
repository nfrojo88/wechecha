@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Performance Reviews</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    @if ($reviews->isEmpty())
    <div class="alert alert-info">
        <i class="fa-solid fa-info-circle"></i> No performance reviews found.
    </div>
    @else
    <div class="row">
        @foreach ($reviews as $review)
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="mb-1">Review Period: {{ $review->review_period }}</h5>
                            <small class="text-muted">Reviewed by: {{ $review->reviewer->name ?? 'N/A' }}</small>
                        </div>
                        <span class="badge badge-success">{{ $review->overall_rating }}/5</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted">Competencies</h6>
                        @if ($review->metrics && $review->metrics->count() > 0)
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ ($review->overall_rating / 5) * 100 }}%"></div>
                            </div>
                            <table class="table table-sm table-borderless">
                                @foreach ($review->metrics as $metric)
                                <tr>
                                    <td>{{ $metric->name }}</td>
                                    <td class="text-right">
                                        <span class="badge badge-primary">{{ $metric->rating }}/5</span>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        @endif
                    </div>

                    @if ($review->strengths)
                    <div class="mb-3">
                        <h6 class="text-muted small">Strengths</h6>
                        <p class="mb-0">{{ $review->strengths }}</p>
                    </div>
                    @endif

                    @if ($review->areas_for_improvement)
                    <div class="mb-3">
                        <h6 class="text-muted small">Areas for Improvement</h6>
                        <p class="mb-0">{{ $review->areas_for_improvement }}</p>
                    </div>
                    @endif

                    @if ($review->goals && $review->goals->count() > 0)
                    <div class="mb-3">
                        <h6 class="text-muted small">Performance Goals</h6>
                        <ul class="list-unstyled">
                            @foreach ($review->goals as $goal)
                            <li class="mb-2">
                                <i class="fa-solid fa-check-circle text-success"></i>
                                <strong>{{ $goal->title }}</strong>
                                <br>
                                <small class="text-muted">Target: {{ $goal->target_value }} | Actual: {{ $goal->actual_value }}</small>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-light">
                    <small class="text-muted">
                        Reviewed on: {{ $review->review_date->format('M d, Y') }}
                    </small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if ($reviews->hasPages())
    <div class="row mt-4">
        <div class="col-md-12">
            {{ $reviews->links() }}
        </div>
    </div>
    @endif
    @endif
</div>
@endsection
