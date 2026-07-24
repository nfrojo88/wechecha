@extends('layouts.app')

@section('title', 'Create Work Order')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center mb-4 gap-2">
        <a href="{{ route('eng-schedule.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color:var(--brand-800)">
                <i class="fa-solid fa-plus-circle me-2 text-primary"></i>New Work Order
            </h1>
            <p class="text-muted small mb-0">Assign work to one or more engineers</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('eng-schedule.store') }}" id="wo-form">
        @csrf
        <div class="row g-4">

            {{-- LEFT COLUMN --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-semibold py-3">
                        <i class="fa-solid fa-clipboard-list me-2 text-primary"></i>Task Details
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="e.g. Site Inspection – Block A">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Detailed description of the work...">{{ old('description') }}</textarea>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Location / Site</label>
                                <input type="text" name="location" class="form-control" value="{{ old('location') }}" placeholder="e.g. Block C, Floor 3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category</label>
                                <input type="text" name="category" class="form-control" list="cat-list" value="{{ old('category') }}" placeholder="e.g. Inspection, Installation">
                                <datalist id="cat-list">
                                    <option>Inspection</option><option>Installation</option><option>Maintenance</option>
                                    <option>Measurement</option><option>Quality Check</option><option>Safety Check</option>
                                </datalist>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Start Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_datetime" class="form-control"
                                       value="{{ old('start_datetime', $prefill['start_datetime'] ?? '') }}" required id="start-dt">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">End Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_datetime" class="form-control"
                                       value="{{ old('end_datetime', $prefill['end_datetime'] ?? '') }}" required id="end-dt">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes / Special Instructions</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Recurrence --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-semibold py-3">
                        <i class="fa-solid fa-rotate me-2 text-info"></i>Recurrence (Optional)
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Repeat</label>
                                <select name="recurrence_type" class="form-select" id="recurrence-type">
                                    <option value="none" {{ old('recurrence_type','none') == 'none' ? 'selected' : '' }}>No Repeat</option>
                                    <option value="daily"   {{ old('recurrence_type') == 'daily'   ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly"  {{ old('recurrence_type') == 'weekly'  ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ old('recurrence_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                </select>
                            </div>
                            <div class="col-md-4" id="recurrence-interval-col" style="display:none">
                                <label class="form-label fw-semibold">Every</label>
                                <div class="input-group">
                                    <input type="number" name="recurrence_interval" class="form-control" value="{{ old('recurrence_interval', 1) }}" min="1" max="30">
                                    <span class="input-group-text" id="rec-unit">day(s)</span>
                                </div>
                            </div>
                            <div class="col-md-4" id="recurrence-end-col" style="display:none">
                                <label class="form-label fw-semibold">Until</label>
                                <input type="date" name="recurrence_end_date" class="form-control" value="{{ old('recurrence_end_date') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN --}}
            <div class="col-lg-4">
                {{-- Assignment --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-semibold py-3">
                        <i class="fa-solid fa-users-gear me-2 text-warning"></i>Assign Engineers
                    </div>
                    <div class="card-body">
                        <label class="form-label fw-semibold">Select Engineers <span class="text-danger">*</span></label>
                        <div style="max-height:260px; overflow-y:auto; border:1px solid #dee2e6; border-radius:6px; padding:8px;">
                            @foreach($engineers as $eng)
                            <div class="form-check mb-1">
                                <input class="form-check-input engineer-check" type="checkbox"
                                       name="engineer_ids[]" value="{{ $eng->id }}" id="eng-{{ $eng->id }}"
                                       {{ (is_array(old('engineer_ids')) && in_array($eng->id, old('engineer_ids'))) || ($prefill['engineer_id'] ?? null) == $eng->id ? 'checked' : '' }}>
                                <label class="form-check-label" for="eng-{{ $eng->id }}">
                                    <span class="fw-semibold">{{ $eng->name }}</span>
                                    <small class="text-muted ms-1">{{ ucwords(str_replace('_',' ', $eng->getRoleNames()->first() ?? '')) }}</small>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <p class="form-text text-muted mt-1 small"><i class="fa-solid fa-info-circle me-1"></i>You can select multiple engineers for a team job.</p>

                        {{-- Conflict warning area --}}
                        <div id="conflict-warning" class="alert alert-warning mt-2 d-none small p-2">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                            <strong>Scheduling Conflict!</strong>
                            <ul id="conflict-list" class="mb-0 mt-1 ps-3"></ul>
                        </div>
                    </div>
                </div>

                {{-- Priority & Links --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-semibold py-3">
                        <i class="fa-solid fa-sliders me-2 text-danger"></i>Settings
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                @foreach(['low' => 'Low','medium' => 'Medium','high' => 'High','urgent' => 'Urgent'] as $val => $lbl)
                                    <option value="{{ $val }}" {{ old('priority','medium') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Linked Project <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-select" required>
                                <option value="">— Select Project —</option>
                                @foreach($projects as $proj)
                                    <option value="{{ $proj->id }}" {{ old('project_id') == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Linked Schedule (Optional)</label>
                            <select name="schedule_id" class="form-select">
                                <option value="">— None —</option>
                                @foreach($schedules as $sched)
                                    <option value="{{ $sched->id }}" {{ old('schedule_id') == $sched->id ? 'selected' : '' }}>
                                        [{{ $sched->project->name ?? '?' }}] {{ $sched->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-paper-plane me-1"></i> Create & Notify Engineers
                            </button>
                            <a href="{{ route('eng-schedule.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// ── Recurrence UI ───────────────────────────────────────────────────────────
const recType  = document.getElementById('recurrence-type');
const intCol   = document.getElementById('recurrence-interval-col');
const endCol   = document.getElementById('recurrence-end-col');
const recUnit  = document.getElementById('rec-unit');

recType.addEventListener('change', function() {
    const show = this.value !== 'none';
    intCol.style.display = show ? '' : 'none';
    endCol.style.display = show ? '' : 'none';
    recUnit.textContent  = this.value === 'daily' ? 'day(s)' : this.value === 'weekly' ? 'week(s)' : 'month(s)';
});

// ── Conflict Check ──────────────────────────────────────────────────────────
function checkConflicts() {
    const checks   = [...document.querySelectorAll('.engineer-check:checked')].map(e => e.value);
    const startDt  = document.getElementById('start-dt').value;
    const endDt    = document.getElementById('end-dt').value;
    const warning  = document.getElementById('conflict-warning');
    const list     = document.getElementById('conflict-list');

    if (!checks.length || !startDt || !endDt) { warning.classList.add('d-none'); return; }

    const body = JSON.stringify({ engineer_ids: checks, start_datetime: startDt, end_datetime: endDt });
    fetch('{{ route("eng-schedule.conflict-check") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body
    })
    .then(r => r.json())
    .then(data => {
        if (data.has_conflicts) {
            list.innerHTML = data.conflicts.map(c =>
                `<li><strong>${c.title}</strong> (${c.engineers}) — ${c.start_datetime}</li>`
            ).join('');
            warning.classList.remove('d-none');
        } else {
            warning.classList.add('d-none');
        }
    });
}

document.querySelectorAll('.engineer-check').forEach(c => c.addEventListener('change', checkConflicts));
document.getElementById('start-dt').addEventListener('change', checkConflicts);
document.getElementById('end-dt').addEventListener('change', checkConflicts);
</script>
@endsection
