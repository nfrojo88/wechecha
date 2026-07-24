@extends('layouts.app')
@section('title', 'Add Department')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-building me-2"></i>Add Department</h1>
        <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card shadow-sm" style="max-width:600px">
        <div class="card-body">
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small text-uppercase" style="letter-spacing: 0.5px;">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-lg rounded-3" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small text-uppercase" style="letter-spacing: 0.5px;">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control form-control-lg rounded-3" value="{{ old('code') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold text-secondary small text-uppercase" style="letter-spacing: 0.5px;">Description</label>
                        <textarea name="description" class="form-control form-control-lg rounded-3" rows="3">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button><a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
            </form>
        </div>
    </div>
</div>
@endsection
