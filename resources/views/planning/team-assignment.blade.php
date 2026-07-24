@extends('layouts.app')

@section('title', 'Assign Project Team')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="page-title mb-4"><i class="fa-solid fa-users me-2 text-primary"></i>Assign Project Team</h1>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Project Name</th>
                                    <th>Code</th>
                                    <th>Current Team (Planners)</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($projects as $project)
                                <tr>
                                    <td class="fw-bold">{{ $project->name }}</td>
                                    <td>{{ $project->code }}</td>
                                    <td>
                                        @if($project->team->isEmpty())
                                            <span class="text-muted small italic">No planners assigned</span>
                                        @else
                                            @foreach($project->team as $member)
                                                <span class="badge bg-secondary me-1">{{ $member->name }}</span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#assignModal{{ $project->id }}">
                                            <i class="fa-solid fa-user-plus me-1"></i> Manage Team
                                        </button>
                                    </td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

@foreach($projects as $project)
<!-- Assign Modal -->
<div class="modal fade" id="assignModal{{ $project->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('planning.team.update', $project) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Manage Team for {{ $project->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Select planners to assign to this project. Planners will only see data for projects they are assigned to.</p>
                
                <div class="list-group">
                    @foreach($planners as $planner)
                        @php
                            $isAssigned = $project->team->contains($planner->id);
                        @endphp
                        <label class="list-group-item d-flex gap-2">
                            <input class="form-check-input flex-shrink-0" type="checkbox" name="user_ids[]" value="{{ $planner->id }}" {{ $isAssigned ? 'checked' : '' }}>
                            <span>
                                {{ $planner->name }}
                                <small class="d-block text-muted">{{ $planner->email }}</small>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Assignments</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection
