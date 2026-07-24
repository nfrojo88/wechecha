@extends('layouts.app')
@section('title', 'Messages')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-envelope me-2"></i>Inbox</h1>
        <a href="{{ route('messages.create') }}" class="btn btn-primary"><i class="fas fa-pen me-1"></i>Compose</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse($messages as $msg)
                <a href="{{ route('messages.show', $msg) }}" class="list-group-item list-group-item-action py-3 {{ $msg->status === 'sent' ? 'fw-bold bg-light' : '' }}">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <div class="mb-1">
                            @if($msg->status === 'sent')
                                <span class="badge bg-primary me-2">New</span>
                            @endif
                            <span class="me-3">{{ $msg->sender->name }}</span>
                            <span>{{ $msg->subject }}</span>
                        </div>
                        <small class="text-muted">{{ $msg->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-1 text-truncate" style="max-width: 80%; color: #6c757d;">
                        {{ strip_tags($msg->body) }}
                    </p>
                </a>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>Your inbox is empty.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
