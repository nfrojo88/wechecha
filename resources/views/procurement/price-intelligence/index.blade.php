@extends('layouts.app')
@section('title', 'Price Intelligence')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-chart-line text-success me-2"></i>Price Intelligence & Market Trends</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Market Price Comparisons</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Material / Product</th>
                            <th>Current Avg. Price (ETB)</th>
                            <th>Price Trend</th>
                            <th>Available Suppliers</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($priceData as $data)
                        <tr>
                            <td class="fw-bold">{{ $data['material'] }}</td>
                            <td>{{ number_format($data['current_price'], 2) }}</td>
                            <td>
                                @if($data['trend'] == 'up')
                                    <span class="text-danger"><i class="fas fa-arrow-up"></i> Trending Up</span>
                                @elseif($data['trend'] == 'down')
                                    <span class="text-success"><i class="fas fa-arrow-down"></i> Trending Down</span>
                                @else
                                    <span class="text-secondary"><i class="fas fa-minus"></i> Stable</span>
                                @endif
                            </td>
                            <td><span class="badge bg-info text-dark">{{ $data['suppliers'] }} Suppliers</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"><i class="fas fa-search me-1"></i>View Vendors</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">No price intelligence data available.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle me-2"></i> This data is aggregated from recent Purchase Orders and Supplier Quotations.
            </div>
        </div>
    </div>
</div>
@endsection
