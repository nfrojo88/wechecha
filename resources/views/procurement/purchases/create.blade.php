@extends('layouts.app')

@section('title', 'Create Purchase Order')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('purchase-orders.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h1 class="h3 mb-0">Create Purchase Order</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('purchase-orders.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Associated Project (Optional)</label>
                    <select name="project_id" class="form-select @error('project_id') is-invalid @enderror">
                        <option value="">— Central / No specific project —</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}" @selected(old('project_id') == $project->id)>
                            {{ $project->name }} ({{ $project->code }})
                        </option>
                        @endforeach
                    </select>
                    @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Reference / PO Number <span class="text-danger">*</span></label>
                    <input type="text" name="reference_number" class="form-control @error('reference_number') is-invalid @enderror"
                           value="{{ old('reference_number', 'PO-'.date('Ym').'-'.rand(1000,9999)) }}" required>
                    @error('reference_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Supplier / Vendor Name <span class="text-danger">*</span></label>
                    <input type="text" name="supplier_name" class="form-control @error('supplier_name') is-invalid @enderror"
                           value="{{ old('supplier_name') }}" required>
                    @error('supplier_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Notes / Terms</label>
                    <textarea name="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Draft PO</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
