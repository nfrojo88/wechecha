@extends('layouts.app')

@section('title', 'All Inventory - Store Manager')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h4><i class="fas fa-boxes-stacked me-2"></i>All Inventory - All Stores</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Store</label>
                    <select name="store_id" class="form-select">
                        <option value="">All Stores</option>
                        @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                            {{ $store->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search Product</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name or Code">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="low_stock" value="1" class="form-check-input" {{ request('low_stock') ? 'checked' : '' }}>
                        <label class="form-check-label">Low Stock Only</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>Filter</button>
                        <a href="{{ route('store-manager.inventory.all') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="card shadow-sm">
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
                            <th class="text-end">Unit Cost</th>
                            <th class="text-end">Total Value</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventory as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                <br><small class="text-muted">{{ $item->product->code ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $item->store->name ?? 'N/A' }}</span>
                                <br><small class="text-muted">{{ $item->store->type ?? '' }}</small>
                            </td>
                            <td class="text-end fw-bold">{{ number_format($item->quantity_on_hand, 3) }}</td>
                            <td class="text-end">{{ number_format($item->quantity_reserved ?? 0, 3) }}</td>
                            <td class="text-end">{{ number_format($item->min_stock, 3) }}</td>
                            <td class="text-end">{{ number_format($item->unit_cost, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($item->quantity_on_hand * $item->unit_cost, 2) }}</td>
                            <td>
                                @if($item->quantity_on_hand <= $item->min_stock)
                                    <span class="badge bg-danger">Low Stock</span>
                                @elseif($item->quantity_on_hand <= $item->min_stock * 1.5)
                                    <span class="badge bg-warning">Near Min</span>
                                @else
                                    <span class="badge bg-success">Available</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No inventory found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $inventory->links() }}
        </div>
    </div>
</div>
@endsection
