@extends('layouts.app')
@section('title', 'Rebar Product Mapping')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 mb-0 fw-bold">
                <i class="fa-solid fa-link text-danger me-2"></i>Rebar Diameter → Product Mapping
            </h1>
            <p class="text-muted small mb-0 mt-1">
                Link each rebar diameter to a material product in your inventory so takeoff sheets can auto-generate material requirements.
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <form action="{{ route('rebar-products.seed') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm shadow-sm"
                        onclick="return confirm('This will auto-create all standard rebar products and link them. Continue?')">
                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Auto-Create & Link Defaults
                </button>
            </form>
            <a href="{{ route('takeoff.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm py-2" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center gap-2 py-2">
            <i class="fa-solid fa-table-list text-danger"></i>
            <span class="fw-bold">Diameter Mappings</span>
            <span class="badge bg-secondary ms-auto">{{ count($diameters) }} diameters</span>
        </div>
        <div class="card-body p-0">
            <form action="{{ route('rebar-products.update') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" style="font-size:13.5px;">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" style="width:90px;">DIAMETER</th>
                                <th class="text-center" style="width:120px;">UNIT WEIGHT (kg/m)</th>
                                <th>LINKED PRODUCT</th>
                                <th class="text-center" style="width:160px;">STOCK LENGTH (m)</th>
                                <th class="text-center" style="width:90px;">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($diameters as $dia => $kgPerM)
                                @php $mapping = $mappings[$dia] ?? null; @endphp
                                <tr>
                                    <td class="text-center fw-bold">
                                        <span class="badge bg-danger fs-6">Ø {{ $dia }}</span>
                                    </td>
                                    <td class="text-center text-muted">{{ number_format($kgPerM, 3) }}</td>
                                    <td>
                                        <select name="mappings[{{ $dia }}][product_id]" class="form-select form-select-sm">
                                            <option value="">— Not linked —</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                    {{ ($mapping && $mapping->product_id == $product->id) ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                    @if($product->sku) ({{ $product->sku }}) @endif
                                                    — {{ $product->unit }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" step="0.5" min="1" max="20"
                                               name="mappings[{{ $dia }}][standard_length_m]"
                                               value="{{ $mapping->standard_length_m ?? '12' }}"
                                               class="form-control form-control-sm text-center"
                                               style="width:90px; margin:auto;"
                                               placeholder="12">
                                    </td>
                                    <td class="text-center">
                                        @if($mapping && $mapping->product_id)
                                            <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Linked</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="fa-solid fa-triangle-exclamation me-1"></i>Unlinked</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top d-flex justify-content-end py-3">
                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Mappings
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Info Panel --}}
    <div class="card shadow-sm mt-4 border-start border-info border-3">
        <div class="card-body py-3">
            <h6 class="fw-bold text-info mb-2"><i class="fa-solid fa-circle-info me-2"></i>How It Works</h6>
            <ol class="mb-0 small text-muted">
                <li>Click <strong>"Auto-Create &amp; Link Defaults"</strong> to automatically generate rebar products in your product catalog (e.g. <em>Rebar Ø8mm, Rebar Ø10mm…</em>) and link them here.</li>
                <li>Or manually select any existing product from your catalog for each diameter.</li>
                <li>Once linked, when you <strong>Convert</strong> a rebar takeoff to a Material Plan, the system automatically maps the calculated weight (kg) per diameter to the correct product.</li>
                <li>You can still adjust prices for each product in the <strong>Products</strong> section.</li>
            </ol>
        </div>
    </div>

</div>
@endsection
