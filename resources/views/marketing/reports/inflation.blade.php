@extends('layouts.app')

@section('title', 'Monthly Market Inflation Report')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="fa-solid fa-chart-line text-danger me-2"></i>Monthly Market Inflation Report
            </h1>
            <p class="text-muted small mb-0">Analysis of price percentage changes and cumulative market inflation across tracked construction materials.</p>
        </div>
    </div>

    {{-- Overall Average Inflation Banner --}}
    <div class="card border-0 shadow-sm rounded-3 bg-gradient mb-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff;">
        <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <span class="text-uppercase small fw-semibold text-white-50 d-block mb-1">Overall Average Inflation Rate (Tracked Materials)</span>
                <h2 class="fw-bold mb-0 text-white display-6">
                    {{ $avgInflationRate > 0 ? '+' : '' }}{{ $avgInflationRate }}%
                </h2>
                <p class="small text-white-50 mb-0 mt-1">Calculated on-the-fly from earliest to latest recorded market rates.</p>
            </div>
            <div class="text-end">
                <span class="badge bg-danger px-3 py-2 fs-6">
                    <i class="fa-solid fa-shield-cat me-1"></i>Inflation Indicator: {{ $avgInflationRate > 5 ? 'High Market Volatility' : 'Moderate Market Shift' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Inflation Breakdown Table --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-transparent py-3">
            <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-percent text-danger me-2"></i>Material-by-Material Inflation Breakdown</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small">
                        <tr>
                            <th>#</th>
                            <th>Material Name</th>
                            <th>Category</th>
                            <th>UM</th>
                            <th class="text-end">Earliest Price</th>
                            <th class="text-end">Latest Market Price</th>
                            <th class="text-end">Price Difference</th>
                            <th class="text-center">Cumulative Inflation %</th>
                            <th class="text-center">Entries Tracked</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $idx => $row)
                        <tr>
                            <td class="text-muted small">{{ $idx + 1 }}</td>
                            <td class="fw-bold text-dark">{{ $row['product_name'] }}</td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $row['category'] }}</span></td>
                            <td><code>{{ $row['unit'] }}</code></td>
                            <td class="text-end text-muted">ETB {{ number_format($row['initial_price'], 2) }}</td>
                            <td class="text-end fw-bold text-primary">ETB {{ number_format($row['latest_price'], 2) }}</td>
                            <td class="text-end fw-semibold {{ $row['diff_amount'] > 0 ? 'text-danger' : ($row['diff_amount'] < 0 ? 'text-success' : 'text-muted') }}">
                                {{ $row['diff_amount'] > 0 ? '+' : '' }}ETB {{ number_format($row['diff_amount'], 2) }}
                            </td>
                            <td class="text-center">
                                @if($row['pct_increase'] > 0)
                                    <span class="badge bg-danger bg-opacity-10 text-danger fw-bold fs-6">🔺 +{{ $row['pct_increase'] }}%</span>
                                @elseif($row['pct_increase'] < 0)
                                    <span class="badge bg-success bg-opacity-10 text-success fw-bold fs-6">🔻 {{ $row['pct_increase'] }}%</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary fw-semibold">➖ 0.00%</span>
                                @endif
                            </td>
                            <td class="text-center"><span class="badge bg-info bg-opacity-10 text-info">{{ $row['records_count'] }} updates</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No material price history records available to calculate inflation rates yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
