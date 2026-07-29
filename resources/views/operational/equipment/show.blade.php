@extends('layouts.app')
@section('title', 'Equipment: ' . $equipment->name)

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-tractor me-2 text-primary"></i>{{ $equipment->name }}</h1>
            <p class="text-muted mt-1">Manage details and specific physical fixed asset units linked to this equipment type</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('equipment.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Master
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#linkAssetModal">
                <i class="fas fa-link me-1"></i> Link Fixed Asset Unit
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        {{-- Side Info Card --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-info-circle me-2"></i>Master Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th class="text-muted pb-2">Code:</th>
                            <td class="font-monospace fw-bold text-dark pb-2">{{ $equipment->code }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted pb-2">Category:</th>
                            <td class="pb-2"><span class="badge bg-light text-dark border">{{ $equipment->category ?: 'Equipment' }}</span></td>
                        </tr>
                        <tr>
                            <th class="text-muted pb-2">Pricing Unit:</th>
                            <td class="pb-2">Per {{ ucfirst($equipment->unit ?: 'day') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted pb-2">Hourly Rate:</th>
                            <td class="pb-2 fw-semibold">{{ number_format($equipment->hourly_rate, 2) }} ETB</td>
                        </tr>
                        <tr>
                            <th class="text-muted pb-2">Daily Rate:</th>
                            <td class="pb-2 fw-semibold">{{ number_format($equipment->daily_rate, 2) }} ETB</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Status:</th>
                            <td>
                                <span class="badge bg-{{ $equipment->is_active ? 'success' : 'secondary' }}">
                                    {{ $equipment->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Log Productivity Form --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-chart-line me-2"></i>Log Work & Productivity</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('equipment.productivity.store', $equipment) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Select Project *</label>
                            <select name="project_id" class="form-select form-select-sm" required>
                                <option value="">— Select Site —</option>
                                @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Date *</label>
                                <input type="date" name="work_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Hours Operated *</label>
                                <input type="number" step="0.1" name="hours_operated" class="form-control form-control-sm" placeholder="e.g. 8" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Task Performed</label>
                            <input type="text" name="task_performed" class="form-control form-control-sm" placeholder="e.g. Excavation ground floor">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-save me-1"></i> Log Productivity
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Units Grid / Table --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-dark"><i class="fas fa-truck text-primary me-2"></i>Linked Fixed Asset Units</h6>
                    <span class="badge bg-primary">{{ $equipment->fixedAssetUnits->count() }} Units</span>
                </div>
                <div class="card-body p-0">
                    @if($equipment->fixedAssetUnits->count() === 0)
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-unlink fa-2x mb-2 d-block text-secondary"></i>
                        No specific units registered for this equipment type.
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Unit Name</th>
                                    <th>Plate No.</th>
                                    <th>Model / Year</th>
                                    <th>Condition</th>
                                    <th>Status</th>
                                    <th>Location</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($equipment->fixedAssetUnits as $unit)
                                <tr>
                                    <td class="ps-3 fw-semibold text-dark">{{ $unit->asset_name }}</td>
                                    <td>
                                        @if($unit->plate_number)
                                        <span class="badge bg-light text-dark border font-monospace">{{ $unit->plate_number }}</span>
                                        @else
                                        <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="small text-dark">{{ $unit->model ?: '—' }}</span>
                                        @if($unit->year)
                                        <small class="text-muted d-block">{{ $unit->year }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-capitalize small fw-bold
                                            @if($unit->condition==='good') text-success
                                            @elseif($unit->condition==='fair') text-warning
                                            @else text-danger @endif">
                                            {{ $unit->condition }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge
                                            @if($unit->status==='available') bg-success
                                            @elseif($unit->status==='on_site') bg-primary
                                            @elseif($unit->status==='maintenance') bg-warning text-dark
                                            @else bg-secondary @endif">
                                            {{ ucfirst(str_replace('_',' ',$unit->status)) }}
                                        </span>
                                    </td>
                                    <td class="small text-muted">{{ $unit->current_location ?: '—' }}</td>
                                    <td class="text-end pe-3">
                                        <div class="d-inline-flex gap-1">
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editUnitModal{{ $unit->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('equipment.units.destroy', [$equipment, $unit]) }}" method="POST" onsubmit="return confirm('Remove unit?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editUnitModal{{ $unit->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold text-dark">Edit Fixed Asset Unit</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('equipment.units.update', [$equipment, $unit]) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <div class="modal-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Plate Number</label>
                                                            <input type="text" name="plate_number" class="form-control" value="{{ $unit->plate_number }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Chassis No.</label>
                                                            <input type="text" name="chassis_number" class="form-control" value="{{ $unit->chassis_number }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Model</label>
                                                            <input type="text" name="model" class="form-control" value="{{ $unit->model }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label fw-semibold">Year</label>
                                                            <input type="number" name="year" class="form-control" value="{{ $unit->year }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label fw-semibold">Condition</label>
                                                            <select name="condition" class="form-select">
                                                                <option value="good" @selected($unit->condition==='good')>Good</option>
                                                                <option value="fair" @selected($unit->condition==='fair')>Fair</option>
                                                                <option value="maintenance" @selected($unit->condition==='maintenance')>Needs Maintenance</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label fw-semibold">Status</label>
                                                            <select name="status" class="form-select">
                                                                <option value="available" @selected($unit->status==='available')>Available</option>
                                                                <option value="on_site" @selected($unit->status==='on_site')>On Site</option>
                                                                <option value="maintenance" @selected($unit->status==='maintenance')>Under Maintenance</option>
                                                                <option value="retired" @selected($unit->status==='retired')>Retired</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label fw-semibold">Current Location</label>
                                                            <input type="text" name="current_location" class="form-control" value="{{ $unit->current_location }}">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label fw-semibold">Notes</label>
                                                            <textarea name="notes" class="form-control" rows="2">{{ $unit->notes }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Productivity Logs --}}
            @if($equipment->productivities->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 fw-bold text-dark"><i class="fas fa-chart-line text-info me-2"></i>Productivity & Work Logs</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Date</th>
                                    <th>Project Site</th>
                                    <th>Hours Worked</th>
                                    <th>Logged By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($equipment->productivities as $log)
                                <tr>
                                    <td class="ps-3 text-dark">{{ \Carbon\Carbon::parse($log->work_date)->format('d M Y') }}</td>
                                    <td><span class="text-primary">{{ $log->project->name ?? '—' }}</span></td>
                                    <td class="fw-bold text-dark">{{ $log->hours_operated }}h</td>
                                    <td class="text-muted small">{{ $log->recordedBy->name ?? '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ── Add/Link Unit Modal ── --}}
<div class="modal fade" id="linkAssetModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark">Link Fixed Asset Unit to {{ $equipment->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('equipment.units.store', $equipment) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Asset Name *</label>
                            <input type="text" name="asset_name" class="form-control" placeholder="e.g. Sino Truck Unit A" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Link to Fixed Asset Product</label>
                            <select name="product_id" class="form-select">
                                <option value="">— Select Fixed Asset Product —</option>
                                @foreach($fixedAssetProducts as $fa)
                                <option value="{{ $fa->id }}">{{ $fa->name }} ({{ $fa->sku ?? 'No SKU' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Plate Number</label>
                            <input type="text" name="plate_number" class="form-control" placeholder="e.g. AA 12345">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Chassis No.</label>
                            <input type="text" name="chassis_number" class="form-control" placeholder="Optional">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Model</label>
                            <input type="text" name="model" class="form-control" placeholder="e.g. HOWO 371HP">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Year</label>
                            <input type="number" name="year" class="form-control" placeholder="{{ date('Y') }}" min="1990" max="{{ date('Y') + 1 }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Condition</label>
                            <select name="condition" class="form-select">
                                <option value="good">Good</option>
                                <option value="fair">Fair</option>
                                <option value="maintenance">Needs Maintenance</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="available">Available</option>
                                <option value="on_site">On Site</option>
                                <option value="maintenance">Under Maintenance</option>
                                <option value="retired">Retired</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Current Location</label>
                            <input type="text" name="current_location" class="form-control" placeholder="Site / Yard">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Link Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
