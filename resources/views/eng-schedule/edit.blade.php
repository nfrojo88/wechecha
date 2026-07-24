@extends('layouts.app')

@section('title', 'Edit Work Order')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center mb-4 gap-2">
        <a href="{{ route('eng-schedule.show', $engSchedule) }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color:var(--brand-800)">
                <i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Edit Work Order
            </h1>
            <p class="text-muted small mb-0">{{ $engSchedule->title }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('eng-schedule.update', $engSchedule) }}">
        @csrf @method('PUT')
        <div class="row g-4">

            {{-- LEFT --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-semibold py-3">
                        <i class="fa-solid fa-clipboard-list me-2 text-primary"></i>Task Details
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $engSchedule->title) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $engSchedule->description) }}</textarea>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Location / Site</label>
                                <input type="text" name="location" class="form-control" value="{{ old('location', $engSchedule->location) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category</label>
                                <input type="text" name="category" class="form-control" list="cat-list" value="{{ old('category', $engSchedule->category) }}">
                                <datalist id="cat-list">
                                    <option>Inspection</option><option>Installation</option><option>Maintenance</option>
                                    <option>Measurement</option><option>Quality Check</option><option>Safety Check</option>
                                </datalist>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Start Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_datetime" class="form-control" id="start-dt"
                                       value="{{ old('start_datetime', $engSchedule->start_datetime->format('Y-m-d\TH:i')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">End Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_datetime" class="form-control" id="end-dt"
                                       value="{{ old('end_datetime', $engSchedule->end_datetime->format('Y-m-d\TH:i')) }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $engSchedule->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT --}}
            <div class="col-lg-4">
                {{-- Assign Engineers --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-semibold py-3">
                        <i class="fa-solid fa-users-gear me-2 text-warning"></i>Assigned Engineers
                    </div>
                    <div class="card-body">
                        <div style="max-height:250px; overflow-y:auto; border:1px solid #dee2e6; border-radius:6px; padding:8px;">
                            @foreach($engineers as $eng)
                            <div class="form-check mb-1">
                                <input class="form-check-input engineer-check" type="checkbox"
                                       name="engineer_ids[]" value="{{ $eng->id }}" id="eng-{{ $eng->id }}"
                                       {{ in_array($eng->id, old('engineer_ids', $assigned)) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="eng-{{ $eng->id }}">{{ $eng->name }}</label>
                            </div>
                            @endforeach
                        </div>
                        {{-- Conflict warning --}}
                        <div id="conflict-warning" class="alert alert-warning mt-2 d-none small p-2">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                            <strong>Conflict!</strong>
                            <ul id="conflict-list" class="mb-0 mt-1 ps-3"></ul>
                        </div>
                    </div>
                </div>

                {{-- Settings --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-semibold py-3">
                        <i class="fa-solid fa-sliders me-2 text-danger"></i>Settings
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                @foreach(['low'=>'Low','medium'=>'Medium','high'=>'High','urgent'=>'Urgent'] as $val => $lbl)
                                    <option value="{{ $val }}" {{ old('priority', $engSchedule->priority) == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Project <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-select" required>
                                <option value="">— Select —</option>
                                @foreach($projects as $proj)
                                    <option value="{{ $proj->id }}" {{ old('project_id', $engSchedule->project_id) == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Linked Schedule</label>
                            <select name="schedule_id" class="form-select">
                                <option value="">— None —</option>
                                @foreach($schedules as $sched)
                                    <option value="{{ $sched->id }}" {{ old('schedule_id', $engSchedule->schedule_id) == $sched->id ? 'selected' : '' }}>
                                        [{{ $sched->project->name ?? '?' }}] {{ $sched->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-save me-1"></i> Save Changes
                            </button>
                            <a href="{{ route('eng-schedule.show', $engSchedule) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function checkConflicts() {
    const checks  = [...document.querySelectorAll('.engineer-check:checked')].map(e => e.value);
    const startDt = document.getElementById('start-dt').value;
    const endDt   = document.getElementById('end-dt').value;
    const warning = document.getElementById('conflict-warning');
    const list    = document.getElementById('conflict-list');
    if (!checks.length || !startDt || !endDt) { warning.classList.add('d-none'); return; }
    fetch('{{ route("eng-schedule.conflict-check") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ engineer_ids: checks, start_datetime: startDt, end_datetime: endDt, exclude_id: {{ $engSchedule->id }} })
    }).then(r => r.json()).then(data => {
        if (data.has_conflicts) {
            list.innerHTML = data.conflicts.map(c => `<li><strong>${c.title}</strong> — ${c.start_datetime}</li>`).join('');
            warning.classList.remove('d-none');
        } else { warning.classList.add('d-none'); }
    });
}
document.querySelectorAll('.engineer-check').forEach(c => c.addEventListener('change', checkConflicts));
document.getElementById('start-dt').addEventListener('change', checkConflicts);
document.getElementById('end-dt').addEventListener('change', checkConflicts);
</script>
@endsection
