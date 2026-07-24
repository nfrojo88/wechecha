@extends('layouts.app')

@section('title', 'Convert Take-Off to ERP Plan')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

* { font-family: 'Inter', sans-serif; }

/* â”€â”€â”€ Hero Header â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.cv-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #1d4ed8 100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
}
.cv-header::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    background: rgba(255,255,255,.05);
    border-radius: 50%;
}
.cv-header-title { font-size: 1.5rem; font-weight: 800; color: #fff; margin: 0 0 4px; }
.cv-header-sub   { color: rgba(255,255,255,.65); font-size: .875rem; margin: 0; }

/* â”€â”€â”€ Meta Card â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.meta-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 1px 4px rgba(0,0,0,.08);
    border: 1px solid #e2e8f0;
    padding: 24px;
    margin-bottom: 24px;
}
.meta-card h6 { font-weight: 700; color: #1e293b; margin-bottom: 20px; font-size: .875rem; text-transform: uppercase; letter-spacing: .5px; }

/* â”€â”€â”€ Section Card â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.section-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    margin-bottom: 20px;
    overflow: hidden;
    transition: box-shadow .2s, transform .15s;
}
.section-card:hover { box-shadow: 0 6px 24px rgba(37,99,235,.12); transform: translateY(-1px); }

.section-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 22px;
    background: linear-gradient(90deg, #f8fafc 0%, #eff6ff 100%);
    border-bottom: 1px solid #e2e8f0;
}
.section-name { font-weight: 700; font-size: 1rem; color: #1e293b; }
.qty-pill {
    background: #2563eb;
    color: #fff;
    padding: 5px 16px;
    border-radius: 50px;
    font-weight: 700;
    font-size: .8rem;
    letter-spacing: .3px;
}

/* â”€â”€â”€ Resource Table â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.res-table { width: 100%; border-collapse: collapse; }
.res-table th {
    padding: 10px 14px;
    font-size: 10.5px;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #64748b;
    font-weight: 700;
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
}
.res-table td { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.res-table tr:last-child td { border-bottom: none; }
.res-table tr:hover td { background: #fafbff; }

/* â”€â”€â”€ Type Badges â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.badge-material  { background: #dbeafe; color: #1e40af; }
.badge-manpower  { background: #dcfce7; color: #14532d; }
.badge-equipment { background: #fef9c3; color: #78350f; }
.type-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 50px; font-size: 11px; font-weight: 600; white-space: nowrap; }

/* â”€â”€â”€ Result Qty â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.result-qty { color: #2563eb; font-weight: 700; font-size: .95rem; }

/* â”€â”€â”€ Add row zone â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.add-row-zone { padding: 14px 22px; border-top: 1px dashed #cbd5e1; display: flex; gap: 8px; flex-wrap: wrap; background: #fafbfd; }
.add-btn { font-size: 12px; font-weight: 600; border-radius: 8px !important; }
.remove-btn { color: #dc2626; background: none; border: none; cursor: pointer; border-radius: 6px; padding: 4px 8px; }
.remove-btn:hover { background: #fee2e2; }

/* â”€â”€â”€ Sticky footer â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.sticky-bar {
    position: sticky;
    bottom: 0;
    background: rgba(255,255,255,.95);
    backdrop-filter: blur(12px);
    border-top: 2px solid #e2e8f0;
    padding: 14px 28px;
    display: flex;
    gap: 12px;
    align-items: center;
    z-index: 200;
    box-shadow: 0 -6px 20px rgba(0,0,0,.08);
}
.process-btn {
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    border: none;
    color: #fff;
    font-weight: 700;
    padding: 10px 28px;
    border-radius: 10px;
    font-size: .925rem;
    cursor: pointer;
    transition: opacity .2s, transform .1s;
}
.process-btn:hover { opacity: .9; transform: translateY(-1px); }

/* â”€â”€â”€ Load Template button â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.load-tmpl-btn {
    background: linear-gradient(135deg,#7c3aed,#4f46e5);
    color:#fff; border:none; border-radius:8px;
    padding:5px 14px; font-size:12px; font-weight:600;
    cursor:pointer; display:flex; align-items:center; gap:6px;
    transition: opacity .15s;
}
.load-tmpl-btn:hover { opacity:.87; }

/* â”€â”€â”€ Collapsible picker panels â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.pick-panel {
    display: none;
    border-radius: 10px;
    padding: 12px 16px;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 4px;
}
.pick-panel.active  { display: flex; }
.pick-panel.tmpl-p  { background:#f8faff; border:1px solid #c7d2fe; }
.pick-panel.mat-p   { background:#eff6ff; border:1px solid #93c5fd; }
.pick-panel.mp-p    { background:#f0fdf4; border:1px solid #86efac; }
.pick-panel.eq-p    { background:#fefce8; border:1px solid #fde047; }
.pick-panel select  { flex:1; min-width:220px; }
.unit-tag {
    font-size:11px; color:#475569; background:#e2e8f0;
    padding:3px 10px; border-radius:20px; font-weight:600; white-space:nowrap;
}

.form-control, .form-select { border-radius: 8px !important; font-size: .875rem; }
</style>
@endpush

@section('content')

{{-- â”€â”€ Hero Header â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<div class="cv-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <p class="cv-header-title">
                <i class="fa-solid fa-diagram-project me-2"></i>Convert Take-Off to ERP Plan
            </p>
            <p class="cv-header-sub">
                Sheet: <strong style="color:#93c5fd;">{{ $takeoff->title }}</strong>
                &nbsp;Â·&nbsp; Project: <strong style="color:#93c5fd;">{{ $takeoff->project->name }}</strong>
            </p>
        </div>
        <div class="d-flex gap-2">
            <div class="form-check form-switch mb-0"
                 style="background:rgba(255,255,255,.1);padding:8px 18px;border-radius:10px;display:flex;align-items:center;gap:8px;">
                <input class="form-check-input" type="checkbox" id="rememberRatios" checked>
                <label class="form-check-label text-white small fw-semibold" for="rememberRatios">Remember Templates</label>
            </div>
            <a href="{{ route('takeoff.show', $takeoff) }}" class="btn btn-light btn-sm fw-semibold">
                <i class="fa-solid fa-xmark me-1"></i> Cancel
            </a>
        </div>
    </div>
</div>

{{-- â”€â”€ Plan Metadata â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<div class="meta-card">
    <h6><i class="fa-solid fa-sliders me-2 text-primary"></i>Plan Settings</h6>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label fw-semibold small">Plan Name <span class="text-danger">*</span></label>
            <input type="text" id="meta_plan_name" class="form-control"
                   value="Execution Plan â€” {{ $takeoff->title }}" required>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold small">Start Date <span class="text-danger">*</span></label>
            <input type="date" id="meta_start_date" class="form-control"
                   value="{{ $takeoff->project->start_date?->format('Y-m-d') ?? now()->format('Y-m-d') }}" required>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold small">End Date <span class="text-danger">*</span></label>
            <input type="date" id="meta_end_date" class="form-control"
                   value="{{ $takeoff->project->end_date?->format('Y-m-d') ?? now()->addDays(90)->format('Y-m-d') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold small">Notes</label>
            <input type="text" id="meta_notes" class="form-control"
                   placeholder="e.g. Phase 1 execution plan from takeoff"
                   value="From Takeoff: {{ $takeoff->title }}">
        </div>
    </div>
</div>

{{-- â”€â”€ Budget Preview â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<div class="d-flex gap-3 mb-4 flex-wrap">
    <div class="d-flex align-items-center gap-2 px-4 py-2 rounded-3 border" style="background:#eff6ff;">
        <i class="fa-solid fa-flask text-primary"></i>
        <span class="small fw-semibold text-muted">Materials:</span>
        <span class="fw-bold text-primary" id="budget-material">0.00</span>
    </div>
    <div class="d-flex align-items-center gap-2 px-4 py-2 rounded-3 border" style="background:#f0fdf4;">
        <i class="fa-solid fa-users text-success"></i>
        <span class="small fw-semibold text-muted">Manpower:</span>
        <span class="fw-bold text-success" id="budget-manpower">0.00</span>
    </div>
    <div class="d-flex align-items-center gap-2 px-4 py-2 rounded-3 border" style="background:#fefce8;">
        <i class="fa-solid fa-gears text-warning"></i>
        <span class="small fw-semibold text-muted">Equipment:</span>
        <span class="fw-bold text-warning" id="budget-equipment">0.00</span>
    </div>
    <div class="d-flex align-items-center gap-2 px-4 py-2 rounded-3 border" style="background:#0f172a;color:#fff;">
        <i class="fa-solid fa-coins"></i>
        <span class="small fw-semibold" style="opacity:.7;">Total Budget:</span>
        <span class="fw-bold" id="budget-total">0.00</span>
        <span class="small" style="opacity:.5;">ETB</span>
    </div>
</div>

{{-- â”€â”€ Section Cards â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
@foreach($takeoff->sections as $sIdx => $section)
<div class="section-card">
    <div class="section-card-head">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-layer-group text-primary fa-sm"></i>
            <span class="section-name">{{ $section->name }}</span>
            @if($section->task)
                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 ms-1" title="Linked to WBS Task: {{ $section->task->name }}">
                    <i class="fa-solid fa-link me-1"></i>Task: {{ $section->task->wbs_code ? $section->task->wbs_code.' - ' : '' }}{{ $section->task->name }}
                </span>
            @endif
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-1">
                <span class="text-muted small fw-semibold">Schedule Duration:</span>
                <input type="number" min="1" step="1" class="form-control form-control-sm sec-duration" id="sec-dur-{{ $sIdx }}" value="{{ $section->schedule_duration_days }}" style="width:70px; font-weight:700;" oninput="updateAllSectionRows({{ $sIdx }})">
                <span class="text-muted small fw-semibold">Days</span>
            </div>
            <span class="text-muted small">Section Total:</span>
            <span class="qty-pill">{{ number_format($section->total_quantity, 3) }} {{ $section->primary_unit }}</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="res-table" id="rtable-{{ $sIdx }}">
            <thead>
                <tr>
                    <th style="width:34px;"><input type="checkbox" class="form-check-input sec-all" data-sec="{{ $sIdx }}" checked></th>
                    <th>Type</th>
                    <th>Resource Name</th>
                    <th style="min-width:150px;">Standard Work Template</th>
                    <th style="width:110px;">Ratio / Unit</th>
                    <th style="width:110px;">Rate (ETB)</th>
                    <th style="width:120px;">Resulting Qty</th>
                    <th style="width:130px; background:#eff6ff; color:#1e40af;">Per-Day Req.</th>
                    <th style="width:120px;">Total Cost</th>
                    <th style="width:36px;"></th>
                </tr>
            </thead>
            <tbody id="tbody-{{ $sIdx }}"
                   data-section-total="{{ $section->total_quantity }}"
                   data-section-unit="{{ $section->primary_unit }}"
                   data-section-name="{{ $section->name }}">
            </tbody>
        </table>
    </div>

    <div class="add-row-zone" style="flex-direction:column;gap:10px;">

        {{-- Template Panel --}}
        <div id="tmpl-wrap-{{ $sIdx }}" class="pick-panel tmpl-p">
            <i class="fa-solid fa-magic-wand-sparkles text-primary"></i>
            <span class="small fw-semibold text-muted">
                Select Standard Work for <strong>{{ $section->primary_unit ?: 'any unit' }}</strong>:
            </span>
            <select class="form-select form-select-sm" id="tmpl-sel-{{ $sIdx }}"
                    onchange="expandTemplate({{ $sIdx }}, this.value)">
                <option value="">â€” Pick a template â€”</option>
            </select>
            <button type="button" class="btn btn-sm btn-outline-secondary"
                    onclick="closePanel('tmpl-wrap-{{ $sIdx }}')">Cancel</button>
        </div>

        {{-- Material Pick Panel --}}
        <div id="mat-pick-{{ $sIdx }}" class="pick-panel mat-p">
            <i class="fa-solid fa-flask text-primary"></i>
            <span class="small fw-semibold text-muted">Select registered material:</span>
            <select class="form-select form-select-sm" id="mat-sel-{{ $sIdx }}"
                    onchange="onPickChange({{ $sIdx }},'material')">
                <option value="">â€” Pick material â€”</option>
                @foreach($registeredProducts as $prod)
                <option value="{{ $prod['id'] }}"
                        data-name="{{ $prod['name'] }}"
                        data-unit="{{ $prod['unit'] }}"
                        data-rate="{{ $prod['rate'] }}">
                    {{ $prod['name'] }} ({{ $prod['unit'] }}){{ $prod['rate'] > 0 ? '  â€”  ETB '.number_format($prod['rate'],2) : '' }}
                </option>
                @endforeach
            </select>
            <span class="unit-tag" id="mat-utag-{{ $sIdx }}">unit: â€”</span>
            <button type="button" class="btn btn-sm btn-primary" id="mat-addbtn-{{ $sIdx }}"
                    style="display:none" onclick="addPickedRow({{ $sIdx }},'material')">
                <i class="fa-solid fa-plus me-1"></i>Add Row
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary"
                    onclick="closePanel('mat-pick-{{ $sIdx }}')">Cancel</button>
        </div>

        {{-- Manpower Pick Panel --}}
        <div id="mp-pick-{{ $sIdx }}" class="pick-panel mp-p">
            <i class="fa-solid fa-users text-success"></i>
            <span class="small fw-semibold text-muted">Select registered role:</span>
            <select class="form-select form-select-sm" id="mp-sel-{{ $sIdx }}"
                    onchange="onPickChange({{ $sIdx }},'manpower')">
                <option value="">â€” Pick role â€”</option>
                @foreach($registeredRoles as $role)
                <option value="{{ $role['id'] }}"
                        data-name="{{ $role['name'] }}"
                        data-unit="{{ $role['unit'] }}"
                        data-rate="{{ $role['rate'] }}">
                    {{ $role['name'] }} ({{ $role['unit'] }}){{ $role['rate'] > 0 ? '  â€”  ETB '.number_format($role['rate'],2).'/day' : '' }}
                </option>
                @endforeach
            </select>
            <span class="unit-tag" id="mp-utag-{{ $sIdx }}">unit: man-day</span>
            <button type="button" class="btn btn-sm btn-success" id="mp-addbtn-{{ $sIdx }}"
                    style="display:none" onclick="addPickedRow({{ $sIdx }},'manpower')">
                <i class="fa-solid fa-plus me-1"></i>Add Row
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary"
                    onclick="closePanel('mp-pick-{{ $sIdx }}')">Cancel</button>
        </div>

        {{-- Equipment Pick Panel --}}
        <div id="eq-pick-{{ $sIdx }}" class="pick-panel eq-p">
            <i class="fa-solid fa-gears text-warning"></i>
            <span class="small fw-semibold text-muted">Select registered equipment:</span>
            <select class="form-select form-select-sm" id="eq-sel-{{ $sIdx }}"
                    onchange="onPickChange({{ $sIdx }},'equipment')">
                <option value="">â€” Pick equipment â€”</option>
                @foreach($registeredEquipment as $eq)
                <option value="{{ $eq['id'] }}"
                        data-name="{{ $eq['name'] }}"
                        data-unit="{{ $eq['unit'] }}"
                        data-rate="{{ $eq['rate'] }}">
                    {{ $eq['name'] }} ({{ $eq['unit'] }}){{ $eq['rate'] > 0 ? '  â€”  ETB '.number_format($eq['rate'],2).'/hr' : '' }}
                </option>
                @endforeach
            </select>
            <span class="unit-tag" id="eq-utag-{{ $sIdx }}">unit: hour</span>
            <button type="button" class="btn btn-sm btn-warning" id="eq-addbtn-{{ $sIdx }}"
                    style="display:none" onclick="addPickedRow({{ $sIdx }},'equipment')">
                <i class="fa-solid fa-plus me-1"></i>Add Row
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary"
                    onclick="closePanel('eq-pick-{{ $sIdx }}')">Cancel</button>
        </div>

        {{-- Button Row --}}
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <button type="button" class="load-tmpl-btn"
                    onclick="openTmplPanel({{ $sIdx }}, '{{ $section->primary_unit }}')">
                <i class="fa-solid fa-magic-wand-sparkles"></i> Load from Template
            </button>
            <span class="text-muted" style="font-size:11px;">or add manually:</span>
            <button type="button" class="btn btn-sm btn-outline-primary add-btn"
                    onclick="openPickPanel({{ $sIdx }},'material')">
                <i class="fa-solid fa-flask me-1"></i>+ Material
            </button>
            <button type="button" class="btn btn-sm btn-outline-success add-btn"
                    onclick="openPickPanel({{ $sIdx }},'manpower')">
                <i class="fa-solid fa-users me-1"></i>+ Manpower
            </button>
            <button type="button" class="btn btn-sm btn-outline-warning add-btn"
                    onclick="openPickPanel({{ $sIdx }},'equipment')">
                <i class="fa-solid fa-gears me-1"></i>+ Equipment
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary add-btn ms-auto"
                    onclick="addManualFreeRow({{ $sIdx }})">
                <i class="fa-solid fa-plus me-1"></i>+ Manual Row
            </button>
        </div>
    </div>
</div>
@endforeach

{{-- â”€â”€ Sticky Bar â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<div class="sticky-bar">
    <button type="button" class="process-btn" onclick="submitConversion()">
        <i class="fa-solid fa-diagram-project me-2"></i>Create ERP Plan
    </button>
    <a href="{{ route('takeoff.show', $takeoff) }}" class="btn btn-outline-secondary fw-semibold">
        <i class="fa-solid fa-xmark me-1"></i>Cancel
    </a>
    <div class="ms-auto d-flex gap-3 align-items-center text-muted small">
        <span><i class="fa-solid fa-list-check me-1"></i><span id="rc-label">0 resources</span></span>
    </div>
</div>

{{-- Hidden form --}}
<form id="mainForm" action="{{ route('takeoff.process-conversion', $takeoff) }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="plan_name"       id="f_plan_name">
    <input type="hidden" name="plan_start_date" id="f_start_date">
    <input type="hidden" name="plan_end_date"   id="f_end_date">
    <input type="hidden" name="notes"           id="f_notes">
    <div id="f_body"></div>
</form>

@endsection

@push('scripts')
<script>
// â”€â”€ Server data â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
const SW      = @json($standardWorksJson);
const MATLIST = @json($registeredProducts);
const EQLIST  = @json($registeredEquipment);
const MPLIST  = @json($registeredRoles);

// â”€â”€ Type config â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
const TYPE_CFG = {
    material:  { cls:'badge-material',  label:'Material',  icon:'fa-flask', swKey:'materials' },
    manpower:  { cls:'badge-manpower',  label:'Manpower',  icon:'fa-users', swKey:'manpower'  },
    equipment: { cls:'badge-equipment', label:'Equipment', icon:'fa-gears', swKey:'equipment' },
};

// â”€â”€ Panel IDs map â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
const PANEL = {
    material:  s => `mat-pick-${s}`,
    manpower:  s => `mp-pick-${s}`,
    equipment: s => `eq-pick-${s}`,
};
const SEL_ID = {
    material:  s => `mat-sel-${s}`,
    manpower:  s => `mp-sel-${s}`,
    equipment: s => `eq-sel-${s}`,
};
const UTAG = {
    material:  s => `mat-utag-${s}`,
    manpower:  s => `mp-utag-${s}`,
    equipment: s => `eq-utag-${s}`,
};
const ADDBTN = {
    material:  s => `mat-addbtn-${s}`,
    manpower:  s => `mp-addbtn-${s}`,
    equipment: s => `eq-addbtn-${s}`,
};

// â”€â”€ Pending picks â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
const picks = {};   // key: "sIdx_type" -> { name, unit, rate }

// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
// Panel helpers
// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function closePanel(id) {
    document.getElementById(id)?.classList.remove('active');
}

function closeAllPanels(sIdx) {
    ['material','manpower','equipment'].forEach(t =>
        document.getElementById(PANEL[t](sIdx))?.classList.remove('active')
    );
    document.getElementById(`tmpl-wrap-${sIdx}`)?.classList.remove('active');
}

// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
// TEMPLATE PANEL
// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function openTmplPanel(sIdx, sectionUnit) {
    closeAllPanels(sIdx);
    const sel = document.getElementById(`tmpl-sel-${sIdx}`);
    sel.innerHTML = '<option value="">â€” Pick a template â€”</option>';
    let list = SW.filter(sw => !sectionUnit || !sw.unit || sw.unit === sectionUnit);
    if (!list.length) list = SW;
    list.forEach(sw => {
        sel.appendChild(new Option(`${sw.name}  (${sw.unit || 'any'})  â€” ${sw.category}`, sw.id));
    });
    document.getElementById(`tmpl-wrap-${sIdx}`).classList.add('active');
}

function expandTemplate(sIdx, swId) {
    if (!swId) return;
    const sw = SW.find(s => String(s.id) === String(swId));
    if (!sw) return;
    (sw.materials  || []).forEach(item => addPrefilledRow(sIdx, 'material',  sw.name, item, null));
    (sw.manpower   || []).forEach(item => addPrefilledRow(sIdx, 'manpower',  sw.name, item, null));
    (sw.equipment  || []).forEach(item => addPrefilledRow(sIdx, 'equipment', sw.name, item, null));
    closeAllPanels(sIdx);
    updateCount();
}

// ─────────────────────────────────────────────────────────
// MANUAL PICK PANELS
// ─────────────────────────────────────────────────────────
function openPickPanel(sIdx, type) {
    closeAllPanels(sIdx);
    // reset
    const selEl   = document.getElementById(SEL_ID[type](sIdx));
    const addBtn  = document.getElementById(ADDBTN[type](sIdx));
    const unitTag = document.getElementById(UTAG[type](sIdx));
    selEl.value          = '';
    addBtn.style.display = 'none';
    unitTag.textContent  = 'unit: —';
    picks[`${sIdx}_${type}`] = null;
    document.getElementById(PANEL[type](sIdx)).classList.add('active');
}

function onPickChange(sIdx, type) {
    const selEl   = document.getElementById(SEL_ID[type](sIdx));
    const addBtn  = document.getElementById(ADDBTN[type](sIdx));
    const unitTag = document.getElementById(UTAG[type](sIdx));
    if (!selEl.value) {
        addBtn.style.display = 'none';
        unitTag.textContent  = 'unit: —';
        picks[`${sIdx}_${type}`] = null;
        return;
    }
    const opt  = selEl.options[selEl.selectedIndex];
    const name = opt.dataset.name;
    const unit = opt.dataset.unit;
    const rate = parseFloat(opt.dataset.rate) || 0;
    picks[`${sIdx}_${type}`] = { name, unit, rate };
    unitTag.textContent  = `unit: ${unit}`;
    addBtn.style.display = 'inline-flex';
}

function addPickedRow(sIdx, type) {
    const pick = picks[`${sIdx}_${type}`];
    if (!pick) return;
    addPrefilledRow(sIdx, type, 'Manual', { name: pick.name, quantity: 1, unit: pick.unit }, pick.rate);
    closePanel(PANEL[type](sIdx));
    picks[`${sIdx}_${type}`] = null;
}

// ─────────────────────────────────────────────────────────
// FREE-TEXT MANUAL ROW
// ─────────────────────────────────────────────────────────
function addManualFreeRow(sIdx) {
    closeAllPanels(sIdx);
    const tbody = document.getElementById(`tbody-${sIdx}`);
    const total = parseFloat(tbody.dataset.sectionTotal) || 0;
    const unit  = tbody.dataset.sectionUnit || '';
    const rowId = `row-${sIdx}-${Date.now()}`;

    const tr = document.createElement('tr');
    tr.id = rowId;
    tr.innerHTML = `
        <td class="text-center">
            <input type="checkbox" class="form-check-input row-inc" checked onchange="updateBudget();updateCount()">
        </td>
        <td>
            <select class="form-select form-select-sm" onchange="this.nextElementSibling.value=this.value;calcRow('${sIdx}','${rowId}');updateBudget()">
                <option value="material">Material</option>
                <option value="manpower">Manpower</option>
                <option value="equipment">Equipment</option>
            </select>
            <input type="hidden" class="r-type" value="material">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm r-name-h" placeholder="Resource name...">
        </td>
        <td><span class="text-muted small">Manual</span></td>
        <td>
            <div class="input-group input-group-sm">
                <input type="number" step="0.0001" min="0" class="form-control r-ratio"
                       value="1" oninput="calcRow('${sIdx}','${rowId}')">
                <span class="input-group-text" style="font-size:10px;">/${unit||'unit'}</span>
            </div>
        </td>
        <td>
            <input type="number" step="0.01" min="0" class="form-control form-control-sm r-rate"
                   value="0" placeholder="0.00" oninput="calcRow('${sIdx}','${rowId}')">
        </td>
        <td>
            <span class="result-qty" id="rq-${rowId}">${total.toFixed(3)}</span>
            <span class="text-muted small ms-1">${unit}</span>
            <input type="hidden" class="r-qty"  value="${total.toFixed(3)}">
            <input type="hidden" class="r-unit" value="${unit}">
        </td>
        <td style="background:#f8fafc;">
            <div id="pdr-${rowId}" class="fw-bold text-primary" style="font-size:12px;">—</div>
        </td>
        <td>
            <span class="fw-semibold text-dark" id="rc-${rowId}">0.00</span>
            <input type="hidden" class="r-cost" value="0">
        </td>
        <td>
            <button type="button" class="remove-btn" onclick="removeRow('${rowId}')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    const sel = tr.querySelector('select');
    sel.addEventListener('change', () => tr.querySelector('.r-type').value = sel.value);
    calcRow(sIdx, rowId);
    updateCount();
}

// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
// PRE-FILLED ROW (template OR registered pick)
// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function addPrefilledRow(sIdx, type, source, item, overrideRate) {
    const tbody  = document.getElementById(`tbody-${sIdx}`);
    const total  = parseFloat(tbody.dataset.sectionTotal) || 0;
    const secUnt = tbody.dataset.sectionUnit || '';
    const cfg    = TYPE_CFG[type] || TYPE_CFG.material;
    const rowId  = `row-${sIdx}-${Date.now()}-${Math.random().toString(36).slice(2,6)}`;

    const ratio  = parseFloat(item.quantity) || 1;
    const qty    = parseFloat((total * ratio).toFixed(3));
    const rate   = (overrideRate !== null && overrideRate !== undefined) ? overrideRate : 0;
    const cost   = parseFloat((qty * rate).toFixed(2));

    const tr = document.createElement('tr');
    tr.id = rowId;
    tr.innerHTML = `
        <td class="text-center">
            <input type="checkbox" class="form-check-input row-inc" checked onchange="updateBudget();updateCount()">
        </td>
        <td>
            <span class="type-badge ${cfg.cls}">
                <i class="fa-solid ${cfg.icon}"></i> ${cfg.label}
            </span>
            <input type="hidden" class="r-type" value="${type}">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm r-name-h" value="${esc(item.name)}">
        </td>
        <td>
            <span class="text-muted small fw-semibold">${esc(source)}</span>
        </td>
        <td>
            <div class="input-group input-group-sm">
                <input type="number" step="0.0001" min="0" class="form-control r-ratio"
                       value="${ratio}" oninput="calcRow('${sIdx}','${rowId}')">
                <span class="input-group-text" style="font-size:10px;">/${secUnt||'unit'}</span>
            </div>
        </td>
        <td>
            <input type="number" step="0.01" min="0" class="form-control form-control-sm r-rate"
                   value="${rate}" placeholder="0.00" oninput="calcRow('${sIdx}','${rowId}')">
        </td>
        <td>
            <span class="result-qty" id="rq-${rowId}">${qty.toFixed(3)}</span>
            <span class="text-muted small ms-1">${item.unit || secUnt}</span>
            <input type="hidden" class="r-qty"  value="${qty.toFixed(3)}">
            <input type="hidden" class="r-unit" value="${item.unit || secUnt}">
        </td>
        <td style="background:#f8fafc;">
            <div id="pdr-${rowId}" class="fw-bold text-primary" style="font-size:12px;">—</div>
        </td>
        <td>
            <span class="fw-semibold text-dark" id="rc-${rowId}">${cost.toLocaleString('en-US',{minimumFractionDigits:2})}</span>
            <input type="hidden" class="r-cost" value="${cost}">
        </td>
        <td>
            <button type="button" class="remove-btn" onclick="removeRow('${rowId}')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    calcRow(sIdx, rowId);
    updateBudget();
}

function calcRow(sIdx, rowId) {
    const tbody = document.getElementById(`tbody-${sIdx}`);
    const total = parseFloat(tbody?.dataset.datasetTotal || tbody?.dataset.sectionTotal) || 0;
    const row   = document.getElementById(rowId);
    if (!row) return;

    const ratio = parseFloat(row.querySelector('.r-ratio').value) || 1;
    const rate  = parseFloat(row.querySelector('.r-rate').value)  || 0;
    const type  = row.querySelector('.r-type')?.value || 'material';
    const dur   = Math.max(1, parseFloat(document.getElementById(`sec-dur-${sIdx}`)?.value) || 1);

    // For Manpower and Equipment, total effort (man-days / machine-hours) scales with Schedule Duration (days)
    let qty = 0;
    if (type === 'manpower' || type === 'equipment') {
        qty = parseFloat((total * ratio * dur).toFixed(3));
    } else {
        qty = parseFloat((total * ratio).toFixed(3));
    }
    const cost  = parseFloat((qty * rate).toFixed(2));

    row.querySelector(`#rq-${rowId}`).textContent = qty;
    row.querySelector('.r-qty').value  = qty;
    row.querySelector(`#rc-${rowId}`).textContent = cost.toLocaleString('en-US',{minimumFractionDigits:2});
    row.querySelector('.r-cost').value = cost;

    // Daily Requirement calculation for Manpower & Equipment
    const pdrEl = row.querySelector(`#pdr-${rowId}`);
    if (pdrEl) {
        if (type === 'manpower' || type === 'equipment') {
            const perDay = (qty / dur).toFixed(2);
            const unitLabel = type === 'manpower' ? 'per day' : '/ day';
            pdrEl.innerHTML = `<span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1"><i class="fa-solid fa-bolt me-1"></i>${perDay} ${unitLabel}</span>`;
        } else {
            pdrEl.innerHTML = `<span class="text-muted" style="font-size:11px;">N/A</span>`;
        }
    }

    updateBudget();
}

function updateAllSectionRows(sIdx) {
    document.querySelectorAll(`#tbody-${sIdx} tr`).forEach(row => {
        calcRow(sIdx, row.id);
    });
}

function updateBudget() {
    const totals = { material:0, manpower:0, equipment:0 };
    document.querySelectorAll('[id^="tbody-"] tr').forEach(row => {
        const cb = row.querySelector('input[type=checkbox]');
        if (!cb?.checked) return;
        const type = row.querySelector('.r-type')?.value;
        const cost = parseFloat(row.querySelector('.r-cost')?.value) || 0;
        if (type && totals[type] !== undefined) totals[type] += cost;
    });
    document.getElementById('budget-material').textContent  = totals.material.toLocaleString('en-US',{minimumFractionDigits:2});
    document.getElementById('budget-manpower').textContent  = totals.manpower.toLocaleString('en-US',{minimumFractionDigits:2});
    document.getElementById('budget-equipment').textContent = totals.equipment.toLocaleString('en-US',{minimumFractionDigits:2});
    document.getElementById('budget-total').textContent = (totals.material+totals.manpower+totals.equipment).toLocaleString('en-US',{minimumFractionDigits:2});
}

function updateCount() {
    const n = document.querySelectorAll('[id^="tbody-"] tr').length;
    document.getElementById('rc-label').textContent = `${n} resource${n!==1?'s':''}`;
}

function removeRow(id) {
    document.getElementById(id)?.remove();
    updateBudget(); updateCount();
}

// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
// SECTION SELECT-ALL
// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
document.addEventListener('change', e => {
    if (!e.target.classList.contains('sec-all')) return;
    const s = e.target.dataset.sec;
    document.querySelectorAll(`#tbody-${s} input[type=checkbox]`).forEach(cb => cb.checked = e.target.checked);
    updateBudget(); updateCount();
});

// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
// SUBMIT
// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function submitConversion() {
    const planName  = document.getElementById('meta_plan_name').value.trim();
    const startDate = document.getElementById('meta_start_date').value;
    const endDate   = document.getElementById('meta_end_date').value;
    if (!planName || !startDate || !endDate) {
        alert('Please fill in Plan Name, Start Date and End Date first.');
        return;
    }
    document.getElementById('f_plan_name').value  = planName;
    document.getElementById('f_start_date').value = startDate;
    document.getElementById('f_end_date').value   = endDate;
    document.getElementById('f_notes').value      = document.getElementById('meta_notes').value;

    const body = document.getElementById('f_body');
    body.innerHTML = '';
    let hasAny = false;

    @foreach($takeoff->sections as $sIdx => $section)
    (function() {
        const si    = {{ $sIdx }};
        const tbody = document.getElementById(`tbody-${si}`);
        addH(body, `sections[${si}][section_id]`,   '{{ $section->id }}');
        addH(body, `sections[${si}][section_name]`, '{{ addslashes($section->name) }}');
        addH(body, `sections[${si}][section_total]`,'{{ $section->total_quantity }}');
        let ri = 0;
        tbody.querySelectorAll('tr').forEach(row => {
            const cb = row.querySelector('input[type=checkbox]');
            if (!cb?.checked) return;
            const name = (row.querySelector('.r-name-h')?.value || '').trim();
            if (!name) return;
            hasAny = true;
            addH(body, `sections[${si}][resources][${ri}][name]`,  name);
            addH(body, `sections[${si}][resources][${ri}][type]`,  row.querySelector('.r-type')?.value  || 'material');
            addH(body, `sections[${si}][resources][${ri}][ratio]`, row.querySelector('.r-ratio')?.value || '1');
            addH(body, `sections[${si}][resources][${ri}][rate]`,  row.querySelector('.r-rate')?.value  || '0');
            addH(body, `sections[${si}][resources][${ri}][qty]`,   row.querySelector('.r-qty')?.value   || '0');
            addH(body, `sections[${si}][resources][${ri}][unit]`,  row.querySelector('.r-unit')?.value  || '');
            ri++;
        });
    })();
    @endforeach

    if (!hasAny) {
        alert('Please add at least one resource row with a name before creating the plan.');
        return;
    }
    document.getElementById('mainForm').submit();
}

function addH(container, name, val) {
    const i = document.createElement('input');
    i.type = 'hidden'; i.name = name; i.value = val;
    container.appendChild(i);
}

function esc(str) {
    return String(str||'')
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
@endpush
