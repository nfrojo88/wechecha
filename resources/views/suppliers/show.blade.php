@extends('layouts.app')
@section('title', 'Supplier: ' . $supplier->name)
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-truck me-2"></i>{{ $supplier->name }}</h1>
        <div>
            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i>Edit</a>
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary ms-2"><i class="fas fa-arrow-left me-1"></i>Back</a>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-5">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-semibold"><i class="fas fa-info-circle me-2"></i>Supplier Details</div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr><th width="40%">Code</th><td>{{ $supplier->code }}</td></tr>
                        <tr><th>Status</th><td><span class="badge bg-{{ $supplier->status === 'active' ? 'success' : ($supplier->status === 'blacklisted' ? 'danger' : 'warning') }}">{{ ucfirst($supplier->status) }}</span></td></tr>
                        <tr><th>Contact</th><td>{{ $supplier->contact_person ?? '-' }}</td></tr>
                        <tr><th>Phone</th><td>{{ $supplier->phone ?? '-' }}</td></tr>
                        <tr><th>Email</th><td>{{ $supplier->email ?? '-' }}</td></tr>
                        <tr><th>Tax ID</th><td>{{ $supplier->tax_id ?? '-' }}</td></tr>
                        <tr><th>Rating</th><td>
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $supplier->rating ? 'text-warning' : 'text-muted' }}"></i>
                            @endfor
                        </td></tr>
                        <tr><th>Address</th><td>{{ $supplier->address ?? '-' }}</td></tr>
                        <tr><th>Notes</th><td>{{ $supplier->notes ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold"><i class="fas fa-shopping-cart me-2"></i>Recent Purchase Orders</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>PO No</th><th>Date</th><th>Total</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($supplier->purchaseOrders->take(10) as $po)
                            <tr>
                                <td>{{ $po->po_no }}</td>
                                <td>{{ optional($po->po_date)->format('d M Y') }}</td>
                                <td>{{ number_format($po->grand_total, 2) }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($po->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No purchase orders yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
