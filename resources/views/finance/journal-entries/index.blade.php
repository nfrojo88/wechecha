@extends('layouts.app')
@section('title', 'Journal Entries')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-book me-2"></i>Journal Entries</h1>
        <a href="{{ route('journal-entries.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Entry</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr><th>Entry No</th><th>Date</th><th>Reference</th><th>Description</th><th>Created By</th><th class="text-center">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($entries as $e)
                    <tr>
                        <td><strong>{{ $e->entry_no }}</strong></td>
                        <td>{{ $e->entry_date->format('d M Y') }}</td>
                        <td>{{ $e->reference ?? '-' }}</td>
                        <td>{{ Str::limit($e->description, 50) }}</td>
                        <td>{{ $e->createdBy->name }}</td>
                        <td class="text-center"><a href="{{ route('journal-entries.show', $e) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4">No journal entries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
