@extends('layouts.app')

@section('title', 'Expense Details')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-secondary me-3"><i class="fa-solid fa-arrow-left"></i></a>
    <h1 class="h3 mb-0 me-3">Expense</h1>
    @php $badge = match($expense->status){ 'approved'=>'success','rejected'=>'danger',default=>'warning' }; @endphp
    <span class="badge bg-{{ $badge }}">{{ ucfirst($expense->status) }}</span>
    <div class="ms-auto d-flex gap-2">
        @if($expense->status === 'pending')
        @can('finance.approve')
        <form method="POST" action="{{ route('expenses.approve', $expense) }}" class="d-inline"
              onsubmit="return confirm('Approve this expense?');">
            @csrf
            <button type="submit" class="btn btn-success"><i class="fa-solid fa-check me-1"></i>Approve</button>
        </form>
        <form method="POST" action="{{ route('expenses.reject', $expense) }}" class="d-inline"
              onsubmit="return confirm('Reject this expense?');">
            @csrf
            <button type="submit" class="btn btn-outline-danger"><i class="fa-solid fa-xmark me-1"></i>Reject</button>
        </form>
        @endcan
        @endif
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr><td class="text-muted w-35">Project</td><td class="fw-semibold"><a href="{{ route('projects.show', $expense->project) }}" class="text-decoration-none">{{ $expense->project->name }}</a></td></tr>
                    <tr><td class="text-muted">Category</td><td><span class="badge bg-secondary">{{ \App\Models\Expense::CATEGORIES[$expense->category] ?? $expense->category }}</span></td></tr>
                    <tr><td class="text-muted">Date</td><td class="fw-semibold">{{ $expense->expense_date->format('d M Y') }}</td></tr>
                    <tr><td class="text-muted">Description</td><td>{{ $expense->description }}</td></tr>
                    <tr><td class="text-muted">Amount</td><td class="fs-4 fw-bold text-primary">{{ number_format($expense->amount, 2) }} ETB</td></tr>
                    @if($expense->notes)
                    <tr><td class="text-muted">Notes</td><td class="text-muted small">{{ $expense->notes }}</td></tr>
                    @endif
                    <tr class="border-top"><td class="text-muted">Recorded By</td><td class="small">{{ $expense->creator->name }} · {{ $expense->created_at->format('d M Y H:i') }}</td></tr>
                    @if($expense->approver)
                    <tr><td class="text-muted">Approved By</td><td class="small text-success">{{ $expense->approver->name }} · {{ $expense->approved_at->format('d M Y H:i') }}</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
