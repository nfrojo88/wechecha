@extends('layouts.app')
@section('title', 'Cut Optimizations')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-scissors me-2"></i>Cut Optimizations</h1>
        <a href="{{ route('cut-optimizations.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Plan</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr><th>Title</th><th>Project</th><th>Material Type</th><th>Standard Length</th><th>Waste %</th><th>Status</th><th class="text-center">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($optimizations as $o)
                    <tr>
                        <td><strong>{{ $o->title }}</strong></td>
                        <td>{{ $o->project->name }}</td>
                        <td>{{ $o->material_type }}</td>
                        <td>{{ $o->standard_length }}</td>
                        <td>{{ $o->total_waste_percent ? $o->total_waste_percent . '%' : '-' }}</td>
                        <td><span class="badge bg-{{ $o->status=='optimized'?'success':'secondary' }}">{{ ucfirst($o->status) }}</span></td>
                        <td class="text-center"><a href="{{ route('cut-optimizations.show', $o) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4">No cut optimization plans found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
