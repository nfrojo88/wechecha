@extends('layouts.app')
@section('title', 'Issues')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-triangle-exclamation me-2"></i>Issues Tracking</h1>
        <a href="{{ route('issues.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Report Issue</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr><th>ID</th><th>Title</th><th>Project</th><th>Category</th><th>Priority</th><th>Status</th><th>Reported By</th><th class="text-center">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($issues as $i)
                    <tr>
                        <td>#{{ $i->id }}</td>
                        <td><strong>{{ $i->title }}</strong></td>
                        <td>{{ $i->project->name }}</td>
                        <td>{{ ucfirst($i->category) }}</td>
                        <td><span class="badge bg-{{ $i->priority=='critical'?'danger':($i->priority=='high'?'warning':'info') }}">{{ ucfirst($i->priority) }}</span></td>
                        <td><span class="badge bg-{{ $i->status=='resolved'?'success':($i->status=='open'?'danger':'secondary') }}">{{ ucfirst($i->status) }}</span></td>
                        <td>{{ $i->reportedBy->name }}</td>
                        <td class="text-center"><a href="{{ route('issues.show', $i) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4">No issues reported.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
