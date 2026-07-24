@extends('layouts.app')
@section('title', 'Material Usages')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-screwdriver-wrench me-2"></i>Material Usages</h1>
        <a href="{{ route('material-usages.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Log Usage</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr><th>Usage No</th><th>Project</th><th>Store</th><th>Date</th><th>Status</th><th>Logged By</th><th class="text-center">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($usages as $u)
                    <tr>
                        <td><strong>{{ $u->usage_no }}</strong></td>
                        <td>{{ $u->project->name }}</td>
                        <td>{{ $u->store->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($u->usage_date)->format('d M Y') }}</td>
                        <td><span class="badge bg-{{ $u->status=='confirmed'?'success':'secondary' }}">{{ ucfirst($u->status) }}</span></td>
                        <td>{{ $u->createdBy->name }}</td>
                        <td class="text-center">
                            <a href="{{ route('material-usages.show', $u) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a>
                            @if($u->status === 'draft')
                                <form action="{{ route('material-usages.confirm', $u) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Confirm Usage"><i class="fas fa-check"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4">No material usages found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
