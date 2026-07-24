@extends('layouts.app')
@section('title', 'My Support Tickets')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fa-solid fa-headset me-2"></i>My Support Tickets</h1>
        <a href="{{ route('tickets.create') }}" class="btn btn-primary shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> New Ticket
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Your Tickets</h6>
        </div>
        <div class="card-body p-0">
            @if($tickets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Ticket No</th>
                                <th>Subject</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Replies</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                            <tr>
                                <td class="font-weight-bold">{{ $ticket->ticket_no }}</td>
                                <td>
                                    <a href="{{ route('tickets.show', $ticket) }}" class="text-decoration-none text-dark font-weight-bold">
                                        {{ Str::limit($ticket->subject, 50) }}
                                    </a>
                                </td>
                                <td>{{ ucfirst(str_replace('_', ' ', $ticket->category)) }}</td>
                                <td>{!! $ticket->status_badge !!}</td>
                                <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-secondary rounded-pill">{{ $ticket->replies_count }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <i class="fa-solid fa-ticket-simple fs-1 text-gray-300 mb-3"></i>
                    <h5>No Tickets Found</h5>
                    <p class="mb-4">You haven't submitted any support tickets yet.</p>
                    <a href="{{ route('tickets.create') }}" class="btn btn-primary">Submit a Request</a>
                </div>
            @endif
        </div>
        @if($tickets->hasPages())
        <div class="card-footer">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
