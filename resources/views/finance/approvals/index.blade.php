@extends('layouts.app')

@section('title', 'Expense Track & Approve')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i>Expense Track & Approve</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent py-3">
        <form method="GET" action="{{ route('approvals.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Project</label>
                <select name="project" class="form-select form-select-sm">
                    <option value="all">All Projects</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->name }}" @selected(request('project') == $p->name)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Category</label>
                <select name="category" class="form-select form-select-sm">
                    <option value="all">All Categories</option>
                    <!-- Expand with specific categories if needed -->
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="all">All Statuses</option>
                    <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                    <option value="approved" @selected(request('status') == 'approved')>Approved</option>
                    <option value="rejected" @selected(request('status') == 'rejected')>Rejected</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Date Range</label>
                <div class="d-flex gap-2">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-sm btn-dark"><i class="fa-solid fa-filter me-1"></i>Filter</button>
                @if(request()->hasAny(['project', 'category', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('approvals.index') }}" class="btn btn-sm btn-outline-danger">Clear</a>
                @endif
            </div>
        </form>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Project</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th class="text-end">Base Amount</th>
                        <th class="text-end">VAT / Withhold</th>
                        <th class="text-end">Net Amount</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paginatedItems as $item)
                        <tr>
                            <td>
                                <span class="fw-semibold text-secondary">{{ $item->id_formatted }}</span>
                            </td>
                            <td>
                                @if($item->date instanceof \Carbon\Carbon)
                                    {{ $item->date->format('d M Y') }}
                                @else
                                    {{ date('d M Y', strtotime($item->date)) }}
                                @endif
                            </td>
                            <td>{{ Str::limit($item->project, 20) }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $item->category }}</span>
                            </td>
                            <td class="text-truncate" style="max-width: 250px;" title="{{ $item->description }}">
                                {{ $item->description }}
                            </td>
                            <td class="text-end text-muted">
                                ETB {{ number_format($item->base_amount, 2) }}
                            </td>
                            <td class="text-end text-muted">
                                &mdash;
                            </td>
                            <td class="text-end fw-bold text-primary">
                                ETB {{ number_format($item->net_amount, 2) }}
                            </td>
                            <td>
                                @php
                                    $badge = match(strtolower($item->status)){ 'approved'=>'success', 'rejected'=>'danger', 'pending'=>'warning', 'draft'=>'secondary', default=>'info' };
                                @endphp
                                <span class="badge bg-{{ $badge }} text-uppercase">{{ $item->status }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ $item->route_show }}" class="btn btn-outline-info" title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if(strtolower($item->status) === 'pending' || strtolower($item->status) === 'draft')
                                        <form method="POST" action="{{ $item->route_approve }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-{{ $item->color }}" title="Process / Approve">
                                                <i class="fa-solid fa-bolt"></i> Process
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-secondary disabled"><i class="fa-solid fa-check"></i> Processed</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-inbox fa-3x mb-3 opacity-50"></i>
                                <p class="mb-0">No pending approvals found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($paginatedItems->hasPages())
        <div class="card-footer bg-transparent">
            {{ $paginatedItems->links() }}
        </div>
    @endif
</div>
@endsection
