@extends('layouts.app')
@section('title', 'Equipment Details')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Equipment: {{ $equipment->name }} ({{ $equipment->code }})</h1>
        <a href="{{ route('equipment.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
        </a>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <!-- Master Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Master Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th>Category:</th>
                            <td>{{ $equipment->category ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Unit:</th>
                            <td>{{ ucfirst($equipment->unit) }}</td>
                        </tr>
                        <tr>
                            <th>Hourly Rate:</th>
                            <td>${{ number_format($equipment->hourly_rate, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Daily Rate:</th>
                            <td>${{ number_format($equipment->daily_rate, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($equipment->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Log Productivity Form -->
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Log Productivity</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('equipment.logProductivity', $equipment) }}" method="POST">
                        @csrf
                        <div class="form-group mb-2">
                            <label class="small font-weight-bold">Project <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-control form-control-sm" required>
                                <option value="">Select Project...</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label class="small font-weight-bold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="work_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group mb-2">
                            <label class="small font-weight-bold">Hours Operated <span class="text-danger">*</span></label>
                            <input type="number" name="hours_operated" class="form-control form-control-sm" step="0.5" min="0.5" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">Task / Remarks</label>
                            <input type="text" name="task_performed" class="form-control form-control-sm">
                        </div>
                        <button type="submit" class="btn btn-info btn-sm btn-block"><i class="fas fa-plus"></i> Save Log</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Usage History -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Usage History</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Project</th>
                                <th>Hours</th>
                                <th>Task Performed</th>
                                <th>Recorded By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($equipment->productivities as $prod)
                            <tr>
                                <td>{{ $prod->work_date->format('M d, Y') }}</td>
                                <td>{{ $prod->project->name }}</td>
                                <td><span class="badge badge-info">{{ $prod->hours_operated }} hrs</span></td>
                                <td>{{ $prod->task_performed ?? '-' }}</td>
                                <td>{{ $prod->recordedBy->name ?? 'Unknown' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No productivity logs found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
