@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="h3 mb-0">
                <i class="fas fa-chart-line me-2"></i>Manpower Forecast & Planning
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('manpower-forecast.create') }}" class="btn btn-primary btn-sm me-2">
                <i class="fas fa-plus me-1"></i>New Forecast
            </a>
            <a href="{{ route('manpower-forecast.export') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-download me-1"></i>Export
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Total Forecasts</h6>
                    <h3 class="text-primary mb-0">{{ $stats['total_forecasts'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Pending Approval</h6>
                    <h3 class="text-warning mb-0">{{ $stats['pending_approval'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">This Week</h6>
                    <h3 class="text-info mb-0">{{ $stats['this_week'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Total Headcount</h6>
                    <h3 class="text-success mb-0">{{ $stats['total_headcount_forecast'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-select form-select-sm">
                        <option value="">All Projects</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Week Starting</label>
                    <input type="date" name="week_starting" class="form-control form-control-sm" value="{{ request('week_starting') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('manpower-forecast.index') }}" class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Forecasts Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Project</th>
                        <th>Week Starting</th>
                        <th>Designation</th>
                        <th class="text-center">Forecasted</th>
                        <th class="text-center">Assigned</th>
                        <th class="text-center">Hours</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($forecasts as $forecast)
                        <tr>
                            <td>
                                <strong>{{ $forecast->project->name }}</strong>
                            </td>
                            <td>
                                {{ $forecast->week_starting->format('M d, Y') }}
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $forecast->designation->name }}</span>
                            </td>
                            <td class="text-center">
                                <strong class="text-primary">{{ $forecast->forecasted_headcount }}</strong>
                            </td>
                            <td class="text-center">
                                <strong class="text-info">{{ $forecast->assignments()->count() }}</strong>
                            </td>
                            <td class="text-center">
                                {{ $forecast->forecasted_hours }}
                            </td>
                            <td>
                                @if ($forecast->status === 'draft')
                                    <span class="badge bg-secondary">Draft</span>
                                @elseif ($forecast->status === 'submitted')
                                    <span class="badge bg-warning">Submitted</span>
                                @elseif ($forecast->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('manpower-forecast.show', $forecast->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if ($forecast->status === 'draft')
                                    <form method="POST" action="{{ route('manpower-forecast.submit', $forecast->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary" title="Submit for approval">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>No forecasts found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $forecasts->links() }}
    </div>
</div>
@endsection
