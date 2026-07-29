@extends('layouts.app')
@section('title', 'Equipment Master')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-tractor me-2 text-primary"></i>Equipment Master</h1>
            <p class="text-muted mt-1">Define equipment types, daily/hourly rates, and link individual fixed asset units</p>
        </div>
        <a href="{{ route('equipment.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Equipment Type
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        @php
            $totalUnits = $equipmentMasterList->sum('total_units');
            $totalAvail = $equipmentMasterList->sum(fn($e) => $e->fixedAssetUnits->where('status','available')->count());
            $totalOnSite= $equipmentMasterList->sum(fn($e) => $e->fixedAssetUnits->where('status','on_site')->count());
        @endphp
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded p-3">
                        <i class="fas fa-list-ul fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small">Equipment Types</h6>
                        <span class="fs-4 fw-bold">{{ $equipmentMasterList->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-info bg-opacity-10 text-info rounded p-3">
                        <i class="fas fa-link fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small">Linked Units</h6>
                        <span class="fs-4 fw-bold">{{ $totalUnits }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 text-success rounded p-3">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small">Available</h6>
                        <span class="fs-4 fw-bold text-success">{{ $totalAvail }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded p-3">
                        <i class="fas fa-hard-hat fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small">On Site</h6>
                        <span class="fs-4 fw-bold text-warning">{{ $totalOnSite }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Code</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Hourly Rate</th>
                            <th>Daily Rate</th>
                            <th>Linked Units</th>
                            <th class="text-end pe-4" style="min-width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($equipmentMasterList as $eq)
                        <tr class="align-middle">
                            <td class="ps-4">
                                <code class="font-monospace fw-bold text-primary">{{ $eq->code }}</code>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $eq->name }}</span>
                                @if($eq->unit)
                                <span class="text-muted small d-block">Per {{ ucfirst($eq->unit) }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $eq->category ?: 'Equipment' }}</span>
                            </td>
                            <td>{{ number_format($eq->hourly_rate ?? 0, 2) }} ETB</td>
                            <td>{{ number_format($eq->daily_rate ?? 0, 2) }} ETB</td>
                            <td>
                                @php
                                    $units      = $eq->fixedAssetUnits;
                                    $availCount = $units->where('status','available')->count();
                                    $siteCount  = $units->where('status','on_site')->count();
                                    $maintCount = $units->where('status','maintenance')->count();
                                @endphp
                                @if($units->count() === 0)
                                    <span class="badge bg-light text-muted border"><i class="fas fa-unlink me-1"></i>0 units</span>
                                @else
                                    <div class="d-flex gap-1 flex-wrap">
                                        @if($availCount > 0)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">{{ $availCount }} Avail</span>
                                        @endif
                                        @if($siteCount > 0)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ $siteCount }} Site</span>
                                        @endif
                                        @if($maintCount > 0)
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">{{ $maintCount }} Maint</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('equipment.show', $eq) }}" class="btn btn-sm btn-outline-primary" title="View / Log Productivity">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#linkAssetModal"
                                        data-eq-id="{{ $eq->id }}"
                                        data-eq-name="{{ $eq->name }}"
                                        data-eq-route="{{ route('equipment.units.store', $eq) }}">
                                        <i class="fas fa-link me-1"></i> Link Fixed Asset
                                    </button>
                                </div>
                            </td>
                        </tr>
                        {{-- Expandable Child Row showing individual plates --}}
                        @if($eq->fixedAssetUnits->count() > 0)
                        <tr class="table-light bg-opacity-25">
                            <td colspan="7" class="py-2 ps-5 pe-4 border-top-0">
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <small class="text-muted fw-bold me-2"><i class="fas fa-level-up-alt fa-rotate-90 me-1"></i> Linked Units:</small>
                                    @foreach($eq->fixedAssetUnits as $unit)
                                    <div class="d-flex align-items-center gap-2 p-1 px-2 bg-white rounded border" style="font-size: .8rem;">
                                        <i class="fas fa-truck text-muted"></i>
                                        <span class="fw-bold text-dark">{{ $unit->plate_number ?: $unit->asset_name }}</span>
                                        @if($unit->plate_number)
                                        <span class="text-muted">({{ $unit->asset_name }})</span>
                                        @endif
                                        <span class="badge
                                            @if($unit->status==='available') bg-success
                                            @elseif($unit->status==='on_site') bg-primary
                                            @elseif($unit->status==='maintenance') bg-warning text-dark
                                            @else bg-secondary @endif" style="font-size: .7rem;">
                                            {{ ucfirst(str_replace('_',' ',$unit->status)) }}
                                        </span>
                                        <form action="{{ route('equipment.units.destroy', [$eq, $unit]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove unit binding?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0 ms-1" style="font-size: .75rem; text-decoration: none;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-tractor fa-2x mb-2 d-block text-secondary"></i>
                                No equipment types defined yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ── Link Asset Modal ── --}}
<div class="modal fade" id="linkAssetModal" tabindex="-1" aria-labelledby="linkAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="linkAssetModalLabel">
                        <i class="fas fa-link text-primary me-2"></i>Link Fixed Asset Unit
                    </h5>
                    <small class="text-muted">Linking unit for: <span id="linkAssetEquipName" class="text-primary fw-semibold">—</span></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="linkAssetForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-light border mb-4">
                        <i class="fas fa-info-circle text-primary me-1"></i>
                        Select a product registered in the Fixed Asset catalog, then add individual identification details such as plate number, model, and year.
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Asset Name *</label>
                            <input type="text" name="asset_name" class="form-control" placeholder="e.g. Sino Truck Unit 1" required>
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
                            <label class="form-label fw-semibold">Chassis / Serial No.</label>
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
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-link me-1"></i> Link Unit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const linkModal = document.getElementById('linkAssetModal');
    if (linkModal) {
        linkModal.addEventListener('show.bs.modal', function (e) {
            const btn      = e.relatedTarget;
            const eqName   = btn.getAttribute('data-eq-name');
            const eqRoute  = btn.getAttribute('data-eq-route');

            document.getElementById('linkAssetEquipName').textContent = eqName;
            document.getElementById('linkAssetForm').action = eqRoute;

            const assetNameInput = document.querySelector('#linkAssetForm input[name="asset_name"]');
            if (assetNameInput) {
                assetNameInput.placeholder = eqName + ' Unit';
            }
        });
    }
});
</script>
@endpush
