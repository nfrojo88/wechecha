@extends('layouts.app')
@section('title', 'Forecast Demand')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-chart-pie text-warning me-2"></i> Resource Forecast Demand</h1>
        <button class="btn btn-outline-primary btn-sm"><i class="fas fa-file-export me-1"></i>Export Forecast</button>
    </div>

    <!-- ERP Plan Filter Selector -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('coordinator.forecast') }}" class="row g-3 align-items-center">
                <div class="col-auto">
                    <label for="erp_plan_id" class="col-form-label fw-bold">
                        <i class="fas fa-clipboard-list text-primary me-1"></i> Select ERP Plan:
                    </label>
                </div>
                <div class="col-md-5">
                    <select name="erp_plan_id" id="erp_plan_id" class="form-select" onchange="this.form.submit()">
                        <option value="">— Show All ERP Plans —</option>
                        @foreach($erpPlans as $plan)
                            <option value="{{ $plan->id }}" {{ $selectedPlanId == $plan->id ? 'selected' : '' }}>
                                {{ $plan->project ? $plan->project->name : 'No Project' }} (Plan #{{ $plan->id }} - {{ $plan->title ?? 'Untitled Plan' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    @if($selectedPlanId)
                        <a href="{{ route('coordinator.forecast') }}" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Material Forecast -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-cubes me-2"></i> Material Demand</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Site / Project</th>
                            <th>Task</th>
                            <th>Material</th>
                            <th>Required Quantity</th>
                            <th>Date Needed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materialForecasts as $forecast)
                        <tr>
                            <td class="fw-bold">{{ $forecast['site'] }}</td>
                            <td><small class="text-muted">{{ $forecast['task_name'] }}</small></td>
                            <td>{{ $forecast['item'] }}</td>
                            <td><span class="badge bg-secondary">{{ $forecast['required_qty'] }}</span></td>
                            <td>
                                {{ \Carbon\Carbon::parse($forecast['date_needed'])->format('d M, Y') }}
                                @php
                                    $diff = \Carbon\Carbon::parse($forecast['date_needed'])->diffInDays(now(), false);
                                @endphp
                                @if($diff > 0)
                                    <small class="text-danger d-block">{{ abs((int)$diff) }} days ago</small>
                                @else
                                    <small class="text-success d-block">In {{ abs((int)$diff) }} days</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $expectedSource = 'Coordinator (ERP Plan: ' . ($forecast['plan_title'] ?? '') . ' / Task: ' . ($forecast['task_name'] ?? '') . ')';
                                    $existingMr = $existingRequests->firstWhere('source', $expectedSource);
                                @endphp

                                @if($existingMr)
                                    @php
                                        $badgeColor = match($existingMr->status) {
                                            'draft' => 'secondary',
                                            'submitted' => 'info',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default => 'primary'
                                        };
                                    @endphp
                                    <a href="{{ route('material-requests.show', $existingMr) }}" class="btn btn-sm btn-outline-{{ $badgeColor }}">
                                        <i class="fa-solid fa-check-circle me-1"></i> {{ ucfirst($existingMr->status) }} Request
                                    </a>
                                @else
                                    <a href="{{ route('material-requests.create', [
                                        'project_id' => $forecast['project_id'] ?? '',
                                        'date_needed' => \Carbon\Carbon::parse($forecast['date_needed'])->format('Y-m-d'),
                                        'material_name' => $forecast['item'] ?? '',
                                        'quantity' => $forecast['raw_quantity'] ?? '',
                                        'unit' => $forecast['unit'] ?? '',
                                        'source' => $expectedSource,
                                        'redirect_back' => url()->full()
                                    ]) }}" class="btn btn-sm btn-primary">
                                        Request Material
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No material demand found for the selected plan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Equipment Forecast -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-truck-monster me-2"></i> Equipment Demand</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Site / Project</th>
                            <th>Task</th>
                            <th>Equipment</th>
                            <th>Required Quantity</th>
                            <th>Date Needed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($equipmentForecasts as $forecast)
                        <tr>
                            <td class="fw-bold">{{ $forecast['site'] }}</td>
                            <td><small class="text-muted">{{ $forecast['task_name'] }}</small></td>
                            <td>{{ $forecast['item'] }}</td>
                            <td><span class="badge bg-secondary">{{ $forecast['required_qty'] }}</span></td>
                            <td>
                                {{ \Carbon\Carbon::parse($forecast['date_needed'])->format('d M, Y') }}
                                @php
                                    $diff = \Carbon\Carbon::parse($forecast['date_needed'])->diffInDays(now(), false);
                                @endphp
                                @if($diff > 0)
                                    <small class="text-danger d-block">{{ abs((int)$diff) }} days ago</small>
                                @else
                                    <small class="text-success d-block">In {{ abs((int)$diff) }} days</small>
                                @endif
                            </td>
                            <td><a href="#" class="btn btn-sm btn-success">Schedule Transfer</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No equipment demand found for the selected plan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Manpower Forecast -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-users me-2"></i> Manpower Demand</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Site / Project</th>
                            <th>Task</th>
                            <th>Role / Trade</th>
                            <th>Required Quantity</th>
                            <th>Date Needed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($manpowerForecasts as $forecast)
                        <tr>
                            <td class="fw-bold">{{ $forecast['site'] }}</td>
                            <td><small class="text-muted">{{ $forecast['task_name'] }}</small></td>
                            <td>{{ $forecast['item'] }}</td>
                            <td><span class="badge bg-secondary">{{ $forecast['required_qty'] }}</span></td>
                            <td>
                                {{ \Carbon\Carbon::parse($forecast['date_needed'])->format('d M, Y') }}
                                @php
                                    $diff = \Carbon\Carbon::parse($forecast['date_needed'])->diffInDays(now(), false);
                                @endphp
                                @if($diff > 0)
                                    <small class="text-danger d-block">{{ abs((int)$diff) }} days ago</small>
                                @else
                                    <small class="text-success d-block">In {{ abs((int)$diff) }} days</small>
                                @endif
                            </td>
                            <td><a href="#" class="btn btn-sm btn-info text-white">Request HR</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No manpower demand found for the selected plan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

