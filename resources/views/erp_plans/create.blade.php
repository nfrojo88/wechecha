@extends('layouts.app')

@section('title', 'Create ERP Plan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-1">
            <i class="fa-solid fa-plus me-2 text-primary"></i>Create ERP Plan
        </h1>
        <p class="text-muted small mb-0">Define a new execution and resource plan for a project.</p>
    </div>
    <a href="{{ route('erp-plans.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('erp-plans.store') }}" method="POST">
                    @csrf

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Plan Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g. Ground Floor Execution Plan" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Project <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                <option value="">-- Select Project --</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                        @if($project->code) ({{ $project->code }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="plan_start_date" value="{{ old('plan_start_date') }}"
                                   class="form-control @error('plan_start_date') is-invalid @enderror"
                                   id="start_date" required>
                            @error('plan_start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="plan_end_date" value="{{ old('plan_end_date') }}"
                                   class="form-control @error('plan_end_date') is-invalid @enderror"
                                   id="end_date" required>
                            @error('plan_end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total Budget (ETB)</label>
                            <input type="number" step="0.01" name="total_budget" value="{{ old('total_budget') }}"
                                   class="form-control @error('total_budget') is-invalid @enderror"
                                   placeholder="0.00">
                            @error('total_budget')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 d-flex align-items-end">
                            <div class="w-100 bg-light rounded p-3 text-center">
                                <div class="small text-muted mb-1">Duration</div>
                                <div class="fw-bold fs-5" id="duration_display">— days</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" rows="3"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Brief overview of this execution plan...">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('erp-plans.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Save Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateDuration() {
        const start = document.getElementById('start_date').value;
        const end   = document.getElementById('end_date').value;
        const display = document.getElementById('duration_display');
        if (start && end) {
            const diff = Math.round((new Date(end) - new Date(start)) / (1000 * 60 * 60 * 24));
            display.textContent = diff >= 0 ? diff + ' days' : 'Invalid range';
            display.style.color = diff >= 0 ? '' : '#dc3545';
        } else {
            display.textContent = '— days';
        }
    }
    document.getElementById('start_date').addEventListener('change', updateDuration);
    document.getElementById('end_date').addEventListener('change', updateDuration);
</script>
@endpush
