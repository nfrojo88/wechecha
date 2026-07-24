@extends('layouts.app')

@section('title', 'Asset Damage Report')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0">Asset Damage Report</h1>
        <small class="text-muted">All reported damage incidents and asset losses</small>
    </div>
    <div class="gap-2 d-flex">
        <a href="{{ route('asset-reports.export-damage') }}" class="btn btn-sm btn-outline-primary">
            <i class="fa-solid fa-download me-2"></i>Export CSV
        </a>
        <a href="{{ route('assets.dashboard') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

{{-- Damage Statistics --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fa-solid fa-exclamation-triangle fa-2x text-danger mb-2 opacity-50"></i>
                <h6 class="text-muted mb-1">Total Damaged</h6>
                <h2 class="mb-0">{{ $damagedAssets->total() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fa-solid fa-money-bill fa-2x text-danger mb-2 opacity-50"></i>
                <h6 class="text-muted mb-1">Total Damage Value</h6>
                <h2 class="mb-0">Br {{ number_format($damageStats->total_damage_value ?? 0, 2) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fa-solid fa-calculator fa-2x text-warning mb-2 opacity-50"></i>
                <h6 class="text-muted mb-1">Average Damage Value</h6>
                <h2 class="mb-0">Br {{ number_format($damageStats->avg_damage_value ?? 0, 2) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fa-solid fa-percent fa-2x text-info mb-2 opacity-50"></i>
                <h6 class="text-muted mb-1">% of Total Assets</h6>
                @php
                    $totalAssets = \App\Models\EmployeeAsset::count();
                    $damagePercent = $totalAssets > 0 ? ($damagedAssets->total() / $totalAssets) * 100 : 0;
                @endphp
                <h2 class="mb-0">{{ round($damagePercent, 1) }}%</h2>
            </div>
        </div>
    </div>
</div>

{{-- Damaged Assets Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fa-solid fa-list me-2"></i>Damaged Assets</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Asset Name</th>
                        <th>Type</th>
                        <th>Unit Price</th>
                        <th>Assigned</th>
                        <th>Damage Reported</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($damagedAssets as $asset)
                    <tr>
                        <td>
                            <a href="{{ route('employees.show', $asset->employee) }}" class="text-decoration-none">
                                {{ $asset->employee->full_name }}
                            </a>
                        </td>
                        <td>{{ $asset->employee->department }}</td>
                        <td><strong>{{ $asset->product->name }}</strong></td>
                        <td>{{ $asset->product->type ?? 'General' }}</td>
                        <td>Br {{ number_format($asset->product->unit_cost ?? 0, 2) }}</td>
                        <td>{{ $asset->assigned_date->format('d M Y') }}</td>
                        <td>{{ $asset->updated_at->format('d M Y') }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="{{ $asset->notes }}">
                                <i class="fa-solid fa-info-circle"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No damaged assets</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($damagedAssets->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $damagedAssets->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>

@endsection
