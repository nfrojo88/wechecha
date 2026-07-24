@extends('layouts.app')

@section('title', 'Transfer Details - Store Manager')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h4><i class="fas fa-truck-moving me-2"></i>Transfer Details</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('store-manager.transfers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Transfer Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold">Transfer #:</td>
                            <td>{{ $transfer->transfer_no }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">From Store:</td>
                            <td>{{ $transfer->fromStore->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">To Store:</td>
                            <td>{{ $transfer->toStore->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Requested By:</td>
                            <td>{{ $transfer->requestedBy->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Required Date:</td>
                            <td>{{ $transfer->required_date ? $transfer->required_date->format('M d, Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Status:</td>
                            <td>
                                @switch($transfer->status)
                                    @case('draft')
                                        <span class="badge bg-secondary">Draft</span>
                                        @break
                                    @case('approved')
                                        <span class="badge bg-info">Approved</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success">Completed</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                        @break
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Reason:</td>
                            <td>{{ $transfer->reason ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Approval Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold">Approved By:</td>
                            <td>{{ $transfer->approvedBy->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Approved At:</td>
                            <td>{{ $transfer->approved_at ? $transfer->approved_at->format('M d, Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Rejection Reason:</td>
                            <td>{{ $transfer->rejection_reason ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Items -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h6 class="mb-0">Transfer Items</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th class="text-end">Quantity</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfer->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                <br><small class="text-muted">{{ $item->product->code ?? '' }}</small>
                            </td>
                            <td class="text-end fw-bold">{{ number_format($item->requested_quantity, 3) }}</td>
                            <td>{{ $item->unit }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No items</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
