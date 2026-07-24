@extends('layouts.app')

@section('title', 'Client Payments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Client Payments Received</h1>
    @can('finance.manage')
    <a href="{{ route('payments.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Record Payment
    </a>
    @endcan
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent py-3">
        <form method="GET" action="{{ route('payments.index') }}" class="d-flex gap-2">
            <select name="project_id" class="form-select form-select-sm" style="max-width:280px;">
                <option value="">All Projects</option>
                @foreach($projects as $p)
                <option value="{{ $p->id }}" @selected(request('project_id')==$p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
            @if(request('project_id'))
            <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-danger">Clear</a>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Reference</th>
                        <th>Project</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th class="text-end">Amount (ETB)</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $pay)
                    <tr>
                        <td class="font-monospace small fw-semibold">{{ $pay->reference_number }}</td>
                        <td><a href="{{ route('projects.show', $pay->project) }}" class="text-decoration-none small">{{ $pay->project->name }}</a></td>
                        <td><span class="badge bg-info bg-opacity-75">{{ \App\Models\Payment::TYPES[$pay->payment_type] ?? $pay->payment_type }}</span></td>
                        <td class="small text-muted">{{ $pay->payment_date->format('d M Y') }}</td>
                        <td class="small">{{ Str::limit($pay->description ?? '—', 45) }}</td>
                        <td class="text-end fw-bold text-success">{{ number_format($pay->amount, 2) }}</td>
                        <td class="text-end">
                            <a href="{{ route('payments.show', $pay) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-coins fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0">No client payments recorded yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($payments->count())
                <tfoot class="table-light">
                    <tr>
                        <th colspan="5" class="text-end text-uppercase">Total Received:</th>
                        <th class="text-end text-success">{{ number_format($payments->sum('amount'), 2) }} ETB</th>
                        <th></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @if($payments->hasPages())
    <div class="card-footer bg-transparent">{{ $payments->links() }}</div>
    @endif
</div>
@endsection
