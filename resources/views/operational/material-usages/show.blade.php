@extends('layouts.app')
@section('title', 'Material Usage Details')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Material Usage: {{ $materialUsage->usage_no }}</h1>
        <div>
            <a href="{{ route('material-usages.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
            </a>
            @if($materialUsage->status === 'draft')
            <form action="{{ route('material-usages.confirm', $materialUsage->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-success shadow-sm" onclick="return confirm('Confirm usage? This will deduct from inventory and cannot be undone.')">
                    <i class="fas fa-check fa-sm text-white-50"></i> Confirm Usage
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
                                @if($materialUsage->status == 'draft') <span class="badge badge-secondary">Draft</span>
                                @elseif($materialUsage->status == 'confirmed') <span class="badge badge-success">Confirmed</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Usage Date:</th>
                            <td>{{ $materialUsage->usage_date->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Project:</th>
                            <td>{{ $materialUsage->project->name }}</td>
                        </tr>
                        <tr>
                            <th>Store:</th>
                            <td>{{ $materialUsage->store->name }}</td>
                        </tr>
                        <tr>
                            <th>Logged By:</th>
                            <td>{{ $materialUsage->createdBy->name }}</td>
                        </tr>
                    </table>
                    <hr>
                    <strong>Description:</strong>
                    <p class="text-muted">{{ $materialUsage->description ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Consumed Materials</h6>
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
                            @forelse($materialUsage->items as $index => $item)
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
