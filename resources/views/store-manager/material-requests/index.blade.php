@extends('layouts.app')

@section('title', 'Material Requests - Store Manager')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h4><i class="fas fa-clipboard-list me-2"></i>Material Requests from Coordinator</h4>
            <p class="text-muted">Review material requests and process them - create transfer if available or send to Purchase Manager</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Processed</option>
                        <option value="needs_purchase" {{ request('status') == 'needs_purchase' ? 'selected' : '' }}>Sent to Purchase</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>Filter</button>
                        <a href="{{ route('store-manager.material-requests.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Material Requests Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Request #</th>
                            <th>Project</th>
                            <th>Requested By</th>
                            <th>Items</th>
                            <th>Required Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                        <tr>
                            <td><strong>#{{ $request->id }}</strong></td>
                            <td>{{ $request->project->name ?? 'N/A' }}</td>
                            <td>{{ $request->requestedBy->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $request->items->count() }} items</span>
                            </td>
                            <td>{{ $request->required_date ? $request->required_date->format('M d, Y') : '-' }}</td>
                            <td>
                                @switch($request->status)
                                    @case('pending')
                                        <span class="badge bg-warning">Pending</span>
                                        @break
                                    @case('processed')
                                        <span class="badge bg-success">Processed</span>
                                        @break
                                    @case('needs_purchase')
                                        <span class="badge bg-info">Sent to Purchase</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ ucfirst($request->status) }}</span>
                                @endswitch
                            </td>
                            <td>
                                @if($request->status == 'pending')
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modal-{{ $request->id }}">
                                    <i class="fas fa-eye me-1"></i>View Items
                                </button>
                                <form action="{{ route('store-manager.material-requests.process', $request) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Process this request? Will check availability and create transfer or send to Purchase Manager.')">
                                        <i class="fas fa-check me-1"></i>Process
                                    </button>
                                </form>
                                @else
                                <span class="text-muted">No action needed</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No material requests found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $requests->links() }}
        </div>
    </div>
</div>

<!-- Modals for each request -->
@foreach($requests as $request)
<div class="modal fade" id="modal-{{ $request->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Material Request #{{ $request->id }} Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th class="text-end">Quantity</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($request->items as $item)
                        <tr>
                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                            <td class="text-end">{{ number_format($item->quantity, 3) }}</td>
                            <td>{{ $item->unit ?? 'pcs' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
