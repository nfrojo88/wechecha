@extends('layouts.app')

@section('title', 'Takeoff Sheets')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-file-invoice me-2 text-primary"></i>Takeoff Sheets
        </h1>
        <p class="text-muted small mb-0 mt-1">Manage quantity takeoffs and material estimates.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('takeoff.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Create Takeoff
        </a>
    </div>
</div>

<div class="card border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold" style="color:var(--gray-700);">
            <i class="fa-solid fa-list me-2 text-primary"></i>All Takeoff Sheets
        </span>
    </div>
    <div class="card-body p-0">
        @if($takeoffSheets->isEmpty())
            <div class="text-center py-5" style="color:var(--gray-400);">
                <i class="fa-solid fa-file-invoice fa-3x d-block mb-3" style="opacity:.3;"></i>
                <p class="fw-semibold mb-1">No Takeoff Sheets Found</p>
                <p class="small mb-3">Create your first takeoff sheet to start estimating materials.</p>
                <a href="{{ route('takeoff.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus"></i> Create Takeoff
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID / Title</th>
                            <th>Project</th>
                            <th>Type</th>
                            <th>Creator</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($takeoffSheets as $sheet)
                        <tr>
                            <td>
                                <div class="fw-semibold" style="color:var(--gray-800);">{{ $sheet->title }}</div>
                                <div class="small text-muted">#{{ str_pad($sheet->id, 5, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td>
                                @if($sheet->project)
                                    <a href="{{ route('projects.show', $sheet->project) }}" class="text-decoration-none fw-semibold" style="color:var(--brand-500);">
                                        {{ $sheet->project->name }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($sheet->sheet_type) }}</span>
                            </td>
                            <td style="color:var(--gray-600);font-size:13px;">
                                {{ $sheet->creator->name ?? 'Unknown' }}
                            </td>
                            <td>
                                @php
                                    $badge = match(strtolower($sheet->status)) {
                                        'draft' => 'secondary',
                                        'approved' => 'success',
                                        'in_progress', 'in progress' => 'primary',
                                        'rejected' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ strtoupper($sheet->status) }}</span>
                            </td>
                            <td style="color:var(--gray-500);font-size:12px;">
                                {{ $sheet->created_at->format('d M Y') }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('takeoff.show', $sheet) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if(auth()->id() == $sheet->created_by)
                                    <form action="{{ route('takeoff.destroy', $sheet->id) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger ms-1" title="Delete Takeoff" onclick="return confirm('Are you sure you want to delete this takeoff sheet?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if($takeoffSheets->hasPages())
        <div class="card-footer border-0 bg-white pt-3 pb-3">
            {{ $takeoffSheets->links() }}
        </div>
    @endif
</div>
@endsection
