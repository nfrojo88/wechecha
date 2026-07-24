@extends('layouts.app')
@section('title', 'Weekly Plan Setup')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">
                <i class="fa-solid fa-calendar-check text-success me-2"></i>Weekly Plan Setup
            </h1>
            <p class="text-muted mb-0 small">Define weekly work targets, task assignments, and dispatch plans</p>
        </div>
        <div>
            <a href="{{ route('dispatches.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                <i class="fa-solid fa-truck-fast me-1"></i>View Dispatches
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        {{-- Setup Form --}}
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success bg-opacity-10 border-0">
                    <h6 class="mb-0 fw-bold text-success">
                        <i class="fa-solid fa-pen-to-square me-2"></i>New Weekly Plan
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('planning-manager.weekly-plan-setup.store') }}" id="weeklyPlanForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Project <span class="text-danger">*</span></label>
                                <select name="project_id" class="form-select" required id="projectSelect">
                                    <option value="">-- Select Project --</option>
                                    @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Week Number <span class="text-danger">*</span></label>
                                <input type="number" name="week_number" class="form-control" min="1" max="52"
                                    value="{{ date('W') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" required
                                    value="{{ now()->startOfWeek()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control" required
                                    value="{{ now()->endOfWeek()->format('Y-m-d') }}">
                            </div>
                        </div>

                        <hr class="my-3">
                        <h6 class="fw-bold text-muted mb-3">
                            <i class="fa-solid fa-list-check me-2"></i>Weekly Work Targets
                        </h6>

                        <div id="targetRows">
                            <div class="target-row row g-2 mb-2 align-items-center">
                                <div class="col-md-5">
                                    <input type="text" name="target_items[]" class="form-control form-control-sm" placeholder="Activity (e.g. Column concreting - Block C)">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="target_quantities[]" class="form-control form-control-sm" placeholder="Target (e.g. 45 m3)">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="target_responsible[]" class="form-control form-control-sm" placeholder="Responsible (e.g. Site Eng.)">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-row" title="Remove">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline-success btn-sm mb-3" id="addTargetRow">
                            <i class="fa-solid fa-plus me-1"></i>Add Activity
                        </button>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes / Targets Summary <span class="text-danger">*</span></label>
                            <textarea name="targets" class="form-control" rows="3"
                                placeholder="e.g. Focus on Block-C columns. Rebar for slab to be completed by Thursday." required></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fa-solid fa-floppy-disk me-1"></i>Save Weekly Plan
                            </button>
                            <button type="reset" class="btn btn-outline-secondary px-4">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar info --}}
        <div class="col-md-5">
            {{-- Current Week Info --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary bg-opacity-10 border-0">
                    <h6 class="fw-bold mb-0 text-primary">
                        <i class="fa-solid fa-calendar-week me-2"></i>Current Week
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Week Number</span>
                        <span class="fw-bold fs-4 text-primary">{{ date('W') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Start</span>
                        <span class="fw-semibold">{{ now()->startOfWeek()->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">End</span>
                        <span class="fw-semibold">{{ now()->endOfWeek()->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Day of Week</span>
                        <span class="fw-semibold">{{ now()->format('l') }}</span>
                    </div>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="fw-bold mb-0"><i class="fa-solid fa-link me-2 text-secondary"></i>Quick Links</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('dispatches.index') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                        <i class="fa-solid fa-truck-fast text-primary"></i>
                        <span>Weekly Dispatches</span>
                        <i class="fa-solid fa-chevron-right ms-auto text-muted small"></i>
                    </a>
                    <a href="{{ route('erp-plans.index') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                        <i class="fa-solid fa-diagram-project text-purple" style="color:#7c3aed;"></i>
                        <span>ERP Plans</span>
                        <i class="fa-solid fa-chevron-right ms-auto text-muted small"></i>
                    </a>
                    <a href="{{ route('material-plans.index') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                        <i class="fa-solid fa-list-check text-success"></i>
                        <span>Material Plans</span>
                        <i class="fa-solid fa-chevron-right ms-auto text-muted small"></i>
                    </a>
                    <a href="{{ route('weekly-reports.create') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                        <i class="fa-solid fa-file-alt text-warning"></i>
                        <span>Submit Weekly Report</span>
                        <i class="fa-solid fa-chevron-right ms-auto text-muted small"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('addTargetRow').addEventListener('click', function() {
    const row = document.querySelector('.target-row').cloneNode(true);
    row.querySelectorAll('input').forEach(i => i.value = '');
    document.getElementById('targetRows').appendChild(row);
});
document.getElementById('targetRows').addEventListener('click', function(e) {
    if (e.target.closest('.remove-row')) {
        const rows = document.querySelectorAll('.target-row');
        if (rows.length > 1) e.target.closest('.target-row').remove();
    }
});
</script>
@endpush
@endsection
