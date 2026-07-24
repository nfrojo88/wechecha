@extends('layouts.app')
@section('title', 'Ticket Details')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fa-solid fa-ticket me-2"></i>Ticket {{ $ticket->ticket_no }}</h1>
        <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to List</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Ticket Conversation -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $ticket->subject }}</h6>
                    <div>{!! $ticket->priority_badge !!} {!! $ticket->status_badge !!}</div>
                </div>
                <div class="card-body">
                    <!-- Original Message -->
                    <div class="d-flex mb-4 pb-3 border-bottom">
                        <div class="flex-shrink-0">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 45px; height: 45px; font-size: 18px;">
                                {{ strtoupper(substr($ticket->user->name ?? 'U', 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 font-weight-bold">{{ $ticket->user->name ?? 'Unknown User' }}</h6>
                                <small class="text-muted">{{ $ticket->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                            <div class="text-xs text-muted mb-2">Category: {{ ucfirst(str_replace('_', ' ', $ticket->category)) }}</div>
                            <div class="p-3 bg-light rounded text-dark" style="white-space: pre-wrap;">{{ $ticket->description }}</div>
                        </div>
                    </div>

                    <!-- Replies -->
                    @foreach($ticket->replies as $reply)
                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                @if($reply->is_admin_reply)
                                    <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 45px; height: 45px; font-size: 18px;">
                                        <i class="fa-solid fa-user-shield"></i>
                                    </div>
                                @else
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 45px; height: 45px; font-size: 18px;">
                                        {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 font-weight-bold">
                                        {{ $reply->user->name ?? 'Unknown' }}
                                        @if($reply->is_admin_reply) <span class="badge bg-secondary ms-1 text-xs">Admin</span> @endif
                                    </h6>
                                    <small class="text-muted">{{ $reply->created_at->format('M d, Y h:i A') }}</small>
                                </div>
                                <div class="p-3 rounded text-dark {{ $reply->is_admin_reply ? 'bg-white border' : 'bg-light' }}" style="white-space: pre-wrap;">{{ $reply->message }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Reply Form -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Post a Reply</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.tickets.reply', $ticket) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea name="message" rows="4" class="form-control" placeholder="Type your response here..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane me-1"></i> Send Reply</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar / Ticket Actions -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ticket Settings</h6>
                </div>
                <div class="card-body">
                    <!-- Status Update -->
                    <form action="{{ route('admin.tickets.status', $ticket) }}" method="POST" class="mb-4">
                        @csrf
                        <label class="form-label font-weight-bold">Update Status</label>
                        <div class="input-group">
                            <select name="status" class="form-select">
                                <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            <button class="btn btn-outline-primary" type="submit">Update</button>
                        </div>
                    </form>

                    <hr>

                    <!-- Assignment -->
                    <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST">
                        @csrf
                        <label class="form-label font-weight-bold">Assign To Admin</label>
                        <div class="input-group">
                            <select name="assigned_to" class="form-select">
                                <option value="">-- Unassigned --</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" {{ $ticket->assigned_to == $admin->id ? 'selected' : '' }}>
                                        {{ $admin->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="btn btn-outline-info" type="submit">Assign</button>
                        </div>
                    </form>

                    <hr>
                    
                    <div class="text-sm">
                        <div class="mb-2"><span class="font-weight-bold">Created At:</span> <span class="float-end">{{ $ticket->created_at->format('M d, Y h:i A') }}</span></div>
                        <div class="mb-2"><span class="font-weight-bold">Last Updated:</span> <span class="float-end">{{ $ticket->updated_at->format('M d, Y h:i A') }}</span></div>
                        @if($ticket->resolved_at)
                        <div class="mb-2 text-success"><span class="font-weight-bold">Resolved At:</span> <span class="float-end">{{ $ticket->resolved_at->format('M d, Y h:i A') }}</span></div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Info</h6>
                </div>
                <div class="card-body text-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow mx-auto mb-3" style="width: 80px; height: 80px; font-size: 30px;">
                        {{ strtoupper(substr($ticket->user->name ?? 'U', 0, 1)) }}
                    </div>
                    <h5 class="font-weight-bold mb-0">{{ $ticket->user->name ?? 'Unknown' }}</h5>
                    <div class="text-muted text-sm mb-2">{{ $ticket->user->email }}</div>
                    @if(isset($ticket->user->employee))
                        <div class="badge bg-secondary mb-1">{{ $ticket->user->employee->department ?? 'No Dept' }}</div>
                        <div class="text-xs text-muted">{{ $ticket->user->employee->role_title ?? '' }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
