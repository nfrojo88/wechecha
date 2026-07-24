@extends('layouts.app')
@section('title', 'Subcontractor Agreements')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-handshake me-2 text-primary"></i>Subcontractor Agreements
            </h1>
            <p class="text-muted mt-1">Manage subcontractor work agreements and payments</p>
        </div>
        <a href="{{ route('subcon-agreements.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Create Agreement
        </a>
    </div>

    <!-- Status Tabs -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="btn-group" role="group">
                <a href="{{ route('subcon-agreements.index') }}" class="btn btn-outline-primary {{ request('status') == null ? 'active' : '' }}">
                    All <span class="badge bg-primary ms-2">{{ $statusCounts['all'] ?? 0 }}</span>
                </a>
                <a href="{{ route('subcon-agreements.index', ['status' => 'draft']) }}" 
                   class="btn btn-outline-secondary {{ request('status') == 'draft' ? 'active' : '' }}">
                    Draft <span class="badge bg-secondary ms-2">{{ $statusCounts['draft'] ?? 0 }}</span>
                </a>
                <a href="{{ route('subcon-agreements.index', ['status' => 'pending']) }}" 
                   class="btn btn-outline-warning {{ request('status') == 'pending' ? 'active' : '' }}">
                    Pending <span class="badge bg-warning ms-2">{{ $statusCounts['pending'] ?? 0 }}</span>
                </a>
                <a href="{{ route('subcon-agreements.index', ['status' => 'approved']) }}" 
                   class="btn btn-outline-success {{ request('status') == 'approved' ? 'active' : '' }}">
                    Approved <span class="badge bg-success ms-2">{{ $statusCounts['approved'] ?? 0 }}</span>
                </a>
                <a href="{{ route('subcon-agreements.index', ['status' => 'active']) }}" 
                   class="btn btn-outline-info {{ request('status') == 'active' ? 'active' : '' }}">
                    Active <span class="badge bg-info ms-2">{{ $statusCounts['active'] ?? 0 }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Agreements Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Agreement #</th>
                            <th>Project</th>
                            <th>Subcontractor</th>
                            <th>Duration</th>
                            <th class="text-end">Amount</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agreements as $agreement)
                        <tr>
                            <td>
                                <strong>{{ $agreement->agreement_no }}</strong>
                            </td>
                            <td>
                                <small>{{ $agreement->project->project_name ?? $agreement->project->name ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <small>{{ $agreement->supplier->name ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $agreement->start_date->format('M d') }} - {{ $agreement->end_date->format('M d, Y') }}
                                </small>
                            </td>
                            <td class="text-end">
                                <strong>{{ number_format($agreement->total_amount ?? 0, 2) }} ETB</strong>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'draft' => 'secondary',
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'active' => 'info',
                                        'completed' => 'success',
                                        'rejected' => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$agreement->status] ?? 'secondary' }}">
                                    {{ ucfirst($agreement->status) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $agreement->createdBy->name ?? 'System' }}</small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('subcon-agreements.show', $agreement) }}" class="btn btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($agreement->status === 'draft')
                                    <form action="{{ route('subcon-agreements.approve', $agreement) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Approve this agreement?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endif

                                    @if($agreement->status === 'approved')
                                    <form action="{{ route('subcon-agreements.activate', $agreement) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Activate this agreement?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-info" title="Activate">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-handshake fa-3x mb-3 opacity-50"></i>
                                <p class="mb-0">No agreements found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($agreements->hasPages())
        <div class="card-footer bg-light">
            {{ $agreements->links() }}
        </div>
        @endif
    </div>
</div>

@endsection
