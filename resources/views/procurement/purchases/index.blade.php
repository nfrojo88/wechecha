@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Purchase Orders</h1>
    @can('purchases.create')
    <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Create PO
    </a>
    @endcan
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>PO Number</th>
                        <th>Supplier</th>
                        <th>Project</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pos as $po)
                    <tr>
                        <td class="fw-semibold">{{ $po->reference_number }}</td>
                        <td>{{ $po->supplier_name }}</td>
                        <td>
                            @if($po->project)
                            <a href="{{ route('projects.show', $po->project) }}" class="text-decoration-none">{{ $po->project->name }}</a>
                            @else
                            <span class="text-muted">Central/HQ</span>
                            @endif
                        </td>
                        <td class="fw-bold">{{ number_format($po->total_amount, 2) }} ETB</td>
                        <td>
                            @php
                                $badge = match($po->status) {
                                    'draft' => 'secondary',
                                    'issued' => 'primary',
                                    'partially_received' => 'warning',
                                    'completed' => 'success',
                                    'cancelled' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ strtoupper(str_replace('_', ' ', $po->status)) }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-file-invoice-dollar fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0">No purchase orders found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($pos->hasPages())
    <div class="card-footer bg-transparent">
        {{ $pos->links() }}
    </div>
    @endif
</div>
@endsection
