@extends('layouts.app')

@section('title', 'Monthly Market Price Update')

@push('styles')
<!-- Tom Select CSS for searchable dropdowns -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
/* ── Premium Modern Price Entry Card Styling ── */
.price-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(15, 22, 35, 0.08);
    background: #ffffff;
    overflow: hidden;
}
.price-card-header {
    background: linear-gradient(135deg, #0f1623 0%, #1e2d45 100%);
    color: #ffffff;
    padding: 20px 28px;
}
.price-card-header h5 {
    font-size: 1.15rem;
    font-weight: 700;
    margin: 0;
    letter-spacing: -0.2px;
}
.price-card-header p {
    font-size: 0.82rem;
    opacity: 0.8;
    margin: 3px 0 0;
}

/* Resource Type Segmented Switcher */
.resource-switcher {
    background: #f1f5f9;
    border-radius: 14px;
    padding: 5px;
    display: flex;
    gap: 4px;
}
.resource-switcher .btn-check + .btn {
    flex: 1;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.88rem;
    padding: 10px 14px;
    color: #64748b;
    background: transparent;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.resource-switcher .btn-check:checked + .btn-material {
    background: #2563eb;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
}
.resource-switcher .btn-check:checked + .btn-manpower {
    background: #059669;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
}
.resource-switcher .btn-check:checked + .btn-equipment {
    background: #d97706;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);
}

/* TomSelect Custom Overrides */
.ts-control {
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 10px 14px !important;
    font-size: 0.9rem !important;
    box-shadow: none !important;
}
.ts-control:focus, .ts-wrapper.focus .ts-control {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
}
.ts-dropdown {
    border-radius: 12px !important;
    border: 1.5px solid #e2e8f0 !important;
    box-shadow: 0 12px 28px rgba(0,0,0,0.12) !important;
    padding: 6px !important;
}
.ts-dropdown .option {
    border-radius: 8px !important;
    padding: 9px 12px !important;
    font-size: 0.88rem !important;
}
.ts-dropdown .option.active {
    background-color: #eff6ff !important;
    color: #1d4ed8 !important;
}

/* Auto-fill Info Box */
.info-box {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
}
.metric-badge {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px;
}
.metric-label {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 2px;
}
.metric-value {
    font-size: 1.15rem;
    font-weight: 700;
    color: #0f172a;
}

/* Modern Input Groups */
.form-control-custom {
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 0.9rem;
}
.form-control-custom:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Top Header Section --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('marketing.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="h4 fw-bold text-dark mb-0">
                    <i class="fa-solid fa-chart-line text-primary me-2"></i>Monthly Market Rate Update
                </h1>
                <p class="text-muted small mb-0">Search and select resources to record updated market prices for ERP estimations.</p>
            </div>
        </div>
        <div class="d-none d-md-flex align-items-center gap-2 text-muted small bg-white px-3 py-2 rounded-3 border shadow-sm">
            <i class="fa-solid fa-user-check text-success"></i>
            <span>Recorded By: <strong>{{ auth()->user()->name }}</strong></span>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm rounded-3">
        <div class="d-flex align-items-start gap-2">
            <i class="fa-solid fa-circle-exclamation mt-1 fs-5"></i>
            <div>
                <strong>Please verify form errors:</strong>
                <ul class="mb-0 mt-1 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="price-card">
                
                <div class="price-card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5><i class="fa-solid fa-pen-to-square me-2 text-warning"></i>Price Update Entry</h5>
                        <p>Select resource category, search items, and submit updated rate intelligence.</p>
                    </div>
                    <span class="badge bg-white text-dark fw-bold px-3 py-2 rounded-pill shadow-sm fs-7">
                        <i class="fa-solid fa-bolt text-warning me-1"></i>Live Sync
                    </span>
                </div>

                <div class="card-body p-4 p-md-5">
                    <form method="POST" action="{{ route('marketing.prices.store') }}">
                        @csrf

                        {{-- Resource Type Segmented Selection --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted letter-spacing-1 mb-2">1. Select Resource Category <span class="text-danger">*</span></label>
                            <div class="resource-switcher" role="group">
                                <input type="radio" class="btn-check" name="resource_type" id="type_material" value="material" checked onchange="switchResourceType('material')">
                                <label class="btn btn-material" for="type_material">
                                    <i class="fa-solid fa-boxes-stacked"></i> Material
                                </label>

                                <input type="radio" class="btn-check" name="resource_type" id="type_manpower" value="manpower" onchange="switchResourceType('manpower')">
                                <label class="btn btn-manpower" for="type_manpower">
                                    <i class="fa-solid fa-user-gear"></i> Manpower
                                </label>

                                <input type="radio" class="btn-check" name="resource_type" id="type_equipment" value="equipment" onchange="switchResourceType('equipment')">
                                <label class="btn btn-equipment" for="type_equipment">
                                    <i class="fa-solid fa-truck-monster"></i> Equipment
                                </label>
                            </div>
                        </div>

                        {{-- Searchable Material Select --}}
                        <div class="mb-4 res-field" id="field_material">
                            <label class="form-label fw-semibold text-dark">Search Material / Product <span class="text-danger">*</span></label>
                            <select name="product_id" id="productSelect" class="form-select @error('product_id') is-invalid @enderror" placeholder="Type to search material by name, SKU, or category...">
                                <option value="">— Search & Select Material —</option>
                                @foreach($products as $p)
                                <option value="{{ $p->id }}"
                                        data-unit="{{ $p->unit }}"
                                        data-category="{{ $p->category }}"
                                        data-price="{{ $p->unit_price }}"
                                        @selected(old('product_id') == $p->id)>
                                    {{ $p->name }} ({{ $p->unit }}) — Category: {{ $p->category ?: 'General' }}
                                </option>
                                @endforeach
                            </select>
                            @error('product_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        {{-- Searchable Manpower Select --}}
                        <div class="mb-4 res-field d-none" id="field_manpower">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold text-dark mb-0">Search Manpower Role / Designation <span class="text-danger">*</span></label>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-bold text-success" data-bs-toggle="modal" data-bs-target="#addManpowerModal">
                                    <i class="fa-solid fa-plus-circle me-1"></i>+ Add New Role
                                </button>
                            </div>
                            <select name="role_id" id="roleSelect" class="form-select @error('role_id') is-invalid @enderror" placeholder="Type to search manpower role...">
                                <option value="">— Search & Select Manpower Role —</option>
                                @foreach($roles as $r)
                                @php $dailyRate = $r->min_salary ? round($r->min_salary / 26, 2) : 0; @endphp
                                <option value="{{ $r->id }}"
                                        data-unit="man-day"
                                        data-category="Manpower"
                                        data-price="{{ $dailyRate }}"
                                        @selected(old('role_id') == $r->id)>
                                    {{ $r->title }} (man-day) — Benchmark: ETB {{ number_format($dailyRate, 2) }}/day
                                </option>
                                @endforeach
                            </select>
                            @error('role_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        {{-- Searchable Equipment Select --}}
                        <div class="mb-4 res-field d-none" id="field_equipment">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold text-dark mb-0">Search Equipment / Fixed Asset <span class="text-danger">*</span></label>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('equipment.index') }}" target="_blank" class="btn btn-link btn-sm p-0 text-decoration-none text-muted fw-semibold">
                                        <i class="fa-solid fa-gear me-1"></i>Manage Assets
                                    </a>
                                    <span class="text-muted">|</span>
                                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-warning text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                                        <i class="fa-solid fa-plus-circle me-1"></i>+ Add Asset
                                    </button>
                                </div>
                            </div>
                            <select name="equipment_id" id="equipmentSelect" class="form-select @error('equipment_id') is-invalid @enderror" placeholder="Type to search equipment or asset code...">
                                <option value="">— Search & Select Equipment —</option>
                                @foreach($equipment as $eq)
                                @php $eqRate = $eq->daily_rate ?: ($eq->hourly_rate ? round($eq->hourly_rate * 8, 2) : 0); @endphp
                                <option value="{{ $eq->id }}"
                                        data-unit="{{ $eq->unit ?: 'day' }}"
                                        data-category="Fixed Asset"
                                        data-price="{{ $eqRate }}"
                                        @selected(old('equipment_id') == $eq->id)>
                                    {{ $eq->name }} [Asset: {{ $eq->code ?: 'N/A' }}] ({{ $eq->unit ?: 'day' }}) — Daily: ETB {{ number_format($eqRate, 2) }}
                                </option>
                                @endforeach
                            </select>
                            @error('equipment_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        {{-- Metric Intelligence Summary Badges --}}
                        <div class="info-box mb-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="metric-badge">
                                        <div class="metric-label"><i class="fa-solid fa-ruler-combined me-1"></i>Unit of Measure (UM)</div>
                                        <div class="metric-value" id="umDisplay">Select Item</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="metric-badge">
                                        <div class="metric-label"><i class="fa-solid fa-clock-rotate-left me-1"></i>Last Recorded Rate</div>
                                        <div class="metric-value text-primary" id="prevPriceDisplay">ETB 0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            {{-- New Market Price Input --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark" id="priceLabel">
                                    New Market Price (ETB) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted fw-bold">ETB</span>
                                    <input type="number" step="0.01" min="0" name="price" class="form-control form-control-custom border-start-0 @error('price') is-invalid @enderror"
                                           value="{{ old('price') }}" placeholder="0.00" required>
                                </div>
                                @error('price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            {{-- Effective Date Input --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">
                                    Effective Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="effective_date" class="form-control form-control-custom @error('effective_date') is-invalid @enderror"
                                       value="{{ old('effective_date', now()->format('Y-m-d')) }}" required>
                                @error('effective_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Source Explanation Notes --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Notes / Price Source Justification</label>
                            <textarea name="notes" rows="3" class="form-control form-control-custom" placeholder="Provide vendor quote reference, market research source, or price adjustment context...">{{ old('notes') }}</textarea>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                            <a href="{{ route('marketing.dashboard') }}" class="btn btn-light px-4 fw-semibold">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
                                <i class="fa-solid fa-check-circle me-1"></i>Save Price Record
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form method="POST" action="{{ route('manpower-roles.store') }}">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-user-plus text-success me-2"></i>Add New Manpower Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role / Designation Title <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-custom" placeholder="e.g. Senior Mason, Electrician, Helper" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Unit of Measure</label>
                        <select name="default_unit" class="form-select form-control-custom">
                            <option value="day">day (man-day)</option>
                            <option value="hr">hr (man-hour)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category</label>
                        <input type="text" name="category" class="form-control form-control-custom" placeholder="e.g. Skilled Labor, Technical">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4"><i class="fa-solid fa-check me-1"></i>Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Quick Add Equipment Modal ── --}}
<div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form method="POST" action="{{ route('equipment.store') }}">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-truck-monster text-warning me-2"></i>Add Fixed Asset Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Equipment / Asset Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-custom" placeholder="e.g. Concrete Mixer 350L, Excavator CAT 320" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Asset Tag / Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control form-control-custom" placeholder="e.g. EQ-001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category</label>
                            <input type="text" name="category" class="form-control form-control-custom" value="Fixed Asset" placeholder="e.g. Heavy Equipment">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hourly Rate (ETB)</label>
                            <input type="number" step="0.01" min="0" name="hourly_rate" class="form-control form-control-custom" value="0.00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Daily Rate (ETB)</label>
                            <input type="number" step="0.01" min="0" name="daily_rate" class="form-control form-control-custom" value="0.00" required>
                        </div>
                    </div>
                    <input type="hidden" name="unit" value="day">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4"><i class="fa-solid fa-check me-1"></i>Create Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Tom Select JS for searchable dropdowns -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
let productTomSelect, roleTomSelect, equipmentTomSelect;

function initTomSelects() {
    const tsOptions = {
        create: false,
        maxOptions: 100,
        sortField: { field: "text", direction: "asc" }
    };

    if (document.getElementById('productSelect') && !productTomSelect) {
        productTomSelect = new TomSelect('#productSelect', {
            ...tsOptions,
            onChange: function() { onTomSelectChange(this); }
        });
    }

    if (document.getElementById('roleSelect') && !roleTomSelect) {
        roleTomSelect = new TomSelect('#roleSelect', {
            ...tsOptions,
            onChange: function() { onTomSelectChange(this); }
        });
    }

    if (document.getElementById('equipmentSelect') && !equipmentTomSelect) {
        equipmentTomSelect = new TomSelect('#equipmentSelect', {
            ...tsOptions,
            onChange: function() { onTomSelectChange(this); }
        });
    }
}

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

    // Reset metric indicators
    document.getElementById('umDisplay').innerText = 'Select Item';
    document.getElementById('prevPriceDisplay').innerText = 'ETB 0.00';

    // Trigger update for selected item
    let instance;
    if (type === 'material') instance = productTomSelect;
    else if (type === 'manpower') instance = roleTomSelect;
    else if (type === 'equipment') instance = equipmentTomSelect;

    if (instance && instance.getValue()) {
        onTomSelectChange(instance);
    }
}

function onTomSelectChange(instance) {
    const val = instance.getValue();
    if (!val) {
        document.getElementById('umDisplay').innerText = 'Select Item';
        document.getElementById('prevPriceDisplay').innerText = 'ETB 0.00';
        return;
    }

    const selectEl = instance.input;
    const selectedOpt = selectEl.querySelector(`option[value="${val}"]`);
    if (selectedOpt) {
        const unit = selectedOpt.dataset.unit || 'N/A';
        const price = selectedOpt.dataset.price || '0.00';

        document.getElementById('umDisplay').innerText = unit;
        document.getElementById('prevPriceDisplay').innerText = 'ETB ' + parseFloat(price).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initTomSelects();
    const checkedType = document.querySelector('input[name="resource_type"]:checked')?.value || 'material';
    switchResourceType(checkedType);
});
</script>
@endpush

