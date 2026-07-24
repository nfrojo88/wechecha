@extends('layouts.app')

@section('title', 'Asset Management Dashboard')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0">Asset Management Dashboard</h1>
        <small class="text-muted">Track all employee equipment and material assignments</small>
    </div>
    <div class="gap-2 d-flex">
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-info dropdown-toggle" type="button" id="reportsDropdown" data-bs-toggle="dropdown">
                <i class="fa-solid fa-chart-bar me-2"></i>Reports
            </button>
            <ul class="dropdown-menu" aria-labelledby="reportsDropdown">
                <li><a class="dropdown-item" href="{{ route('asset-reports.utilization') }}">Utilization Report</a></li>
                <li><a class="dropdown-item" href="{{ route('asset-reports.damage') }}">Damage Report</a></li>
                <li><a class="dropdown-item" href="{{ route('asset-reports.employee-allocation') }}">Employee Allocation</a></li>
            </ul>
        </div>
        <a href="{{ route('assets.export') }}" class="btn btn-sm btn-outline-primary">
            <i class="fa-solid fa-download me-2"></i>Export All
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Total Assets --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fa-solid fa-boxes-stacked fa-2x text-primary mb-2 opacity-50"></i>
                <h6 class="text-muted mb-1">Total Assets</h6>
                <h2 class="mb-0">{{ $totalAssets }}</h2>
                <small class="text-muted">All assignments</small>
            </div>
        </div>
    </div>

    {{-- Active Assignments --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fa-solid fa-computer fa-2x text-success mb-2 opacity-50"></i>
                <h6 class="text-muted mb-1">Active Assets</h6>
                <h2 class="mb-0">{{ $activeAssets }}</h2>
                <small class="text-muted">In use or assigned</small>
            </div>
        </div>
    </div>

    {{-- Returned Assets --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fa-solid fa-arrow-rotate-left fa-2x text-warning mb-2 opacity-50"></i>
                <h6 class="text-muted mb-1">Returned</h6>
                <h2 class="mb-0">{{ $returnedAssets }}</h2>
                <small class="text-muted">Available for reuse</small>
            </div>
        </div>
    </div>

    {{-- Damaged Assets --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fa-solid fa-exclamation-triangle fa-2x text-danger mb-2 opacity-50"></i>
                <h6 class="text-muted mb-1">Damaged</h6>
                <h2 class="mb-0">{{ $damagedAssets }}</h2>
                <small class="text-muted">Requires action</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Asset Value Summary --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fa-solid fa-chart-pie me-2"></i>Asset Value Distribution</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 text-center">
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Active Assets Value</small>
                        <h4 class="mb-0 text-success">Br {{ number_format($activeAssetsValue, 2) }}</h4>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Damaged Assets Value</small>
                        <h4 class="mb-0 text-danger">Br {{ number_format($damagedAssetsValue, 2) }}</h4>
                    </div>
                    <div class="col-12">
                        <div class="progress" style="height: 25px;">
                            @php
                                $totalValue = $activeAssetsValue + $damagedAssetsValue;
                                $activePercent = $totalValue > 0 ? ($activeAssetsValue / $totalValue) * 100 : 0;
                                $damagedPercent = $totalValue > 0 ? ($damagedAssetsValue / $totalValue) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-success" style="width: {{ $activePercent }}%" role="progressbar">
                                {{ round($activePercent) }}% Active
                            </div>
                            <div class="progress-bar bg-danger" style="width: {{ $damagedPercent }}%" role="progressbar">
                                {{ round($damagedPercent) }}% Damaged
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <small class="text-muted">Total Portfolio Value: <strong>Br {{ number_format($totalValue, 2) }}</strong></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Asset Categories --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fa-solid fa-list me-2"></i>Assets by Category</h5>
            </div>
            <div class="card-body">
                @if($assetsByCategory->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Count</th>
                                <th class="text-end">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assetsByCategory as $category)
                            <tr>
                                <td>
                                    <strong>{{ $category->category ?? 'Uncategorized' }}</strong>
                                </td>
                                <td class="text-end">{{ $category->total_count }}</td>
                                <td class="text-end">Br {{ number_format($category->total_value, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted mb-0">No assets categorized yet</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Top Departments --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fa-solid fa-sitemap me-2"></i>Assets by Department</h5>
            </div>
            <div class="card-body">
                @if($assetsByDepartment->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Department</th>
                                <th class="text-end">Count</th>
                                <th class="text-end">Active</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assetsByDepartment as $dept)
                            <tr>
                                <td><strong>{{ $dept->department }}</strong></td>
                                <td class="text-end">{{ $dept->total_count }}</td>
                                <td class="text-end">{{ $dept->active_count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted mb-0">No departments with assets</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fa-solid fa-history me-2"></i>Recent Activity</h5>
            </div>
            <div class="card-body">
                @if($recentActivity->count() > 0)
                <div class="timeline">
                    @foreach($recentActivity as $activity)
                    <div class="timeline-item mb-3 pb-3 border-bottom">
                        <div class="d-flex gap-2">
                            <div>
                                @if($activity->status === 'assigned')
                                    <i class="fa-solid fa-circle-plus text-success"></i>
                                @elseif($activity->status === 'returned')
                                    <i class="fa-solid fa-circle-check text-warning"></i>
                                @elseif($activity->status === 'damaged')
                                    <i class="fa-solid fa-circle-exclamation text-danger"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">
                                    <strong>{{ $activity->employee->full_name }}</strong>
                                    @if($activity->status === 'assigned')
                                        assigned
                                    @elseif($activity->status === 'returned')
                                        returned
                                    @elseif($activity->status === 'damaged')
                                        reported damage
                                    @endif
                                </small>
                                <small class="text-muted d-block">{{ $activity->product->name }}</small>
                                <small class="text-muted">{{ $activity->updated_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted mb-0">No recent activity</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- All Assets Table --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-table me-2"></i>All Asset Assignments</h5>
                <div>
                    <input type="text" class="form-control form-control-sm" id="searchTable" placeholder="Search assets..." style="width: 200px;">
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="assetsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Asset Name</th>
                                <th>Type</th>
                                <th>Assigned Date</th>
                                <th>Status</th>
                                <th>Unit Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allAssets as $asset)
                            <tr>
                                <td>
                                    <a href="{{ route('employees.show', $asset->employee) }}" class="text-decoration-none">
                                        {{ $asset->employee->full_name }}
                                    </a>
                                </td>
                                <td>{{ $asset->employee->department }}</td>
                                <td><strong>{{ $asset->product->name }}</strong></td>
                                <td>{{ $asset->product->type ?? 'General' }}</td>
                                <td>{{ $asset->assigned_date->format('d M Y') }}</td>
                                <td>
                                    @if($asset->status === 'assigned')
                                        <span class="badge bg-primary">Assigned</span>
                                    @elseif($asset->status === 'in_use')
                                        <span class="badge bg-success">In Use</span>
                                    @elseif($asset->status === 'returned')
                                        <span class="badge bg-warning">Returned</span>
                                    @elseif($asset->status === 'damaged')
                                        <span class="badge bg-danger">Damaged</span>
                                    @endif
                                </td>
                                <td>Br {{ number_format($asset->product->unit_cost ?? 0, 2) }}</td>
                                <td>
                                    <a href="{{ route('employees.show', $asset->employee) }}" class="btn btn-sm btn-outline-secondary" title="View Employee">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No assets assigned yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('searchTable').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('#assetsTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});
</script>

@endsection
