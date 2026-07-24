@extends('layouts.app')

@section('title', 'Expenses')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Project Expenses</h1>
    @can('finance.view')
    <a href="{{ route('expenses.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Record Expense
    </a>
    @endcan
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent py-3">
        <form method="GET" action="{{ route('expenses.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <select name="project_id" class="form-select form-select-sm">
                    <option value="">All Projects</option>
                    @foreach($projects as $p)
                    <option value="{{ $p->id }}" @selected(request('project_id')==$p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $key => $label)
                    <option value="{{ $key }}" @selected(request('category')==$key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
                @if(request('project_id') || request('category'))
                <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-danger">Clear</a>
                @endif
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Project</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th class="text-end">Amount (ETB)</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                    <tr>
                        <td class="small text-muted">{{ $exp->expense_date->format('d M Y') }}</td>
                        <td><a href="{{ route('projects.show', $exp->project) }}" class="text-decoration-none small">{{ $exp->project->name }}</a></td>
                        <td><span class="badge bg-secondary bg-opacity-75">{{ $categories[$exp->category] ?? $exp->category }}</span></td>
                        <td>{{ Str::limit($exp->description, 50) }}</td>
                        <td class="text-end fw-bold">{{ number_format($exp->amount, 2) }}</td>
                        <td>
                            @php $badge = match($exp->status){ 'approved'=>'success','rejected'=>'danger',default=>'warning' }; @endphp
                            <span class="badge bg-{{ $badge }}">{{ ucfirst($exp->status) }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('expenses.show', $exp) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-receipt fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0">No expenses recorded yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($expenses->hasPages())
    <div class="card-footer bg-transparent">{{ $expenses->links() }}</div>
    @endif
</div>
@endsection
