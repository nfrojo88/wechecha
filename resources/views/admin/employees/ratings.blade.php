@extends('layouts.app')
@section('title', 'Employee Ratings')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fa-solid fa-star me-2"></i>Employee Ratings</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Rate Employees</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Employee Name</th>
                            <th>Department & Role</th>
                            <th>Avg Rating</th>
                            <th>Recent Ratings</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $emp)
                        <tr>
                            <td>
                                <div class="font-weight-bold">{{ $emp->full_name ?? $emp->user->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-muted">{{ $emp->employee_code }}</div>
                            </td>
                            <td>
                                <div>{{ $emp->department ?? 'No Department' }}</div>
                                <div class="text-xs text-muted">{{ $emp->role_title ?? 'No Title' }}</div>
                            </td>
                            <td>
                                @if($emp->avg_rating)
                                    <div class="d-flex align-items-center">
                                        <span class="h5 mb-0 text-warning me-2">{{ number_format($emp->avg_rating, 1) }}</span>
                                        <div class="text-xs text-muted">({{ $emp->ratings_count }} reviews)</div>
                                    </div>
                                @else
                                    <span class="text-muted text-sm">Not rated</span>
                                @endif
                            </td>
                            <td>
                                @if($emp->ratings->count() > 0)
                                    <div style="font-size: 11px;">
                                        @foreach($emp->ratings as $r)
                                            <div class="mb-1 text-truncate" style="max-width: 250px;" title="{{ $r->comment }}">
                                                {!! $r->stars_html !!} 
                                                <span class="text-muted ms-1">{{ $r->period }} - {{ ucfirst($r->category) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted text-xs">No recent ratings</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#rateModal{{ $emp->id }}">
                                    <i class="fa-solid fa-star"></i> Rate
                                </button>
                            </td>
                        </tr>

                        <!-- Rate Modal -->
                        <div class="modal fade" id="rateModal{{ $emp->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.employee-ratings.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="employee_id" value="{{ $emp->id }}">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Rate Employee: {{ $emp->full_name ?? $emp->user->name ?? '' }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Category</label>
                                                <select name="category" class="form-select" required>
                                                    <option value="overall">Overall Performance</option>
                                                    <option value="attendance">Attendance & Punctuality</option>
                                                    <option value="performance">Task Execution / Performance</option>
                                                    <option value="attitude">Attitude & Teamwork</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Rating (1 to 5 Stars)</label>
                                                <select name="rating" class="form-select" required>
                                                    <option value="5">⭐⭐⭐⭐⭐ 5 - Excellent</option>
                                                    <option value="4">⭐⭐⭐⭐ 4 - Good</option>
                                                    <option value="3" selected>⭐⭐⭐ 3 - Average</option>
                                                    <option value="2">⭐⭐ 2 - Poor</option>
                                                    <option value="1">⭐ 1 - Very Poor</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Period (e.g., Q2 2026, July 2026)</label>
                                                <input type="text" name="period" class="form-control" value="{{ now()->format('F Y') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Feedback / Comments (Optional)</label>
                                                <textarea name="comment" class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Submit Rating</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if($employees->hasPages())
        <div class="card-footer">
            {{ $employees->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
