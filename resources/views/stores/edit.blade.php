@extends('layouts.app')

@section('title', 'Edit Store')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('stores.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h1 class="h3 mb-0">Edit Store: {{ $store->name }}</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('stores.update', $store) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Store Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $store->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Store Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                           value="{{ old('code', $store->code) }}" required>
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="site" @selected(old('type', $store->type) == 'site')>Project Site</option>
                        <option value="warehouse" @selected(old('type', $store->type) == 'warehouse')>Central Warehouse</option>
                        <option value="yard" @selected(old('type', $store->type) == 'yard')>Equipment Yard</option>
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Assigned Project</label>
                    <select name="project_id" class="form-select">
                        <option value="">— None (Central Store) —</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}" @selected(old('project_id', $store->project_id) == $project->id)>
                            {{ $project->name }} ({{ $project->code }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Assigned Store Keeper</label>
                    <select name="manager_id" class="form-select">
                        <option value="">— Unassigned —</option>
                        @foreach($managers as $manager)
                        <option value="{{ $manager->id }}" @selected(old('manager_id', $store->manager_id) == $manager->id)>
                            {{ $manager->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-12">
                    <label class="form-label">Physical Address</label>
                    <textarea name="address" rows="2" class="form-control">{{ old('address', $store->address) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2" class="form-control">{{ old('notes', $store->notes) }}</textarea>
                </div>
                
                <div class="col-12 mt-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" @checked(old('is_active', $store->is_active))>
                        <label class="form-check-label" for="isActive">Store is active</label>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('stores.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
