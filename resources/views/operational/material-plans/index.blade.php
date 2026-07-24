@extends('layouts.app')
@section('title', 'Material Plans')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-list-check me-2"></i>Material Plans</h1>
        <a href="{{ route('material-plans.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Plan</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr><th>Title</th><th>Project</th><th>Week Period</th><th>Status</th><th>Created By</th><th class="text-center">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($plans as $p)
                    <tr>
                        <td><strong>{{ $p->title }}</strong></td>
                        <td>{{ $p->project->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->plan_week_start)->format('d M') }} - {{ \Carbon\Carbon::parse($p->plan_week_end)->format('d M Y') }}</td>
                        <td><span class="badge bg-{{ $p->status=='approved'?'success':($p->status=='draft'?'secondary':'warning') }}">{{ ucfirst($p->status) }}</span></td>
                        <td>{{ $p->createdBy->name }}</td>
                        <td class="text-center"><a href="{{ route('material-plans.show', $p) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4">No material plans found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
