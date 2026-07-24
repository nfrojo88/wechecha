@extends('layouts.app')

@section('title', 'Store Manager Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-0"><i class="fas fa-warehouse me-2"></i>Store Manager Dashboard</h4>
            <p class="text-muted mb-0">Manage inventory, transfers, and material requests across all stores</p>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-4 border-primary shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-muted small">Total Items</div>
                            <div class="fs-4 fw-bold text-primary">{{ number_format($kpi['total_items']) }}</div>
                        </div>
                        <i class="fas fa-boxes fs-2 text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-4 border-success shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-muted small">Total Value</div>
                            <div class="fs-4 fw-bold text-success">{{ number_format($kpi['total_value'], 2) }}</div>
                        </div>
                        <i class="fas fa-dollar-sign fs-2 text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-4 border-warning shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-muted small">Low Stock</div>
                            <div class="fs-4 fw-bold text-warning">{{ number_format($kpi['low_stock_items']) }}</div>
                        </div>
                        <i class="fas fa-exclamation-triangle fs-2 text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-4 border-info shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-muted small">Pending Transfers</div>
                            <div class="fs-4 fw-bold text-info">{{ number_format($kpi['pending_transfers']) }}</div>
                        </div>
                        <i class="fas fa-exchange-alt fs-2 text-info opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-4 border-secondary shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-muted small">Received Today</div>
                            <div class="fs-4 fw-bold text-secondary">{{ number_format($kpi['received_today']) }}</div>
                        </div>
                        <i class="fas fa-truck fs-2 text-secondary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-4 border-danger shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-muted small">Pending Requests</div>
                            <div class="fs-4 fw-bold text-danger">{{ number_format($kpi['pending_requests']) }}</div>
                        </div>
                        <i class="fas fa-clipboard-list fs-2 text-danger opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-auto mb-2">
                            <a href="{{ route('store-manager.transfers.create') }}" class="btn btn-primary">
                                <i class="fas fa-exchange-alt me-1"></i> Create Transfer
                            </a>
                        </div>
                        <div class="col-md-auto mb-2">
                            <a href="{{ route('store-manager.slips.create') }}" class="btn btn-success">
                                <i class="fas fa-arrow-down me-1"></i> New Slip
                            </a>
                        </div>
                        <div class="col-md-auto mb-2">
                            <a href="{{ route('store-manager.slips.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-file-invoice me-1"></i> Slip Records
                            </a>
                        </div>
                        <div class="col-md-auto mb-2">
                            <a href="{{ route('store-manager.products.create') }}" class="btn btn-secondary">
                                <i class="fas fa-plus me-1"></i> Add Product
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Inventory Overview -->
        <div class="col-xl-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-boxes me-2"></i>Inventory Overview - All Stores</h6>
                    <a href="{{ route('store-manager.inventory.all') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Store</th>
                                    <th class="text-end">On Hand</th>
                                    <th class="text-end">Reserved</th>
                                    <th class="text-end">Min Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allInventory as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                        <br><small class="text-muted">{{ $item->product->code ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $item->store->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-end fw-bold">{{ number_format($item->quantity_on_hand, 2) }}</td>
                                    <td class="text-end">{{ number_format($item->quantity_reserved ?? 0, 2) }}</td>
                                    <td class="text-end">{{ number_format($item->min_stock, 2) }}</td>
                                    <td>
                                        @if($item->quantity_on_hand <= $item->min_stock)
                                            <span class="badge bg-danger">Low Stock</span>
                                        @else
                                            <span class="badge bg-success">Available</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No inventory found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="col-xl-4 mb-4">
            <div class="card shadow-sm border-warning h-100">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alerts</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($lowStock as $item)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                    <br><small class="text-muted">{{ $item->store->name ?? 'N/A' }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="text-danger fw-bold">{{ number_format($item->quantity_on_hand, 2) }}</div>
                                    <small class="text-muted">Min: {{ number_format($item->min_stock, 2) }}</small>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted py-4">
                            <i class="fas fa-check-circle text-success me-2"></i>All items adequately stocked
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Transfers to General Service -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-truck-moving me-2"></i>Transfers Scheduled for General Service</h6>
                    <a href="{{ route('store-manager.transfers.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Transfer #</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transfersToGeneralService as $transfer)
                                <tr>
                                    <td>
                                        <a href="{{ route('store-manager.transfers.show', $transfer) }}">
                                            {{ $transfer->transfer_no }}
                                        </a>
                                    </td>
                                    <td>{{ $transfer->fromStore->name ?? 'N/A' }}</td>
                                    <td>{{ $transfer->toStore->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $transfer->status == 'approved' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($transfer->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $transfer->required_date->format('M d, Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No scheduled transfers</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Material Requests from Coordinator -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Material Requests from Coordinator</h6>
                    <a href="{{ route('store-manager.material-requests.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Request #</th>
                                    <th>Project</th>
                                    <th>Requested By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($materialRequests as $request)
                                <tr>
                                    <td><strong>#{{ $request->id }}</strong></td>
                                    <td>{{ $request->project->name ?? 'N/A' }}</td>
                                    <td>{{ $request->requestedBy->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $request->status == 'pending' ? 'warning' : ($request->status == 'processed' ? 'success' : 'danger') }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($request->status == 'pending')
                                        <form action="{{ route('store-manager.material-requests.process', $request) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Process this request?')">
                                                <i class="fas fa-check me-1"></i>Process
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No pending material requests</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
