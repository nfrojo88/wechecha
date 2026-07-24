@extends('layouts.app')
@section('title', 'Suppliers')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-truck me-2"></i>Suppliers</h1>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Supplier
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="suppliersTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Code</th><th>Name</th><th>Contact</th><th>Phone</th>
                            <th>Status</th><th>Rating</th><th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $s)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $s->code }}</span></td>
                            <td><strong>{{ $s->name }}</strong></td>
                            <td>{{ $s->contact_person ?? '-' }}</td>
                            <td>{{ $s->phone ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $s->status === 'active' ? 'success' : ($s->status === 'blacklisted' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($s->status) }}
                                </span>
                            </td>
                            <td>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $s->rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </td>
                            <td class="text-center">
                                <a href="{{ route('suppliers.show', $s) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('suppliers.edit', $s) }}" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('suppliers.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this supplier?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No suppliers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">{{ $suppliers->links() }}</div>
    </div>
</div>
@endsection
