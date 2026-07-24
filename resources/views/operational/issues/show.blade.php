@extends('layouts.app')
@section('title', 'View Issue: ' . $issue->title)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Issue: {{ $issue->title }}</h1>
        <div>
            <a href="{{ route('issues.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Issue Details</h6>
                </div>
                <div class="card-body">
                    <p><strong>Description:</strong></p>
                    <p class="text-muted">{{ $issue->description }}</p>
                    
                    <hr>
                    <p><strong>Comments & Activity:</strong></p>
                    @if($issue->comments && $issue->comments->count() > 0)
                        @foreach($issue->comments as $comment)
                            <div class="mb-3 p-3 bg-light rounded">
                                <small class="text-primary font-weight-bold">{{ $comment->user->name }}</small>
                                <small class="text-muted float-right">{{ $comment->created_at->diffForHumans() }}</small>
                                <p class="mb-0 mt-2">{{ $comment->comment }}</p>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No comments yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Metadata</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($issue->status == 'open') <span class="badge badge-danger">Open</span>
                                @elseif($issue->status == 'in_progress') <span class="badge badge-warning">In Progress</span>
                                @else <span class="badge badge-success">Resolved</span> @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Priority:</th>
                            <td>
                                <span class="badge badge-{{ $issue->priority == 'critical' ? 'danger' : ($issue->priority == 'high' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($issue->priority) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Category:</th>
                            <td>{{ ucfirst($issue->category) }}</td>
                        </tr>
                        <tr>
                            <th>Project:</th>
                            <td>{{ $issue->project->name }}</td>
                        </tr>
                        <tr>
                            <th>Reported By:</th>
                            <td>{{ $issue->reportedBy->name }}</td>
                        </tr>
                        <tr>
                            <th>Date:</th>
                            <td>{{ $issue->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
