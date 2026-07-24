@extends('layouts.app')
@section('title', 'Project Resource Report')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">
                <i class="fa-solid fa-chart-bar text-info me-2"></i>Project Resource Report
            </h1>
            <p class="text-muted mb-0 small">Live resource allocation overview across all active projects</p>
        </div>
        <div>
            <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="fa-solid fa-print me-1"></i>Print Report
            </button>
        </div>
    </div>

    @if(count($reportData) === 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fa-solid fa-chart-bar fa-3x text-muted mb-3 d-block"></i>
            <h5 class="text-muted">No active projects</h5>
            <p class="small text-muted">Resource report will appear here once active projects are added.</p>
        </div>
    </div>
    @else
    {{-- Resource Cards --}}
    <div class="row g-3 mb-4">
        @php
            $totalManpower = array_sum(array_column($reportData, 'manpower_active'));
            $totalEquipment = array_sum(array_column($reportData, 'equipment_active'));
            $totalCement = array_sum(array_column($reportData, 'cement_qty'));
            $totalSteel = array_sum(array_column($reportData, 'steel_qty'));
        @endphp
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3" style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;">
                <i class="fa-solid fa-users fa-2x opacity-75 mb-2"></i>
                <div class="fs-2 fw-bold">{{ $totalManpower }}</div>
                <div class="small opacity-75">Total Active Manpower</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3" style="background:linear-gradient(135deg,#11998e,#38ef7d);color:#fff;">
                <i class="fa-solid fa-tractor fa-2x opacity-75 mb-2"></i>
                <div class="fs-2 fw-bold">{{ $totalEquipment }}</div>
                <div class="small opacity-75">Total Equipment Units</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3" style="background:linear-gradient(135deg,#f093fb,#f5576c);color:#fff;">
                <i class="fa-solid fa-bag-shopping fa-2x opacity-75 mb-2"></i>
                <div class="fs-2 fw-bold">{{ number_format($totalCement) }}</div>
                <div class="small opacity-75">Cement (Bags)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3" style="background:linear-gradient(135deg,#4facfe,#00f2fe);color:#fff;">
                <i class="fa-solid fa-weight-scale fa-2x opacity-75 mb-2"></i>
                <div class="fs-2 fw-bold">{{ number_format($totalSteel) }}T</div>
                <div class="small opacity-75">Steel (Tons)</div>
            </div>
        </div>
    </div>

    {{-- Per-project breakdown --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h6 class="mb-0 fw-bold"><i class="fa-solid fa-table me-2 text-info"></i>Resource Breakdown by Project</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Project</th>
                            <th>Status</th>
                            <th class="text-center">Manpower</th>
                            <th class="text-center">Equipment</th>
                            <th class="text-center">Cement (Bags)</th>
                            <th class="text-center">Steel (Tons)</th>
                            <th class="text-center">Sand (m3)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $i => $row)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td>
                                <div class="fw-semibold">{{ $row['project']->name }}</div>
                                <small class="text-muted">{{ $row['project']->location ?? 'N/A' }}</small>
                            </td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td class="text-center">
                                <span class="badge bg-primary rounded-pill px-3">{{ $row['manpower_active'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success rounded-pill px-3">{{ $row['equipment_active'] }}</span>
                            </td>
                            <td class="text-center">{{ number_format($row['cement_qty']) }}</td>
                            <td class="text-center">{{ number_format($row['steel_qty']) }}</td>
                            <td class="text-center">{{ number_format($row['sand_qty']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">Totals:</td>
                            <td class="text-center">{{ $totalManpower }}</td>
                            <td class="text-center">{{ $totalEquipment }}</td>
                            <td class="text-center">{{ number_format($totalCement) }}</td>
                            <td class="text-center">{{ number_format($totalSteel) }}</td>
                            <td class="text-center">{{ number_format(array_sum(array_column($reportData, 'sand_qty'))) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
