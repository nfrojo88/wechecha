@extends('layouts.app')

@section('title', 'Planning vs Actual Cost Comparison Report')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="fa-solid fa-scale-balanced text-primary me-2"></i>Planning vs Actual Cost Comparison
            </h1>
            <p class="text-muted small mb-0">Demonstrating Two Costing Modes: <strong>Planning Mode</strong> (Uses latest market rate) vs. <strong>Actual Cost Mode</strong> (Uses real paid purchase invoice rate or weighted average store issue cost).</p>
        </div>
    </div>

    {{-- Mode Explanation Banner --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 border-start border-4 border-primary h-100 p-3">
                <h6 class="fw-bold text-primary mb-1"><i class="fa-solid fa-calculator me-2"></i>A) Planning Mode (Market Rate)</h6>
                <p class="text-muted small mb-0">Estimates are calculated as <code>Material Standard × Quantity</code> using the <strong>latest market price</strong> from market research logs (`material_prices`), capturing market rate projections for new budgets.</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 border-start border-4 border-success h-100 p-3">
                <h6 class="fw-bold text-success mb-1"><i class="fa-solid fa-file-invoice-dollar me-2"></i>B) Actual Project Cost Mode (Real Cost)</h6>
                <p class="text-muted small mb-0">Actual costs pull from real purchase invoice records (`purchase_order_items`) or store stock issue average costs (`inventory_movements`), ensuring store inventory items do <strong>not</strong> get artificially inflated by market rates.</p>
            </div>
        </div>
    </div>

    {{-- Comparison Report Table --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-transparent py-3">
            <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-list-check me-2 text-primary"></i>ERP Execution Plans Cost Variance</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small">
                        <tr>
                            <th>Plan Name</th>
                            <th>Project</th>
                            <th class="text-end" style="background:#eff6ff; color:#1e40af;">Planning Cost (Market Rates)</th>
                            <th class="text-end" style="background:#f0fdf4; color:#14532d;">Actual Cost (Real Paid/Store Cost)</th>
                            <th class="text-end">Variance (ETB)</th>
                            <th class="text-center">Variance %</th>
                            <th class="text-center">Costing Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comparisonData as $row)
                        <tr>
                            <td>
                                <span class="fw-bold text-dark d-block">{{ $row['plan_name'] }}</span>
                                <small class="text-muted">Plan #{{ $row['plan_id'] }}</small>
                            </td>
                            <td class="fw-semibold text-secondary">{{ $row['project_name'] }}</td>
                            <td class="text-end fw-bold text-primary" style="background:#f8faff;">ETB {{ number_format($row['planned_material_cost'], 2) }}</td>
                            <td class="text-end fw-bold text-success" style="background:#f8fdf9;">ETB {{ number_format($row['actual_material_cost'], 2) }}</td>
                            <td class="text-end fw-bold {{ $row['variance'] > 0 ? 'text-danger' : ($row['variance'] < 0 ? 'text-success' : 'text-muted') }}">
                                {{ $row['variance'] > 0 ? '+' : '' }}ETB {{ number_format($row['variance'], 2) }}
                            </td>
                            <td class="text-center fw-bold">
                                {{ $row['variance_pct'] > 0 ? '+' : '' }}{{ $row['variance_pct'] }}%
                            </td>
                            <td class="text-center">
                                @if($row['variance'] < 0)
                                    <span class="badge bg-success bg-opacity-10 text-success fw-bold"><i class="fa-solid fa-circle-check me-1"></i>Savings (Under Budget)</span>
                                @elseif($row['variance'] > 0)
                                    <span class="badge bg-danger bg-opacity-10 text-danger fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i>Market Rate Inflation</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary fw-semibold">Matched</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No ERP Plans available for cost comparison yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
