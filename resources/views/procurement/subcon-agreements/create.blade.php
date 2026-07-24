@extends('layouts.app')
@section('title', 'Create Subcontractor Agreement')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-handshake me-2 text-primary"></i>Create Subcontractor Agreement
            </h1>
            <p class="text-muted mt-1">Define work scope from takeoff or manual items</p>
        </div>
        <a href="{{ route('subcon-agreements.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
    </div>

    <form action="{{ route('subcon-agreements.store') }}" method="POST" id="subconForm">
        @csrf

        <!-- Basic Information -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0 font-weight-bold">Basic Information</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Project <span class="text-danger">*</span></label>
                        <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                            <option value="{{ $project->id }}" @selected(old('project_id')==$project->id)>
                                {{ $project->project_name ?? $project->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Supplier/Subcontractor <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id')==$supplier->id)>
                                {{ $supplier->name }} ({{ $supplier->contact_person ?? 'N/A' }})
                            </option>
                            @endforeach
                        </select>
                        @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Work Description <span class="text-danger">*</span></label>
                        <textarea name="work_description" class="form-control @error('work_description') is-invalid @enderror" 
                                  rows="3" required>{{ old('work_description') }}</textarea>
                        @error('work_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                               value="{{ old('start_date') }}" required>
                        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">End Date <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                               value="{{ old('end_date') }}" required>
                        @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Link to Takeoff (Optional)</label>
                        <select name="takeoff_sheet_id" class="form-select" onchange="loadTakeoffItems(this.value)">
                            <option value="">No Takeoff</option>
                            @foreach($takeoffs as $takeoff)
                            <option value="{{ $takeoff->id }}">
                                {{ $takeoff->project->project_name ?? 'N/A' }} ({{ $takeoff->created_at->format('M d, Y') }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Takeoff Items Selection (if selecting takeoff) -->
        <div class="card shadow-sm mb-4" id="takeoffItemsCard" style="display: none;">
            <div class="card-header bg-light">
                <h6 class="mb-0 font-weight-bold">Select Takeoff Items</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="takeoffItemsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">
                                    <input type="checkbox" id="selectAllTakeoff" class="form-check-input">
                                </th>
                                <th>Description</th>
                                <th class="text-center">Qty</th>
                                <th>Unit</th>
                                <th>Est. Rate</th>
                                <th>Your Rate</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="takeoffItemsBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Manual Items -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0 font-weight-bold">Work Items</h6>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addManualItem()">
                    <i class="fas fa-plus me-1"></i>Add Item
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Task Description</th>
                                <th class="text-center">Qty</th>
                                <th>Unit</th>
                                <th>Unit Rate</th>
                                <th>Total</th>
                                <th style="width: 50px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <tr class="item-row">
                                <td>
                                    <input type="text" name="items[0][task_description]" class="form-control form-control-sm" required>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]" class="form-control form-control-sm" step="0.01" min="0.01" required>
                                </td>
                                <td>
                                    <input type="text" name="items[0][unit]" class="form-control form-control-sm" placeholder="e.g., m, ft, days" required>
                                </td>
                                <td>
                                    <input type="number" name="items[0][unit_rate]" class="form-control form-control-sm" step="0.01" min="0" required>
                                </td>
                                <td class="item-total">0.00</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Manual Items Total
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="manualTotal">0.00</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Takeoff Items Total
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="takeoffTotal">0.00</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Grand Total
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="grandTotal">0.00</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('subcon-agreements.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Create Agreement
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let itemCounter = 1;

function addManualItem() {
    const tbody = document.getElementById('itemsBody');
    const row = document.createElement('tr');
    row.className = 'item-row';
    row.innerHTML = `
        <td>
            <input type="text" name="items[${itemCounter}][task_description]" class="form-control form-control-sm" required>
        </td>
        <td>
            <input type="number" name="items[${itemCounter}][quantity]" class="form-control form-control-sm quantity" step="0.01" min="0.01" required onchange="updateItemTotal(this)">
        </td>
        <td>
            <input type="text" name="items[${itemCounter}][unit]" class="form-control form-control-sm" placeholder="e.g., m, ft, days" required>
        </td>
        <td>
            <input type="number" name="items[${itemCounter}][unit_rate]" class="form-control form-control-sm unit_rate" step="0.01" min="0" required onchange="updateItemTotal(this)">
        </td>
        <td class="item-total">0.00</td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    itemCounter++;
}

function removeItem(btn) {
    btn.closest('tr').remove();
    updateManualTotal();
}

function updateItemTotal(input) {
    const row = input.closest('tr');
    const qty = parseFloat(row.querySelector('.quantity')?.value || 0);
    const rate = parseFloat(row.querySelector('.unit_rate')?.value || 0);
    const total = row.querySelector('.item-total');
    const amount = (qty * rate).toFixed(2);
    total.textContent = amount;
    updateManualTotal();
}

function updateManualTotal() {
    let total = 0;
    document.querySelectorAll('#itemsBody .item-row').forEach(row => {
        const amount = parseFloat(row.querySelector('.item-total').textContent || 0);
        total += amount;
    });
    document.getElementById('manualTotal').textContent = total.toFixed(2);
    updateGrandTotal();
}

function loadTakeoffItems(takeoffId) {
    const card = document.getElementById('takeoffItemsCard');
    const tbody = document.getElementById('takeoffItemsBody');
    
    if (!takeoffId) {
        card.style.display = 'none';
        tbody.innerHTML = '';
        updateGrandTotal();
        return;
    }

    fetch(`/subcon-agreements/takeoff-items?takeoff_id=${takeoffId}`)
        .then(response => response.json())
        .then(data => {
            tbody.innerHTML = '';
            if (data.items && data.items.length > 0) {
                card.style.display = 'block';
                data.items.forEach((item, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>
                            <input type="checkbox" class="form-check-input takeoff-item-checkbox" value="${item.id}" 
                                   name="takeoff_items[]" onchange="updateTakeoffTotal()">
                        </td>
                        <td>${item.description || 'N/A'}</td>
                        <td class="text-center">${item.quantity}</td>
                        <td>${item.unit || 'N/A'}</td>
                        <td>${item.estimated_rate || 0}</td>
                        <td>
                            <input type="number" name="takeoff_rate_${item.id}" class="form-control form-control-sm takeoff-rate" 
                                   step="0.01" min="0" value="${item.estimated_rate || 0}" onchange="updateTakeoffTotal()">
                        </td>
                        <td class="takeoff-item-total">0.00</td>
                    `;
                    tbody.appendChild(row);
                });
            }
        });
}

function updateTakeoffTotal() {
    let total = 0;
    document.querySelectorAll('#takeoffItemsBody tr').forEach(row => {
        const checkbox = row.querySelector('.takeoff-item-checkbox');
        if (checkbox && checkbox.checked) {
            const rateInput = row.querySelector('.takeoff-rate');
            const rate = parseFloat(rateInput?.value || 0);
            // Get quantity from the header
            const qty = parseFloat(row.cells[2].textContent || 0);
            const amount = qty * rate;
            row.querySelector('.takeoff-item-total').textContent = amount.toFixed(2);
            total += amount;
        }
    });
    document.getElementById('takeoffTotal').textContent = total.toFixed(2);
    updateGrandTotal();
}

function updateGrandTotal() {
    const manual = parseFloat(document.getElementById('manualTotal').textContent || 0);
    const takeoff = parseFloat(document.getElementById('takeoffTotal').textContent || 0);
    const grand = manual + takeoff;
    document.getElementById('grandTotal').textContent = grand.toFixed(2);
}

// Initial setup
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.quantity, .unit_rate').forEach(input => {
        input.addEventListener('change', function() {
            updateItemTotal(this);
        });
    });
});

// Select all takeoff items checkbox
document.addEventListener('change', function(e) {
    if (e.target.id === 'selectAllTakeoff') {
        document.querySelectorAll('.takeoff-item-checkbox').forEach(cb => {
            cb.checked = e.target.checked;
        });
        updateTakeoffTotal();
    }
});
</script>
@endpush

@endsection
