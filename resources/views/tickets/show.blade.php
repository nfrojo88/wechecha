@extends('layouts.app')
@section('title', 'Ticket Details')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fa-solid fa-ticket me-2"></i>Ticket {{ $ticket->ticket_no }}</h1>
        <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to My Tickets</a>
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
                                {{ strtoupper(substr(auth()->user()->name ?? 'Y', 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 font-weight-bold">You</h6>
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
                                        {{ strtoupper(substr($reply->user->name ?? 'Y', 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 font-weight-bold">
                                        @if($reply->is_admin_reply)
                                            System Support
                                        @else
                                            You
                                        @endif
                                    </h6>
                                    <small class="text-muted">{{ $reply->created_at->format('M d, Y h:i A') }}</small>
                                </div>
                                <div class="p-3 rounded text-dark {{ $reply->is_admin_reply ? 'bg-white border shadow-sm' : 'bg-light' }}" style="white-space: pre-wrap;">{{ $reply->message }}</div>
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
                    <form action="{{ route('tickets.reply', $ticket) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea name="message" rows="4" class="form-control" placeholder="Type your response here..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane me-1"></i> Send Reply</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar / Ticket Info -->
        <div class="col-lg-4">
            <div class="card shadow mb-4 border-left-{{ $ticket->status == 'resolved' ? 'success' : ($ticket->status == 'closed' ? 'secondary' : 'primary') }}">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ticket Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-sm">
                        <div class="mb-3">
                            <span class="font-weight-bold d-block text-muted text-xs text-uppercase">Ticket Number</span>
                            <span class="fs-5">{{ $ticket->ticket_no }}</span>
                        </div>
                        <div class="mb-3">
                            <span class="font-weight-bold d-block text-muted text-xs text-uppercase">Current Status</span>
                            <div class="mt-1">{!! $ticket->status_badge !!}</div>
                        </div>
                        <div class="mb-3">
                            <span class="font-weight-bold d-block text-muted text-xs text-uppercase">Priority Level</span>
                            <div class="mt-1">{!! $ticket->priority_badge !!}</div>
                        </div>
                        <hr>
                        <div class="mb-2"><span class="font-weight-bold">Created:</span> <span class="float-end">{{ $ticket->created_at->format('M d, Y h:i A') }}</span></div>
                        <div class="mb-2"><span class="font-weight-bold">Last Update:</span> <span class="float-end">{{ $ticket->updated_at->format('M d, Y h:i A') }}</span></div>
                        @if($ticket->resolved_at)
                        <div class="mb-2 text-success"><span class="font-weight-bold">Resolved At:</span> <span class="float-end">{{ $ticket->resolved_at->format('M d, Y h:i A') }}</span></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
