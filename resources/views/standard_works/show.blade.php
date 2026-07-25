@extends('layouts.app')

@section('title', 'Standard Work – ' . $standardWork->name)

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('standard-works.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="me-auto">
        <h1 class="page-title mb-1">
            <i class="fa-solid fa-sliders me-2 text-primary"></i>{{ $standardWork->name }}
        </h1>
        <p class="text-muted small mb-0">
            <i class="fa-solid fa-ruler me-1"></i>Unit: <code>{{ $standardWork->unit }}</code>
            &nbsp;·&nbsp;
            <span class="text-success"><i class="fa-solid fa-cubes me-1"></i>{{ $standardWork->materials->count() }} materials</span>
            &nbsp;·&nbsp;
            <span class="text-primary"><i class="fa-solid fa-person-digging me-1"></i>{{ $standardWork->manpower->count() }} manpower</span>
            &nbsp;·&nbsp;
            <span style="color:#6d28d9;"><i class="fa-solid fa-flask-vial me-1"></i>{{ $standardWork->scientificManpower->count() }} scientific</span>
            &nbsp;·&nbsp;
            <span class="text-warning"><i class="fa-solid fa-tractor me-1"></i>{{ $standardWork->equipment->count() }} equipment</span>
        </p>
    </div>
    <form action="{{ route('standard-works.destroy', $standardWork) }}" method="POST"
          onsubmit="return confirm('Delete this standard work permanently?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger btn-sm">
            <i class="fa-solid fa-trash me-1"></i>Delete
        </button>
    </form>
</div>


@if($standardWork->description)
<div class="alert alert-light border mb-4">
    <i class="fa-solid fa-circle-info me-2 text-primary"></i>
    <strong>Description:</strong> {{ $standardWork->description }}
</div>
@endif

{{-- ── Materials ── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent py-3">
        <h5 class="mb-0"><i class="fa-solid fa-cubes me-2 text-success"></i>Materials per 1 {{ $standardWork->unit }}</h5>
    </div>
    <div class="card-body p-0">
        @if($standardWork->materials->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Material Name</th>
                        <th class="text-end">Quantity</th>
                        <th>Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($standardWork->materials as $i => $mat)
                    <tr>
                        <td class="text-muted small">{{ $i + 1 }}</td>
                        <td class="fw-semibold">{{ $mat->material_name }}</td>
                        <td class="text-end">{{ number_format($mat->quantity, 3) }}</td>
                        <td><code>{{ $mat->unit }}</code></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center text-muted py-4">
            <i class="fa-solid fa-cubes fa-2x mb-2 d-block opacity-25"></i>No materials defined.
        </div>
        @endif
    </div>
</div>

{{-- ── Manpower & Productivity ── --}}
<div class="card border-0 shadow-sm mb-4" style="border-left:4px solid #3b82f6 !important;">
    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary">
            <i class="fa-solid fa-users-gear me-2"></i>Manpower &amp; Productivity
        </h5>
    </div>

    @if($standardWork->min_productivity || $standardWork->max_productivity || $standardWork->default_productivity)
    <div class="card-body border-bottom bg-light bg-opacity-50 py-3">
        <div class="row g-3 text-center text-md-start">
            <div class="col-md-4">
                <span class="text-muted small d-block">Min Output Rate</span>
                <span class="fw-bold fs-6">
                    {{ $standardWork->min_productivity !== null ? number_format($standardWork->min_productivity, 3) : '—' }}
                    <small class="text-muted fw-normal">{{ $standardWork->unit }}/day</small>
                </span>
            </div>
            <div class="col-md-4">
                <span class="text-muted small d-block">Max Output Rate</span>
                <span class="fw-bold fs-6">
                    {{ $standardWork->max_productivity !== null ? number_format($standardWork->max_productivity, 3) : '—' }}
                    <small class="text-muted fw-normal">{{ $standardWork->unit }}/day</small>
                </span>
            </div>
            <div class="col-md-4">
                <span class="text-muted small d-block text-primary">Default Output per Day</span>
                <span class="fw-bold fs-6 text-primary">
                    {{ $standardWork->default_productivity !== null ? number_format($standardWork->default_productivity, 3) : '—' }}
                    <small class="text-primary opacity-75 fw-normal">{{ $standardWork->unit }}/day</small>
                </span>
            </div>
        </div>
    </div>
    @endif

    <div class="card-body p-0">
        <ul class="nav nav-tabs border-bottom px-3 pt-2" id="showManpowerTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="show-labour-tab" data-bs-toggle="tab" data-bs-target="#show-labour-panel" type="button" role="tab">
                    <i class="fa-solid fa-person-digging me-1 text-primary"></i>Labour ({{ $standardWork->manpower->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="show-scientific-tab" data-bs-toggle="tab" data-bs-target="#show-scientific-panel" type="button" role="tab">
                    <i class="fa-solid fa-flask-vial me-1" style="color:#8b5cf6;"></i>
                    <span style="color:#6d28d9;">Scientific ({{ $standardWork->scientificManpower->count() }})</span>
                </button>
            </li>
        </ul>

        <div class="tab-content">
            {{-- Labour Tab --}}
            <div class="tab-pane fade show active" id="show-labour-panel" role="tabpanel">
                @if($standardWork->manpower->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Role / Trade</th>
                                <th class="text-end">Quantity (per 1 {{ $standardWork->unit }})</th>
                                <th>Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($standardWork->manpower as $i => $mp)
                            <tr>
                                <td class="text-muted small">{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $mp->role }}</td>
                                <td class="text-end">{{ number_format($mp->quantity, 3) }}</td>
                                <td><code>{{ $mp->unit }}</code></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="fa-solid fa-person-digging fa-2x mb-2 d-block opacity-25"></i>No manpower roles defined.
                </div>
                @endif
            </div>

            {{-- Scientific Tab --}}
            <div class="tab-pane fade" id="show-scientific-panel" role="tabpanel">
                @if($standardWork->scientificManpower->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Role / Specialisation</th>
                                <th class="text-end">Quantity (per 1 {{ $standardWork->unit }})</th>
                                <th>Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($standardWork->scientificManpower as $i => $smp)
                            <tr>
                                <td class="text-muted small">{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $smp->role }}</td>
                                <td class="text-end">{{ number_format($smp->quantity, 3) }}</td>
                                <td><code>{{ $smp->unit }}</code></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="fa-solid fa-flask-vial fa-2x mb-2 d-block opacity-25" style="color:#8b5cf6;"></i>No scientific manpower defined.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── Equipment ── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent py-3">
        <h5 class="mb-0"><i class="fa-solid fa-tractor me-2 text-warning"></i>Equipment per 1 {{ $standardWork->unit }}</h5>
    </div>
    <div class="card-body p-0">
        @if($standardWork->equipment->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Equipment Name</th>
                        <th class="text-end">Quantity</th>
                        <th>Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($standardWork->equipment as $i => $eq)
                    <tr>
                        <td class="text-muted small">{{ $i + 1 }}</td>
                        <td class="fw-semibold">{{ $eq->equipment_name }}</td>
                        <td class="text-end">{{ number_format($eq->quantity, 3) }}</td>
                        <td><code>{{ $eq->unit }}</code></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center text-muted py-4">
            <i class="fa-solid fa-tractor fa-2x mb-2 d-block opacity-25"></i>No equipment defined.
        </div>
        @endif
    </div>
</div>

{{-- ── Footer meta ── --}}
<div class="text-end text-muted small mb-4">
    Created by <strong>{{ $standardWork->creator->name ?? 'Unknown' }}</strong>
    on {{ $standardWork->created_at->format('d M Y, H:i') }}
</div>
@endsection
