@extends('layouts.app')

@section('title', 'Inventory Movements')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('inventory.show', $inventory) }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h1 class="h3 mb-0 me-3">Movement History</h1>
    <span class="text-muted border-start ps-3">
        {{ $inventory->product->name }} &mdash; {{ $inventory->store->name }}
    </span>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date & Time</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Performed By</th>
                        <th>Reference</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $mov)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $mov->created_at->format('d M Y') }}</div>
                            <div class="text-muted small">{{ $mov->created_at->format('H:i:s') }}</div>
                        </td>
                        <td>
                            @php
                                $badge = match($mov->type) {
                                    'in' => 'success',
                                    'out' => 'danger',
                                    'transfer' => 'info',
                                    'adjustment' => 'warning',
                                    'reserve' => 'secondary',
                                    'release' => 'secondary',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ strtoupper($mov->type) }}</span>
                        </td>
                        <td class="fw-bold fs-5 {{ in_array($mov->type, ['in','adjustment','release']) ? 'text-success' : 'text-danger' }}">
                            {{ in_array($mov->type, ['in','adjustment','release']) ? '+' : '-' }}{{ number_format($mov->quantity, 3) }}
                        </td>
                        <td>{{ $mov->performer->name }}</td>
                        <td>
                            @if($mov->reference_type && $mov->reference_id)
                                <code class="small text-muted">{{ class_basename($mov->reference_type) }} #{{ $mov->reference_id }}</code>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-wrap" style="max-width: 300px;">{{ $mov->remarks ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No movements recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($movements->hasPages())
    <div class="card-footer bg-transparent">
        {{ $movements->links() }}
    </div>
    @endif
</div>
@endsection
