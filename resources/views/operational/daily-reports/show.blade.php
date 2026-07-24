@extends('layouts.app')
@section('title', 'Daily Report Details')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daily Report: {{ $dailyReport->report_date->format('M d, Y') }}</h1>
        <div>
            <a href="{{ route('daily-reports.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
            </a>
            <button class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-print"></i> Print</button>
        </div>
    </div>

    <div class="row">
        <!-- Site Conditions & Info -->
        <div class="col-lg-4">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Site Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th>Project:</th>
                            <td>{{ $dailyReport->project->name }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($dailyReport->status == 'draft') <span class="badge badge-secondary">Draft</span>
                                @elseif($dailyReport->status == 'submitted') <span class="badge badge-info">Submitted</span>
                                @elseif($dailyReport->status == 'approved') <span class="badge badge-success">Approved</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Submitted By:</th>
                            <td>{{ $dailyReport->createdBy->name ?? 'Unknown' }}</td>
                        </tr>
                        <tr>
                            <th>Total Manpower:</th>
                            <td><span class="badge badge-primary" style="font-size:14px">{{ $dailyReport->total_manpower }}</span></td>
                        </tr>
                        <tr>
                            <th>Weather:</th>
                            <td>
                                @if($dailyReport->weather_conditions == 'Sunny') <i class="fas fa-sun text-warning"></i>
                                @elseif($dailyReport->weather_conditions == 'Rainy') <i class="fas fa-cloud-rain text-info"></i>
                                @elseif($dailyReport->weather_conditions == 'Cloudy') <i class="fas fa-cloud text-secondary"></i>
                                @endif
                                {{ $dailyReport->weather_conditions ?? '-' }} 
                                @if($dailyReport->temperature) ({{ $dailyReport->temperature }}°C) @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">General Comments</h6>
                </div>
                <div class="card-body">
                    <strong>General Notes & Progress:</strong>
                    <p class="text-muted small">{{ $dailyReport->general_notes ?? 'No general notes.' }}</p>
                    <hr>
                    <strong class="text-danger">Safety Incidents:</strong>
                    <p class="text-muted small">{{ $dailyReport->safety_incidents ?? 'None reported.' }}</p>
                </div>
            </div>
        </div>

        <!-- Task Details -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tasks Performed</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Work Description</th>
                                <th>Qty Done</th>
                                <th>Workers</th>
                                <th>Equipment Used</th>
                                <th>Issues/Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailyReport->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->work_description }}</td>
                                <td>{{ $item->qty_completed > 0 ? number_format($item->qty_completed, 2) : '-' }}</td>
                                <td>{{ $item->workers_count > 0 ? $item->workers_count : '-' }}</td>
                                <td>{{ $item->equipment_used ?? '-' }}</td>
                                <td>{{ $item->issues ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No tasks recorded for this day.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
