@extends('layouts.app')

@section('title', 'Monthly Market Price Update')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('marketing.dashboard') }}" class="btn btn-outline-secondary btn-sm me-3">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="fa-solid fa-calendar-plus text-primary me-2"></i>Monthly Market Price Entry
            </h1>
            <p class="text-muted small mb-0">Record or update current market rates for materials to maintain price intelligence and historical trends.</p>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4">
        <i class="fa-solid fa-circle-exclamation me-2"></i>
        <strong>Please fix the errors below:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8 col-xl-7">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-transparent py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Price Update Form</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('marketing.prices.store') }}">
                        @csrf

                        {{-- Resource Type Selection --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-block">Resource Type <span class="text-danger">*</span></label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="resource_type" id="type_material" value="material" checked onchange="switchResourceType('material')">
                                <label class="btn btn-outline-primary fw-bold py-2" for="type_material">
                                    <i class="fa-solid fa-flask me-1"></i>Material
                                </label>

                                <input type="radio" class="btn-check" name="resource_type" id="type_manpower" value="manpower" onchange="switchResourceType('manpower')">
                                <label class="btn btn-outline-success fw-bold py-2" for="type_manpower">
                                    <i class="fa-solid fa-users me-1"></i>Manpower
                                </label>

                                <input type="radio" class="btn-check" name="resource_type" id="type_equipment" value="equipment" onchange="switchResourceType('equipment')">
                                <label class="btn btn-outline-warning text-dark fw-bold py-2" for="type_equipment">
                                    <i class="fa-solid fa-gears me-1"></i>Equipment
                                </label>
                            </div>
                        </div>

                        {{-- Select Material --}}
                        <div class="mb-3 res-field" id="field_material">
                            <label class="form-label fw-semibold">Material / Product <span class="text-danger">*</span></label>
                            <select name="product_id" id="productSelect" class="form-select @error('product_id') is-invalid @enderror" onchange="onResourceSelect(this)">
                                <option value="">— Select Material —</option>
                                @foreach($products as $p)
                                <option value="{{ $p->id }}"
                                        data-unit="{{ $p->unit }}"
                                        data-category="{{ $p->category }}"
                                        data-price="{{ $p->unit_price }}"
                                        @selected(old('product_id') == $p->id)>
                                    {{ $p->name }} ({{ $p->unit }}) — {{ $p->category }}
                                </option>
                                @endforeach
                            </select>
                            @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Select Manpower Role --}}
                        <div class="mb-3 res-field d-none" id="field_manpower">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold mb-0">Manpower Role / Designation <span class="text-danger">*</span></label>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-semibold" data-bs-toggle="modal" data-bs-target="#addManpowerModal">
                                    <i class="fa-solid fa-plus-circle me-1"></i>+ Add New Role
                                </button>
                            </div>
                            <select name="role_id" id="roleSelect" class="form-select @error('role_id') is-invalid @enderror" onchange="onResourceSelect(this)">
                                <option value="">— Select Manpower Role —</option>
                                @foreach($roles as $r)
                                @php $dailyRate = $r->min_salary ? round($r->min_salary / 26, 2) : 0; @endphp
                                <option value="{{ $r->id }}"
                                        data-unit="man-day"
                                        data-category="Manpower"
                                        data-price="{{ $dailyRate }}"
                                        @selected(old('role_id') == $r->id)>
                                    {{ $r->title }} (man-day) — ETB {{ number_format($dailyRate, 2) }}/day
                                </option>
                                @endforeach
                            </select>
                            @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Select Equipment (Linked with Fixed Assets) --}}
                        <div class="mb-3 res-field d-none" id="field_equipment">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold mb-0">Equipment / Fixed Asset <span class="text-danger">*</span></label>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('equipment.index') }}" target="_blank" class="btn btn-link btn-sm p-0 text-decoration-none text-muted fw-semibold">
                                        <i class="fa-solid fa-gear me-1"></i>Manage Assets
                                    </a>
                                    <span class="text-muted">|</span>
                                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-warning text-dark fw-semibold" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                                        <i class="fa-solid fa-plus-circle me-1"></i>+ Add New Asset
                                    </button>
                                </div>
                            </div>
                            <select name="equipment_id" id="equipmentSelect" class="form-select @error('equipment_id') is-invalid @enderror" onchange="onResourceSelect(this)">
                                <option value="">— Select Equipment / Fixed Asset —</option>
                                @foreach($equipment as $eq)
                                @php $eqRate = $eq->daily_rate ?: ($eq->hourly_rate ? round($eq->hourly_rate * 8, 2) : 0); @endphp
                                <option value="{{ $eq->id }}"
                                        data-unit="{{ $eq->unit ?: 'day' }}"
                                        data-category="Fixed Asset"
                                        data-price="{{ $eqRate }}"
                                        @selected(old('equipment_id') == $eq->id)>
                                    {{ $eq->name }} [Asset: {{ $eq->code ?: 'N/A' }}] ({{ $eq->unit ?: 'day' }}) — ETB {{ number_format($eqRate, 2) }}/day
                                </option>
                                @endforeach
                            </select>
                            @error('equipment_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-3 mb-3">
                            {{-- Unit of Measure --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Unit of Measure (UM)</label>
                                <input type="text" id="umDisplay" class="form-control bg-light" placeholder="Auto-filled" readonly>
                            </div>

                            {{-- Previous Recorded Price --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Previous Recorded Price</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted">ETB</span>
                                    <input type="text" id="prevPriceDisplay" class="form-control bg-light" placeholder="Auto-filled" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            {{-- New Market Price --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" id="priceLabel">New Market Rate (ETB) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" name="price" class="form-control @error('price') is-invalid @enderror"
                                       value="{{ old('price') }}" placeholder="0.00" required>
                                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Effective Date / Month --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Effective Date <span class="text-danger">*</span></label>
                                <input type="date" name="effective_date" class="form-control @error('effective_date') is-invalid @enderror"
                                       value="{{ old('effective_date', now()->format('Y-m-d')) }}" required>
                                @error('effective_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Updated By & Info --}}
                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted fw-semibold">Logged-in User (Updated By):</span>
                                <span class="fw-bold text-dark"><i class="fa-solid fa-user-check me-1 text-success"></i>{{ auth()->user()->name }}</span>
                            </div>
                            <div class="text-muted small mt-1">
                                <i class="fa-solid fa-info-circle me-1 text-primary"></i>Updating rates here instantly feeds into project budget calculations across ERP Plans.
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Notes / Source Explanation</label>
                            <textarea name="notes" rows="2" class="form-control" placeholder="Optional notes regarding market quotes, supplier research, or price justification...">{{ old('notes') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('marketing.dashboard') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-floppy-disk me-1"></i>Save Price Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── Quick Add Manpower Modal ── --}}
<div class="modal fade" id="addManpowerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('manpower-roles.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-plus text-success me-2"></i>Add New Manpower Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role / Designation Title <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Senior Mason, Electrician, Helper" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Unit of Measure</label>
                        <select name="default_unit" class="form-select">
                            <option value="day">day (man-day)</option>
                            <option value="hr">hr (man-hour)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category</label>
                        <input type="text" name="category" class="form-control" placeholder="e.g. Skilled Labor, Technical">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fa-solid fa-check me-1"></i>Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Quick Add Equipment / Fixed Asset Modal ── --}}
<div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('equipment.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-truck-monster text-warning text-dark me-2"></i>Add Fixed Asset Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Equipment / Asset Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Concrete Mixer 350L, Excavator CAT 320" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Asset Tag / Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" placeholder="e.g. EQ-001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category</label>
                            <input type="text" name="category" class="form-control" value="Fixed Asset" placeholder="e.g. Heavy Equipment">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hourly Rate (ETB)</label>
                            <input type="number" step="0.01" min="0" name="hourly_rate" class="form-control" value="0.00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Daily Rate (ETB)</label>
                            <input type="number" step="0.01" min="0" name="daily_rate" class="form-control" value="0.00" required>
                        </div>
                    </div>
                    <input type="hidden" name="unit" value="day">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning"><i class="fa-solid fa-check me-1"></i>Create Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function switchResourceType(type) {
    document.querySelectorAll('.res-field').forEach(el => el.classList.add('d-none'));
    const activeField = document.getElementById(`field_${type}`);
    if (activeField) activeField.classList.remove('d-none');

    const priceLabel = document.getElementById('priceLabel');
    if (type === 'manpower') {
        priceLabel.innerHTML = 'Daily Manpower Rate (ETB/day) <span class="text-danger">*</span>';
    } else if (type === 'equipment') {
        priceLabel.innerHTML = 'Daily Rental Rate (ETB/day) <span class="text-danger">*</span>';
    } else {
        priceLabel.innerHTML = 'New Market Price (ETB) <span class="text-danger">*</span>';
    }

    // Reset displays
    document.getElementById('umDisplay').value = '';
    document.getElementById('prevPriceDisplay').value = '';

    // Trigger select change if active has value
    let sel;
    if (type === 'material') sel = document.getElementById('productSelect');
    else if (type === 'manpower') sel = document.getElementById('roleSelect');
    else if (type === 'equipment') sel = document.getElementById('equipmentSelect');

    if (sel && sel.value) onResourceSelect(sel);
}

function onResourceSelect(selectEl) {
    const opt = selectEl.options[selectEl.selectedIndex];
    const unit = opt.dataset.unit || '';
    const price = opt.dataset.price || '0.00';
    document.getElementById('umDisplay').value = unit;
    document.getElementById('prevPriceDisplay').value = parseFloat(price).toLocaleString('en-US', {minimumFractionDigits: 2});
}

document.addEventListener('DOMContentLoaded', function() {
    const checkedType = document.querySelector('input[name="resource_type"]:checked')?.value || 'material';
    switchResourceType(checkedType);
});
</script>
@endpush
