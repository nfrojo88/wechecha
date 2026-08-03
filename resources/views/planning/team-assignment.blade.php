@extends('layouts.app')

@section('title', 'Assign Project Team')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="fa-solid fa-users text-primary me-2"></i>Assign Project Teams
            </h1>
            <p class="text-muted small mb-0">Assign planning team members to each project. Assigned members will see and work on their project data.</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Projects table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-diagram-project text-primary me-2"></i>Projects</h6>
            <span class="badge bg-primary-subtle text-primary">{{ $projects->count() }} Projects</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Project Name</th>
                        <th>Code</th>
                        <th>Status</th>
                        <th>Current Team Members</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-semibold">{{ $project->name }}</div>
                            <div class="text-muted small">{{ $project->location ?? '' }}</div>
                        </td>
                        <td><code class="small">{{ $project->code }}</code></td>
                        <td>
                            <span class="badge rounded-pill
                                {{ $project->status === 'active' ? 'bg-success-subtle text-success' :
                                   ($project->status === 'planning' ? 'bg-info-subtle text-info' : 'bg-secondary-subtle text-secondary') }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </td>
                        <td>
                            @if($project->team->isEmpty())
                                <span class="text-muted small fst-italic"><i class="fa-solid fa-user-xmark me-1 text-warning"></i>No team assigned yet</span>
                            @else
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($project->team as $member)
                                        <span class="badge bg-primary-subtle text-primary rounded-pill">
                                            <i class="fa-solid fa-user me-1"></i>{{ $member->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-primary rounded-pill px-3"
                                    data-bs-toggle="modal"
                                    data-bs-target="#assignModal{{ $project->id }}">
                                <i class="fa-solid fa-user-plus me-1"></i>Manage Team
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-folder-open fa-2x mb-2 d-block opacity-25"></i>
                            No projects found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modals --}}
@foreach($projects as $project)
<div class="modal fade" id="assignModal{{ $project->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <form action="{{ route('planning.team.update', $project) }}" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header border-bottom pb-3">
                <div>
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="fa-solid fa-users-gear text-primary me-2"></i>Manage Team
                    </h5>
                    <small class="text-muted">{{ $project->name }} ({{ $project->code }})</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    <i class="fa-solid fa-circle-info text-info me-1"></i>
                    Select team members to assign to this project. They will have access to all planning data for this project.
                </p>

                {{-- Search inside modal --}}
                <div class="input-group mb-3">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-search text-muted small"></i></span>
                    <input type="text" class="form-control bg-light border-start-0 ps-0"
                           placeholder="Search team member..."
                           oninput="filterPlanners(this, 'plannerList{{ $project->id }}')"
                           autocomplete="off">
                </div>

                <div class="list-group list-group-flush" id="plannerList{{ $project->id }}">
                    @forelse($planners as $planner)
                        @php $isAssigned = $project->team->contains($planner->id); @endphp
                        <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 cursor-pointer planner-item rounded-3 mb-1"
                               data-name="{{ strtolower($planner->name) }}">
                            <input class="form-check-input flex-shrink-0 mt-0"
                                   type="checkbox"
                                   name="user_ids[]"
                                   value="{{ $planner->id }}"
                                   {{ $isAssigned ? 'checked' : '' }}>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">{{ $planner->name }}</div>
                                <div class="text-muted" style="font-size:.75rem">{{ $planner->email }}</div>
                            </div>
                            @php
                                $roles = $planner->getRoleNames();
                            @endphp
                            @if($roles->isNotEmpty())
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size:.7rem">
                                    {{ ucwords(str_replace(['_','-'], ' ', $roles->first())) }}
                                </span>
                            @endif
                        </label>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="fa-solid fa-user-slash fa-lg mb-2 d-block opacity-25"></i>
                            No eligible users found.
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer border-top pt-3">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i>Save Team
                </button>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
function filterPlanners(input, listId) {
    const term = input.value.toLowerCase();
    document.querySelectorAll('#' + listId + ' .planner-item').forEach(function(item) {
        const name = item.getAttribute('data-name') || '';
        item.style.display = name.includes(term) ? '' : 'none';
    });
}
</script>
@endpush
