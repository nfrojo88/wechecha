@extends('layouts.app')
@section('title', 'Create Purchase Request')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-file-invoice me-2"></i>New Purchase Request</h1>
        <a href="{{ route('purchase-requests.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </div>
    <form action="{{ route('purchase-requests.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">Request Details</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Project <span class="text-danger">*</span></label>
                                <select name="project_id" class="form-select" required>
                                    <option value="">-- Select Project --</option>
                                    @foreach($projects as $p)<option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Store</label>
                                <select name="store_id" class="form-select">
                                    <option value="">-- Select Store --</option>
                                    @foreach($stores as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Priority</label>
                                <select name="priority" class="form-select">
                                    <option value="normal">Normal</option><option value="high">High</option><option value="urgent">Urgent</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Type</label>
                                <select name="type" class="form-select">
                                    <option value="normal">Normal</option><option value="emergency">Emergency</option><option value="direct">Direct</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Required Date</label>
                                <input type="date" name="required_date" class="form-control" value="{{ old('required_date') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Justification</label>
                                <textarea name="justification" class="form-control" rows="2">{{ old('justification') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm mt-3">
                    <div class="card-header d-flex justify-content-between fw-semibold">
                        <span>Items</span>
                        <button type="button" class="btn btn-sm btn-success" id="addItem"><i class="fas fa-plus me-1"></i>Add Item</button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table align-middle mb-0">
                            <thead class="table-light"><tr><th>Product</th><th>Qty</th><th>Unit</th><th>Est. Cost/Unit</th><th></th></tr></thead>
                            <tbody id="itemsBody">
                                <tr>
                                    <td><select name="items[0][product_id]" class="form-select" required><option value="">-- Product --</option>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></td>
                                    <td><input type="number" name="items[0][quantity]" class="form-control" min="0.001" step="0.001" required></td>
                                    <td><input type="text" name="items[0][unit]" class="form-control" required></td>
                                    <td><input type="number" name="items[0][estimated_unit_cost]" class="form-control" step="0.01"></td>
                                    <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="fas fa-times"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save me-1"></i>Save Purchase Request</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@push('scripts')
<script>
let idx = 1;
document.getElementById('addItem').addEventListener('click', function() {
    const opts = `@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach`;
    const r = `<tr><td><select name="items[${idx}][product_id]" class="form-select" required><option value="">-- Product --</option>${opts}</select></td><td><input type="number" name="items[${idx}][quantity]" class="form-control" min="0.001" step="0.001" required></td><td><input type="text" name="items[${idx}][unit]" class="form-control" required></td><td><input type="number" name="items[${idx}][estimated_unit_cost]" class="form-control" step="0.01"></td><td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="fas fa-times"></i></button></td></tr>`;
    document.getElementById('itemsBody').insertAdjacentHTML('beforeend', r);
    idx++;
});
document.getElementById('itemsBody').addEventListener('click', e => { if(e.target.closest('.remove-row')) e.target.closest('tr').remove(); });
</script>
@endpush
@endsection
