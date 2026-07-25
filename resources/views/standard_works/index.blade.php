@extends('layouts.app')

@section('title', 'Standard Work')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-1">
            <i class="fa-solid fa-sliders me-2 text-primary"></i>Standard Work
        </h1>
        <p class="text-muted small mb-0">Define standard resource conversion ratios for each type of work.</p>
    </div>
    <a href="{{ route('standard-works.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> New Standard Work
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>WORK NAME</th>
                        <th>CATEGORY</th>
                        <th>UNIT</th>
                        <th class="text-center">MATERIALS</th>
                        <th class="text-center">MANPOWER</th>
                        <th class="text-center">EQUIPMENT</th>
                        <th class="text-end">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($works as $work)
                    <tr>
                        <td>
                            <a href="{{ route('standard-works.show', $work) }}" class="fw-semibold text-decoration-none text-dark">
                                {{ $work->name }}
                            </a>
                            @if($work->description)
                                <div class="small text-muted text-truncate" style="max-width:240px;">{{ $work->description }}</div>
                            @endif
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $work->category }}</span></td>
                        <td><code>{{ $work->unit }}</code></td>
                        <td class="text-center">
                            <span class="badge bg-success bg-opacity-10 text-success">
                                <i class="fa-solid fa-cubes me-1"></i>{{ $work->materials->count() }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                <i class="fa-solid fa-person-digging me-1"></i>{{ $work->manpower->count() }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-warning bg-opacity-10 text-warning">
                                <i class="fa-solid fa-tractor me-1"></i>{{ $work->equipment->count() }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('standard-works.show', $work) }}" class="btn btn-sm btn-outline-secondary" title="View Details">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('standard-works.edit', $work) }}" class="btn btn-sm btn-outline-primary ms-1" title="Edit Standard Work">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('standard-works.destroy', $work) }}" method="POST" class="d-inline ms-1"
                                  onsubmit="return confirm('Delete this Standard Work?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-sliders fa-3x mb-3 d-block opacity-25"></i>
                            No standard works defined yet.
                            <div class="mt-2">
                                <a href="{{ route('standard-works.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-plus me-1"></i> Create First Standard
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($works->hasPages())
    <div class="card-footer bg-white">{{ $works->links() }}</div>
    @endif
</div>
@endsection
