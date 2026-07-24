@extends('layouts.app')
@section('title', 'New Transfer')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-exchange-alt me-2"></i>New Store Transfer</h1>
        <a href="{{ route('transfers.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </div>
    <form action="{{ route('transfers.store') }}" method="POST" id="transferForm">
        @csrf
        <div class="row g-3">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">Transfer Details</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">From Store <span class="text-danger">*</span></label>
                                <select name="from_store_id" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">To Store <span class="text-danger">*</span></label>
                                <select name="to_store_id" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Required Date</label>
                                <input type="date" name="required_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Reason</label>
                                <input type="text" name="reason" class="form-control" placeholder="Reason for transfer">
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
                        <table class="table align-middle mb-0" id="itemsTable">
                            <thead class="table-light"><tr><th>Product</th><th>Quantity</th><th>Unit</th><th></th></tr></thead>
                            <tbody id="itemsBody">
                                <tr id="itemRow_0">
                                    <td><select name="items[0][product_id]" class="form-select" required><option value="">-- Select Product --</option>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code }})</option>@endforeach</select></td>
                                    <td><input type="number" name="items[0][quantity]" class="form-control" min="0.001" step="0.001" required></td>
                                    <td><input type="text" name="items[0][unit]" class="form-control" placeholder="e.g. kg" required></td>
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
                        <button type="submit" class="btn btn-primary w-100 mb-2"><i class="fas fa-paper-plane me-1"></i>Create Transfer</button>
                        <a href="{{ route('transfers.index') }}" class="btn btn-outline-secondary w-100">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@push('scripts')
<script>
let rowIdx = 1;
document.getElementById('addItem').addEventListener('click', function() {
    const products = `@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code }})</option>@endforeach`;
    const row = `<tr id="itemRow_${rowIdx}">
        <td><select name="items[${rowIdx}][product_id]" class="form-select" required><option value="">-- Select --</option>${products}</select></td>
        <td><input type="number" name="items[${rowIdx}][quantity]" class="form-control" min="0.001" step="0.001" required></td>
        <td><input type="text" name="items[${rowIdx}][unit]" class="form-control" placeholder="e.g. kg" required></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="fas fa-times"></i></button></td>
    </tr>`;
    document.getElementById('itemsBody').insertAdjacentHTML('beforeend', row);
    rowIdx++;
});
document.getElementById('itemsBody').addEventListener('click', function(e) {
    if (e.target.closest('.remove-row')) e.target.closest('tr').remove();
});
</script>
@endpush
@endsection
