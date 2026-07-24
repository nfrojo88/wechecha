@extends('layouts.app')
@section('title', 'Weekly Progress Report')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Weekly Report: {{ $weeklyReport->week_start->format('M d') }} - {{ $weeklyReport->week_end->format('M d, Y') }}</h1>
        <div>
            <a href="{{ route('weekly-reports.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
            </a>
            <button class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-print"></i> Print Report</button>
        </div>
    </div>

    <div class="row">
        <!-- Project & Status Info -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Project</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $weeklyReport->project->name }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Status</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">{{ ucfirst($weeklyReport->status) }}</div>
                            <div class="small text-muted mt-1">By: {{ $weeklyReport->createdBy->name ?? 'System' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-info-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Planned Progress</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ number_format($weeklyReport->planned_progress_percent, 1) }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-success" role="progressbar" @style(["width: {$weeklyReport->planned_progress_percent}%"])></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullseye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Actual Progress</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ number_format($weeklyReport->actual_progress_percent, 1) }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-warning" role="progressbar" @style(["width: {$weeklyReport->actual_progress_percent}%"])></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-alt mr-2"></i> Executive Summary</h6>
                </div>
                <div class="card-body">
                    <p class="text-justify">{{ $weeklyReport->executive_summary ?? 'No executive summary provided.' }}</p>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-forward mr-2"></i> Plan for Next Week</h6>
                </div>
                <div class="card-body">
                    <p class="text-justify">{{ $weeklyReport->next_week_plan ?? 'No plan outlined for next week.' }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-exclamation-triangle mr-2"></i> Critical Issues & Delays</h6>
                </div>
                <div class="card-body">
                    @if($weeklyReport->critical_issues)
                        <div class="alert alert-danger border-left-danger bg-white text-dark shadow-sm">
                            {!! nl2br(e($weeklyReport->critical_issues)) !!}
                        </div>
                    @else
                        <p class="text-muted">No critical issues or delays reported this week.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
