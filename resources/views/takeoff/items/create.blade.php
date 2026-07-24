@extends('layouts.app')

@section('title', 'Add Item to Takeoff')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-plus me-2 text-primary"></i>Add Takeoff Item
        </h1>
        <p class="text-muted small mb-0 mt-1">Adding an item to "{{ $takeoff->title }}"</p>
    </div>
    <a href="{{ route('takeoff.show', $takeoff) }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Sheet
    </a>
</div>

<div class="card border-0">
    <div class="card-header bg-white">
        <span class="fw-bold" style="color:var(--gray-700);">
            <i class="fa-solid fa-cube me-2 text-primary"></i>Item Details
        </span>
    </div>
    <div class="card-body">
        <form action="{{ route('takeoff.items.store', $takeoff) }}" method="POST">
            @csrf
            
            <div class="row g-4">
                <div class="col-md-12">
                    <label for="element" class="form-label">Item Description (Element) <span class="text-danger">*</span></label>
                    <input type="text" name="element" id="element" class="form-control @error('element') is-invalid @enderror" 
                           value="{{ old('element') }}" placeholder="e.g. Ground Floor Column C1" required>
                    @error('element')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="dimensions" class="form-label">Dimensions / Notes</label>
                    <input type="text" name="dimensions" id="dimensions" class="form-control @error('dimensions') is-invalid @enderror" 
                           value="{{ old('dimensions') }}" placeholder="e.g. 0.40 x 0.40 x 3.00">
                    @error('dimensions')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="formula" class="form-label">Calculation Formula</label>
                    <input type="text" name="formula" id="formula" class="form-control @error('formula') is-invalid @enderror" 
                           value="{{ old('formula') }}" placeholder="e.g. 4 * (0.4 * 0.4 * 3.0)">
                    @error('formula')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="result_quantity" class="form-label">Total Quantity <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="result_quantity" id="result_quantity" class="form-control @error('result_quantity') is-invalid @enderror" 
                           value="{{ old('result_quantity') }}" required>
                    @error('result_quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="result_unit" class="form-label">Unit <span class="text-danger">*</span></label>
                    <select name="result_unit" id="result_unit" class="form-select @error('result_unit') is-invalid @enderror" required>
                        <option value="">-- Select Unit --</option>
                        <option value="m3" {{ old('result_unit') == 'm3' ? 'selected' : '' }}>Cubic Meter (m³)</option>
                        <option value="m2" {{ old('result_unit') == 'm2' ? 'selected' : '' }}>Square Meter (m²)</option>
                        <option value="m" {{ old('result_unit') == 'm' ? 'selected' : '' }}>Linear Meter (m)</option>
                        <option value="kg" {{ old('result_unit') == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                        <option value="ton" {{ old('result_unit') == 'ton' ? 'selected' : '' }}>Ton</option>
                        <option value="pcs" {{ old('result_unit') == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                        <option value="ls" {{ old('result_unit') == 'ls' ? 'selected' : '' }}>Lump Sum (LS)</option>
                    </select>
                    @error('result_unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr class="my-4" style="border-color:var(--gray-200);">
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('takeoff.show', $takeoff) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Item
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
