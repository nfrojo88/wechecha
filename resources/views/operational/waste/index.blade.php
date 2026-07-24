@extends('layouts.app')
@section('title', 'Waste Tracking')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-trash-can me-2"></i>Waste Tracking</h1>
        <a href="{{ route('waste.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Report Waste</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr><th>ID</th><th>Project</th><th>Store</th><th>Date</th><th>Reason</th><th>Status</th><th>Reported By</th><th class="text-center">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($wasteRecords as $w)
                    <tr>
                        <td>#{{ $w->id }}</td>
                        <td>{{ $w->project->name }}</td>
                        <td>{{ $w->store->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($w->waste_date)->format('d M Y') }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $w->reason)) }}</td>
                        <td><span class="badge bg-{{ $w->status=='verified'?'success':($w->status=='written_off'?'danger':'warning') }}">{{ ucfirst($w->status) }}</span></td>
                        <td>{{ $w->recordedBy->name }}</td>
                        <td class="text-center">
                            <a href="{{ route('waste.show', $w) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a>
                            @if($w->status === 'reported')
                                <form action="{{ route('waste.verify', $w) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Verify Waste"><i class="fas fa-check"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4">No waste records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
