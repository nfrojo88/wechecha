@extends('layouts.app')

@section('title', 'Bill of Quantities')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Bill of Quantities (BOQ)</h1>
    @can('boq.create')
    <a href="{{ route('boqs.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Create BOQ
    </a>
    @endcan
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
        <form method="GET" action="{{ route('boqs.index') }}" class="d-flex gap-2">
            <select name="project_id" class="form-select form-select-sm" style="min-width: 250px;">
                <option value="">All Projects</option>
                @foreach($projects as $project)
                <option value="{{ $project->id }}" @selected(request('project_id') == $project->id)>
                    {{ $project->name }} ({{ $project->code }})
                </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
            @if(request('project_id'))
            <a href="{{ route('boqs.index') }}" class="btn btn-sm btn-outline-danger">Clear</a>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Reference</th>
                        <th>Title</th>
                        <th>Project</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boqs as $boq)
                    <tr>
                        <td class="fw-semibold">{{ $boq->reference_number }}</td>
                        <td>{{ $boq->title }}</td>
                        <td>
                            @if($boq->project)
                                <a href="{{ route('projects.show', $boq->project) }}" class="text-decoration-none">
                                    {{ $boq->project->name }}
                                </a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td class="fw-bold">{{ number_format($boq->total_amount, 2) }} ETB</td>
                        <td>
                            @php
                                $badge = match($boq->status) {
                                    'draft' => 'secondary',
                                    'approved' => 'success',
                                    'revised' => 'warning',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ strtoupper($boq->status) }}</span>
                        </td>
                        <td class="small text-muted">{{ $boq->creator->name }}</td>
                        <td class="text-end">
                            <a href="{{ route('boqs.show', $boq) }}" class="btn btn-sm btn-outline-primary">View / Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-file-invoice-dollar fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0">No BOQs found. Start planning by creating one!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($boqs->hasPages())
    <div class="card-footer bg-transparent">
        {{ $boqs->links() }}
    </div>
    @endif
</div>
@endsection
