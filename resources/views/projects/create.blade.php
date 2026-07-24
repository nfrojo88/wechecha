@extends('layouts.app')

@section('title', 'New Project')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h1 class="h3 mb-0">New Project</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('projects.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Project Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Project Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                           value="{{ old('code') }}" required>
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                {{-- Status is auto-managed by the planning workflow --}}
                <input type="hidden" name="status" value="planning">
                <div class="col-md-6">
                    <label class="form-label">Client Name</label>
                    <input type="text" name="client_name" class="form-control" value="{{ old('client_name') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Client Contact</label>
                    <input type="text" name="client_contact" class="form-control" value="{{ old('client_contact') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" value="{{ old('location') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Default Store</label>
                    <select name="default_store_id" class="form-select">
                        <option value="">— None —</option>
                        @foreach($stores as $store)
                        <option value="{{ $store->id }}" @selected(old('default_store_id') == $store->id)>
                            {{ $store->name }} ({{ $store->code }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-info border-0 mb-0 py-2 px-3 rounded-3 h-100 d-flex align-items-center" style="background:#eff6ff;">
                        <div class="small text-muted">
                            <i class="fa-solid fa-circle-info me-1 text-info"></i>
                            <strong>Contract Value</strong> will be sourced from the <strong>BOQ</strong> once created.<br>
                            <i class="fa-solid fa-circle-info me-1 text-info"></i>
                            <strong>Budget</strong> will be allocated by the <strong>GM</strong> during workflow approval.
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Project</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
