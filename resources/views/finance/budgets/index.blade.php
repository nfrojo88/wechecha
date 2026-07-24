@extends('layouts.app')
@section('title', 'Project Budgets')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Project Budgets</h1>
            <p class="text-muted mb-0 mt-1">Monitor company payments relatively against the allocated GM budgets.</p>
        </div>
        @hasanyrole('gm|admin|global_admin')
        <a href="{{ route('budgets.create') }}" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-1"></i>New Budget</a>
        @endhasanyrole
    </div>
    
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th class="ps-4">PROJECT</th>
                            <th>CATEGORY</th>
                            <th>PERIOD</th>
                            <th>BUDGETED</th>
                            <th>ACTUAL (PAYMENTS)</th>
                            <th>VARIANCE</th>
                            <th>UTILIZATION</th>
                            @hasanyrole('gm|admin|global_admin')
                            <th class="text-center">ACTION</th>
                            @endhasanyrole
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($budgets as $b)
                        @php
                            $utilization = $b->budgeted_amount > 0 ? ($b->actual_amount / $b->budgeted_amount) * 100 : 0;
                            $utilization = min(100, max(0, $utilization));
                            
                            $progressClass = 'bg-success';
                            if($utilization > 75) $progressClass = 'bg-warning';
                            if($utilization > 90) $progressClass = 'bg-danger';
                        @endphp
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $b->project->name }}</td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $b->category }}</span></td>
                            <td>
                                {{ ucfirst($b->period_type) }} 
                                <small class="text-muted d-block">{{ $b->period_start ? $b->period_start->format('M Y') : 'Overall' }}</small>
                            </td>
                            <td class="fw-bold">{{ number_format($b->budgeted_amount, 2) }}</td>
                            <td class="text-primary fw-bold">{{ number_format($b->actual_amount, 2) }}</td>
                            <td class="{{ $b->variance < 0 ? 'text-danger' : 'text-success' }} fw-semibold">
                                {{ $b->variance < 0 ? '-' : '+' }}{{ number_format(abs($b->variance), 2) }}
                            </td>
                            <td style="min-width: 150px;">
                                <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.8rem;">
                                    <span>{{ number_format($utilization, 1) }}%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $utilization }}%"></div>
                                </div>
                            </td>
                            @hasanyrole('gm|admin|global_admin')
                            <td class="text-center">
                                <a href="{{ route('budgets.edit', $b) }}" class="btn btn-sm btn-light text-primary border shadow-sm"><i class="fas fa-edit"></i></a>
                            </td>
                            @endhasanyrole
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-5 text-muted">No project budgets found. The GM has not sent any budgets yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">
        {{ $budgets->links() }}
    </div>
</div>
@endsection
