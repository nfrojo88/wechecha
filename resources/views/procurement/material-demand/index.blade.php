@extends('layouts.app')
@section('title', 'Material Demand & Forecast')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-cubes text-info me-2"></i>Material Demand & Forecast</h1>
        <button class="btn btn-outline-primary btn-sm"><i class="fas fa-file-export me-1"></i>Export Forecast</button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Upcoming Material Requirements (Next 30 Days)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Project</th>
                            <th>Material / Product</th>
                            <th>Required Quantity</th>
                            <th>Date Needed</th>
                            <th>Status / Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($forecasts as $forecast)
                        <tr>
                            <td class="fw-bold">{{ $forecast['project'] }}</td>
                            <td>{{ $forecast['material'] }}</td>
                            <td><span class="badge bg-secondary">{{ $forecast['required_qty'] }}</span></td>
                            <td>
                                @php
                                    $days = \Carbon\Carbon::parse($forecast['date_needed'])->diffInDays(now());
                                    $dateStr = \Carbon\Carbon::parse($forecast['date_needed'])->format('d M, Y');
                                @endphp
                                {{ $dateStr }} 
                                <small class="text-danger d-block">In {{ $days }} days</small>
                            </td>
                            <td>
                                <a href="{{ route('purchase-requests.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-shopping-cart me-1"></i>Create PR</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">No upcoming material demand forecasted.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="alert alert-warning mt-3">
                <i class="fas fa-lightbulb me-2"></i> These forecasts are generated based on active Project Schedules and BoQ requirements.
            </div>
        </div>
    </div>
</div>
@endsection
