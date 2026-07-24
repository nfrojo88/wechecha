@extends('layouts.app')
@section('title', 'Waste Details')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Waste Record Details</h1>
        <div>
            <a href="{{ route('waste.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
            </a>
            @if($waste->status === 'reported')
            <form action="{{ route('waste.verify', $waste->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-success shadow-sm" onclick="return confirm('Verify waste? This will deduct the wasted items from inventory.')">
                    <i class="fas fa-check fa-sm text-white-50"></i> Verify & Deduct
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Overview</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($waste->status == 'reported') <span class="badge badge-warning">Reported</span>
                                @elseif($waste->status == 'verified') <span class="badge badge-success">Verified</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Waste Date:</th>
                            <td>{{ $waste->waste_date->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Reason:</th>
                            <td><span class="badge badge-danger">{{ ucwords(str_replace('_', ' ', $waste->reason)) }}</span></td>
                        </tr>
                        <tr>
                            <th>Project:</th>
                            <td>{{ $waste->project->name }}</td>
                        </tr>
                        <tr>
                            <th>Store:</th>
                            <td>{{ $waste->store->name }}</td>
                        </tr>
                        <tr>
                            <th>Logged By:</th>
                            <td>{{ $waste->recordedBy->name }}</td>
                        </tr>
                    </table>
                    <hr>
                    <strong>Description/Notes:</strong>
                    <p class="text-muted">{{ $waste->description ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Wasted Materials</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product / Material</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($waste->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ number_format($item->quantity, 2) }}</td>
                                <td>{{ $item->unit }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No items logged.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
