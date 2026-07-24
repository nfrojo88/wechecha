@extends('layouts.app')

@section('title', 'Edit BOQ')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('boqs.show', $boq) }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h1 class="h3 mb-0">Edit BOQ: {{ $boq->reference_number }}</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('boqs.update', $boq) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Associated Project</label>
                    <input type="text" class="form-control" value="{{ $boq->project->name }}" disabled>
                    <div class="form-text">Project cannot be changed once drafted.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Reference Number <span class="text-danger">*</span></label>
                    <input type="text" name="reference_number" class="form-control @error('reference_number') is-invalid @enderror"
                           value="{{ old('reference_number', $boq->reference_number) }}" required>
                    @error('reference_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">BOQ Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $boq->title) }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Description / Scope of Work</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description', $boq->description) }}</textarea>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('boqs.show', $boq) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
