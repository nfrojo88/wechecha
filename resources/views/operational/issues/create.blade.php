@extends('layouts.app')
@section('title', 'Report Issue')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Report Site Issue</h1>
        <a href="{{ route('issues.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Issues
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('issues.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Project <span class="text-danger">*</span></label>
                        <select name="project_id" class="form-control" required>
                            <option value="">-- Select Project --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-control" required>
                            <option value="safety">Safety</option>
                            <option value="quality">Quality</option>
                            <option value="schedule">Schedule</option>
                            <option value="material">Material</option>
                            <option value="equipment">Equipment</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label>Issue Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Priority <span class="text-danger">*</span></label>
                        <select name="priority" class="form-control" required>
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label>Description / Details <span class="text-danger">*</span></label>
                    <textarea name="description" rows="4" class="form-control" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Submit Issue</button>
            </form>
        </div>
    </div>
</div>
@endsection
