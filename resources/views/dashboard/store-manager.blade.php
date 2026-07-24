@extends('layouts.app')
@section('title', 'Store Manager Dashboard')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-warehouse me-2"></i> Store Manager Dashboard</h1>
        <span class="badge badge-secondary p-2">{{ now()->format('l, F j Y') }}</span>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Items in Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kpi['total_items'] }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-boxes-stacked fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Low Stock Alerts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kpi['low_stock'] }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-triangle-exclamation fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Transfers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kpi['pending_transfers'] }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-exchange-alt fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Receipts This Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kpi['recent_receipts'] }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-truck-ramp-box fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($kpi['low_stock'] > 0)
    <div class="alert alert-danger shadow-sm">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <strong>{{ $kpi['low_stock'] }} item(s)</strong> are at or below minimum stock level. Please review inventory immediately.
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-triangle-exclamation mr-2"></i> Low Stock Items</h6>
                    <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-outline-danger">View All Inventory</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light"><tr><th>Product</th><th>In Stock</th><th>Min Level</th><th>Action</th></tr></thead>
                        <tbody>
                            @forelse($lowStockItems as $item)
                            <tr>
                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                <td><span class="font-weight-bold text-danger">{{ $item->quantity }}</span></td>
                                <td>{{ $item->min_level ?? 'N/A' }}</td>
                                <td><a href="{{ route('inventory.show', $item) }}" class="btn btn-xs btn-warning">Adjust</a></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-success"><i class="fas fa-check mr-2"></i> All stock levels are healthy!</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('delivery-receipts.create') }}" class="btn btn-primary btn-block mb-2"><i class="fas fa-plus mr-2"></i> New Delivery Receipt</a>
                    <a href="{{ route('transfers.create') }}" class="btn btn-warning btn-block mb-2"><i class="fas fa-exchange-alt mr-2"></i> New Transfer</a>
                    <a href="{{ route('inventory.index') }}" class="btn btn-info btn-block mb-2"><i class="fas fa-clipboard-list mr-2"></i> View Stock</a>
                    <a href="{{ route('material-requests.index') }}" class="btn btn-secondary btn-block"><i class="fas fa-cart-flatbed mr-2"></i> Material Requests</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
