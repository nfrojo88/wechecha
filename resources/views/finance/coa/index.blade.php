@extends('layouts.app')
@section('title', 'Chart of Accounts')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-sitemap me-2"></i>Chart of Accounts</h1>
        <a href="{{ route('coa.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Account</a>
    </div>
    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @php $typeColors = ['asset'=>'primary','liability'=>'danger','equity'=>'success','revenue'=>'info','expense'=>'warning']; @endphp
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr><th>Code</th><th>Name</th><th>Parent</th><th>Manager</th><th>Type</th><th>Balance</th><th>Active</th><th class="text-center">Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $a)
                        <tr>
                            <td><strong>{{ $a->code }}</strong></td>
                            <td>{{ $a->name }}</td>
                            <td>{{ optional($a->parent)->name ?? '-' }}</td>
                            <td>{!! $a->manager ? '<span class="badge bg-info"><i class="fas fa-user me-1"></i>'.$a->manager->name.'</span>' : '<span class="text-muted small">-</span>' !!}</td>
                            <td><span class="badge bg-{{ $typeColors[$a->type] ?? 'secondary' }}">{{ ucfirst($a->type) }}</span></td>
                            <td class="{{ $a->current_balance < 0 ? 'text-danger' : '' }}">{{ number_format($a->current_balance, 2) }}</td>
                            <td>{!! $a->is_active ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                            <td class="text-center">
                                @unless($a->is_system)
                                <a href="{{ route('coa.edit', $a) }}" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></a>
                                @endunless
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No accounts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">{{ $accounts->links() }}</div>
    </div>
</div>
@endsection
