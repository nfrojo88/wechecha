@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="h3 mb-0">
                <i class="fas fa-chart-bar me-2"></i>Performance Dashboard
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('performance-dashboard.create-review') }}" class="btn btn-primary btn-sm me-2">
                <i class="fas fa-plus me-1"></i>New Review
            </a>
            <a href="{{ route('performance-dashboard.analytics') }}" class="btn btn-outline-primary btn-sm me-2">
                <i class="fas fa-chart-line me-1"></i>Analytics
            </a>
            <a href="{{ route('performance-dashboard.export') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-download me-1"></i>Export
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Total Reviewed</h6>
                    <h3 class="text-success mb-0">{{ $stats['total_reviewed'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Pending Review</h6>
                    <h3 class="text-warning mb-0">{{ $stats['pending_review'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Submitted</h6>
                    <h3 class="text-info mb-0">{{ $stats['submitted_for_approval'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Avg Score</h6>
                    <h3 class="text-primary mb-0">{{ number_format($stats['avg_score'] ?? 0, 2) }}/5.0</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Employee</label>
                    <select name="employee_id" class="form-select form-select-sm">
                        <option value="">All Employees</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('performance-dashboard.index') }}" class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Period</th>
                        <th class="text-center">Overall Score</th>
                        <th class="text-center">Technical</th>
                        <th class="text-center">Soft Skills</th>
                        <th class="text-center">Attendance</th>
                        <th class="text-center">Productivity</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reviews as $review)
                        <tr>
                            <td>
                                <strong>{{ $review->employee->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $review->employee->code }}</small>
                            </td>
                            <td>
                                {{ $review->review_period->format('M Y') }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $review->rating_badge }} fs-6">
                                    {{ number_format($review->overall_score, 1) }}
                                </span>
                            </td>
                            <td class="text-center">
                                {{ number_format($review->technical_skills_score, 1) }}
                            </td>
                            <td class="text-center">
                                {{ number_format($review->soft_skills_score, 1) }}
                            </td>
                            <td class="text-center">
                                {{ number_format($review->attendance_score, 1) }}
                            </td>
                            <td class="text-center">
                                {{ number_format($review->productivity_score, 1) }}
                            </td>
                            <td>
                                @if ($review->status === 'draft')
                                    <span class="badge bg-secondary">Draft</span>
                                @elseif ($review->status === 'submitted')
                                    <span class="badge bg-warning">Submitted</span>
                                @else
                                    <span class="badge bg-success">Approved</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('performance-dashboard.show-review', $review->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if ($review->status === 'draft')
                                    <form method="POST" action="{{ route('performance-dashboard.submit-review', $review->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary" title="Submit">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </form>
                                @elseif ($review->status === 'submitted')
                                    <form method="POST" action="{{ route('performance-dashboard.approve-review', $review->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>No performance reviews found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $reviews->links() }}
    </div>
</div>
@endsection
