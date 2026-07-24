@extends('layouts.app')

@section('title', 'Material Request Details')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('material-requests.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h1 class="h3 mb-0 me-3">MR: {{ $materialRequest->reference_number }}</h1>
    
    @php
        $badge = match($materialRequest->status) {
            'draft' => 'secondary',
            'submitted' => 'info',
            'approved' => 'primary',
            'fulfilled' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    @endphp
    <span class="badge bg-{{ $badge }} me-3">{{ strtoupper($materialRequest->status) }}</span>
    
    <div class="ms-auto d-flex gap-2">
        @if($materialRequest->status === 'draft')
            <form method="POST" action="{{ route('material-requests.updateStatus', $materialRequest) }}">
                @csrf
                <input type="hidden" name="status" value="submitted">
                <button type="submit" class="btn btn-primary" onclick="return confirm('Submit this request for approval?');">
                    <i class="fa-solid fa-paper-plane me-1"></i> Submit Request
                </button>
            </form>
        @endif
        
        @if($materialRequest->status === 'submitted')
            @if(auth()->user()->can('material_requests.approve') || auth()->user()->hasAnyRole(['store_manager', 'Store Manager', 'admin', 'global_admin']))
            <form method="POST" action="{{ route('material-requests.updateStatus', $materialRequest) }}" class="d-inline">
                @csrf
                <input type="hidden" name="status" value="approved">
                <button type="submit" class="btn btn-success" onclick="return confirm('Approve this request?');">
                    <i class="fa-solid fa-check me-1"></i> Approve
                </button>
            </form>
            <form method="POST" action="{{ route('material-requests.updateStatus', $materialRequest) }}" class="d-inline">
                @csrf
                <input type="hidden" name="status" value="rejected">
                <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this request?');">
                    <i class="fa-solid fa-xmark me-1"></i> Reject
                </button>
            </form>
            @endif
        @endif
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title text-muted mb-4">Request Details</h5>
                <table class="table table-borderless table-sm mb-0">
                    <tr><td class="text-muted w-25">Source</td><td><span class="badge bg-light text-dark border fw-bold"><i class="fa-solid fa-code-branch text-primary me-1"></i>{{ $materialRequest->source ?? 'Manual Creation' }}</span></td></tr>
                    <tr><td class="text-muted">Project</td><td class="fw-semibold">{{ $materialRequest->project->name }}</td></tr>
                    <tr><td class="text-muted">Deliver To</td><td class="fw-semibold">{{ $materialRequest->store->name }} ({{ $materialRequest->store->code }})</td></tr>
                    <tr><td class="text-muted">Required By</td><td class="fw-semibold {{ $materialRequest->required_date->isPast() ? 'text-danger' : '' }}">{{ $materialRequest->required_date->format('d M Y') }}</td></tr>
                </table>
                @if($materialRequest->notes)
                <div class="mt-3 pt-3 border-top text-muted small">
                    <strong>Notes:</strong> {{ $materialRequest->notes }}
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-light h-100">
            <div class="card-body text-muted small">
                <div class="mb-3">
                    <div class="text-uppercase fw-bold mb-1">Created By</div>
                    <div class="fw-semibold text-dark">{{ $materialRequest->creator->name }}</div>
                    <div>{{ $materialRequest->created_at->format('d M Y H:i') }}</div>
                </div>
                <div>
                    <div class="text-uppercase fw-bold mb-1">Approved By</div>
                    <div class="fw-semibold text-dark">{{ $materialRequest->approver->name ?? '—' }}</div>
                    <div>{{ $materialRequest->approved_at?->format('d M Y H:i') ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Requested Materials</h5>
        @if($materialRequest->status === 'draft')
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMrItemModal">
            <i class="fa-solid fa-plus me-1"></i> Add Item
        </button>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product / Material</th>
                        <th>Category</th>
                        <th class="text-end">Qty Requested</th>
                        <th class="text-end">Qty Fulfilled</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materialRequest->items as $item)
                    <tr>
                        <td class="fw-semibold">{{ $item->product->name }} <br><code class="small text-muted">{{ $item->product->code }}</code></td>
                        <td>{{ $item->product->category }}</td>
                        <td class="text-end fw-bold">{{ number_format($item->quantity_requested, 3) }} <small class="text-muted">{{ $item->product->unit }}</small></td>
                        <td class="text-end text-success">{{ number_format($item->quantity_fulfilled, 3) }}</td>
                        <td class="small text-muted">{{ $item->notes }}</td>
                        @if($materialRequest->status === 'draft')
                        <td class="text-end">
                            <form method="POST" action="{{ route('mr-items.destroy', $item) }}"
                                  class="d-inline" onsubmit="return confirm('Remove this item?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $materialRequest->status === 'draft' ? 6 : 5 }}" class="text-center py-4 text-muted">No items added yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($materialRequest->status === 'draft')
<div class="modal fade" id="addMrItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('mr-items.store', $materialRequest) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Requested Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Product / Material <span class="text-danger">*</span></label>
                            <select name="product_id" class="form-select" required>
                                <option value="">— Select Product —</option>
                                @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code }}) – {{ $p->unit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Quantity Required <span class="text-danger">*</span></label>
                            <input type="number" step="0.001" min="0.001" name="quantity_requested"
                                   class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes (optional)</label>
                            <input type="text" name="notes" class="form-control" placeholder="Specification or remarks">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
