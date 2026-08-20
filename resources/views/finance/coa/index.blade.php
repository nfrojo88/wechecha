@extends('layouts.app')
@section('title', 'Chart of Accounts')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-sitemap me-2 text-primary"></i>Chart of Accounts</h1>
            <p class="text-muted small mb-0">General ledger accounts, balances, and real-time fund transfers</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('coa-transfers.index') }}" class="btn btn-outline-info btn-sm">
                <i class="fa-solid fa-list-check me-1"></i>Transfer History
            </a>
            <a href="{{ route('coa-transfers.create') }}" class="btn btn-success btn-sm fw-bold shadow-sm">
                <i class="fa-solid fa-money-bill-transfer me-1"></i>Transfer Funds
            </a>
            <a href="{{ route('coa.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Add Account</a>
        </div>
    </div>
    @if(session('success'))<div class="alert alert-success alert-dismissible fade show shadow-sm">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @php $typeColors = ['asset'=>'primary','liability'=>'danger','equity'=>'success','revenue'=>'info','expense'=>'warning']; @endphp
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark small text-uppercase">
                        <tr><th>Code</th><th>Name</th><th>Parent</th><th>Manager</th><th>Type</th><th class="text-end">Balance (ETB)</th><th>Active</th><th class="text-center">Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $a)
                        <tr>
                            <td class="font-monospace fw-bold text-primary"><strong>{{ $a->code }}</strong></td>
                            <td class="fw-semibold text-dark">{{ $a->name }}</td>
                            <td>{{ optional($a->parent)->name ?? '-' }}</td>
                            <td>{!! $a->manager ? '<span class="badge bg-info text-dark"><i class="fas fa-user me-1"></i>'.$a->manager->name.'</span>' : '<span class="text-muted small">-</span>' !!}</td>
                            <td><span class="badge bg-{{ $typeColors[$a->type] ?? 'secondary' }}">{{ ucfirst($a->type) }}</span></td>
                            <td class="text-end font-monospace fw-bold {{ $a->current_balance < 0 ? 'text-danger' : 'text-dark' }}">{{ number_format($a->current_balance, 2) }}</td>
                            <td>{!! $a->is_active ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('coa-transfers.create', ['from_coa_id' => $a->id]) }}" class="btn btn-xs btn-outline-success py-1 px-2" title="Transfer Funds from this account">
                                        <i class="fa-solid fa-money-bill-transfer me-1"></i>Transfer
                                    </a>
                                    @unless($a->is_system)
                                    <a href="{{ route('coa.edit', $a) }}" class="btn btn-xs btn-outline-warning py-1 px-2" title="Edit Account"><i class="fas fa-edit"></i></a>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">No accounts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top py-3">{{ $accounts->links() }}</div>
    </div>
</div>
@endsection
