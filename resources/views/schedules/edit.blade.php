@extends('layouts.app')

@section('title', 'Edit Schedule')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('schedules.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h1 class="h3 mb-0">Edit Schedule: {{ $schedule->name }}</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('schedules.update', $schedule) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Associated Project <span class="text-danger">*</span></label>
                    <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}" @selected(old('project_id', $schedule->project_id) == $project->id)>
                            {{ $project->name }} ({{ $project->code }})
                        </option>
                        @endforeach
                    </select>
                    @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Schedule Name / Phase <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $schedule->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                           value="{{ old('start_date', $schedule->start_date->format('Y-m-d')) }}" required>
                    @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                           value="{{ old('end_date', $schedule->end_date->format('Y-m-d')) }}" required>
                    @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="draft" @selected(old('status', $schedule->status) == 'draft')>Draft</option>
                        <option value="active" @selected(old('status', $schedule->status) == 'active')>Active</option>
                        <option value="delayed" @selected(old('status', $schedule->status) == 'delayed')>Delayed</option>
                        <option value="completed" @selected(old('status', $schedule->status) == 'completed')>Completed</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Progress (%) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" max="100" name="progress" class="form-control @error('progress') is-invalid @enderror"
                           value="{{ old('progress', $schedule->progress) }}" required>
                    @error('progress')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('schedules.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
