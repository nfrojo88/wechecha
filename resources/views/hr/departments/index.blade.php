@extends('layouts.app')
@section('title', 'Departments')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-building me-2"></i>Departments</h1>
        <a href="{{ route('departments.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Department</a>
    </div>
    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    <div class="row g-3">
        @forelse($departments as $dept)
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold"><i class="fas fa-building text-primary me-2"></i>{{ $dept->name }}</h5>
                    <span class="badge bg-secondary mb-2">{{ $dept->code }}</span>
                    @if($dept->head)<p class="mb-1 text-muted small"><i class="fas fa-user me-1"></i>Head: {{ $dept->head->first_name }} {{ $dept->head->last_name }}</p>@endif
                    <p class="mb-1 text-muted small"><i class="fas fa-users me-1"></i>{{ $dept->employees->count() }} employees</p>
                    @if($dept->description)<p class="mb-0 text-muted small">{{ Str::limit($dept->description, 80) }}</p>@endif
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <span class="badge bg-{{ $dept->is_active ? 'success' : 'secondary' }}">{{ $dept->is_active ? 'Active' : 'Inactive' }}</span>
                    <a href="{{ route('departments.edit', $dept) }}" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit me-1"></i>Edit</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12"><div class="alert alert-info">No departments yet. <a href="{{ route('departments.create') }}">Create one</a>.</div></div>
        @endforelse
    </div>
</div>
@endsection
