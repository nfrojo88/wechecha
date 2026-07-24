@extends('layouts.app')
@section('title', 'PR: ' . $purchaseRequest->pr_no)
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-file-invoice me-2"></i>{{ $purchaseRequest->pr_no }}</h1>
        <a href="{{ route('purchase-requests.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </div>
    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">PR Details</div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><th width="45%">Project</th><td>{{ $purchaseRequest->project->name }}</td></tr>
                        <tr><th>Priority</th><td><span class="badge bg-{{ $purchaseRequest->priority === 'urgent' ? 'danger' : ($purchaseRequest->priority === 'high' ? 'warning' : 'secondary') }}">{{ ucfirst($purchaseRequest->priority) }}</span></td></tr>
                        <tr><th>Type</th><td>{{ ucfirst($purchaseRequest->type) }}</td></tr>
                        <tr><th>Status</th><td><span class="badge bg-info">{{ str_replace('_',' ',ucfirst($purchaseRequest->status)) }}</span></td></tr>
                        <tr><th>Required Date</th><td>{{ optional($purchaseRequest->required_date)->format('d M Y') ?? '-' }}</td></tr>
                        <tr><th>Requested By</th><td>{{ $purchaseRequest->requestedBy->name }}</td></tr>
                        <tr><th>Justification</th><td>{{ $purchaseRequest->justification ?? '-' }}</td></tr>
                    </table>
                </div>
                @if($purchaseRequest->status === 'draft')
                <div class="card-footer">
                    <form action="{{ route('purchase-requests.submit', $purchaseRequest) }}" method="POST">
                        @csrf <button class="btn btn-primary btn-sm w-100"><i class="fas fa-paper-plane me-1"></i>Submit for Review</button>
                    </form>
                </div>
                @elseif(in_array($purchaseRequest->status, ['submitted','under_review']))
                <div class="card-footer d-flex gap-2">
                    <form action="{{ route('purchase-requests.approve', $purchaseRequest) }}" method="POST">
                        @csrf <button class="btn btn-success btn-sm"><i class="fas fa-check me-1"></i>Approve</button>
                    </form>
                    <form action="{{ route('purchase-requests.reject', $purchaseRequest) }}" method="POST" class="d-flex gap-1">
                        @csrf
                        <input type="text" name="rejection_reason" class="form-control form-control-sm" placeholder="Reason" required>
                        <button class="btn btn-danger btn-sm"><i class="fas fa-times"></i></button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header fw-semibold">Requested Items</div>
                <div class="card-body p-0">
                    <table class="table align-middle mb-0">
                        <thead class="table-light"><tr><th>Product</th><th>Quantity</th><th>Unit</th><th>Est. Unit Cost</th><th>Est. Total</th></tr></thead>
                        <tbody>
                            @foreach($purchaseRequest->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->unit }}</td>
                                <td>{{ number_format($item->estimated_unit_cost ?? 0, 2) }}</td>
                                <td>{{ number_format($item->estimated_total ?? 0, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
