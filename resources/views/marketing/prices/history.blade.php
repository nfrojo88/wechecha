@extends('layouts.app')

@section('title', 'Price History & Trends')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Market Price History &amp; Log
            </h1>
            <p class="text-muted small mb-0">Historical log of all recorded market price updates across materials.</p>
        </div>
        <div>
            @if(auth()->check() && (auth()->user()->hasRole('marketing') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('global_admin')))
            <a href="{{ route('marketing.prices.create') }}" class="btn btn-primary btn-sm fw-semibold">
                <i class="fa-solid fa-plus-circle me-1"></i>New Price Update
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('marketing.prices.history') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold">Filter by Material</label>
                    <select name="product_id" class="form-select form-select-sm">
                        <option value="">— All Materials —</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" @selected(request('product_id') == $p->id)>{{ $p->name }} ({{ $p->unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Filter by Category</label>
                    <select name="category" class="form-select form-select-sm">
                        <option value="">— All Categories —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" @selected(request('category') == $cat)>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fa-solid fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('marketing.prices.history') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Log Table --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small">
                        <tr>
                            <th>Effective Date</th>
                            <th>Material Name</th>
                            <th>Category</th>
                            <th>UM</th>
                            <th class="text-end">Recorded Price</th>
                            <th>Source</th>
                            <th>Notes</th>
                            <th>Updated By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prices as $price)
                        <tr>
                            <td class="fw-bold text-dark">{{ $price->effective_date->format('Y-m-d') }}</td>
                            <td class="fw-semibold text-primary">{{ $price->product->name ?? 'N/A' }}</td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $price->product->category ?? 'N/A' }}</span></td>
                            <td><code>{{ $price->product->unit ?? '-' }}</code></td>
                            <td class="text-end fw-bold fs-6 text-dark">ETB {{ number_format($price->price, 2) }}</td>
                            <td><span class="badge bg-info bg-opacity-10 text-info text-capitalize">{{ $price->source }}</span></td>
                            <td class="small text-muted" style="max-width:200px;">{{ $price->notes ?: '—' }}</td>
                            <td class="small text-muted">{{ $price->creator->name ?? 'System' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No price history entries found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($prices->hasPages())
            <div class="p-3 border-top">
                {{ $prices->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
