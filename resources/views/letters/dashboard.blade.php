@extends('layouts.app')

@section('title', 'Correspondence Dashboard - Letters Management')

@section('content')
<div class="container-fluid py-3">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="fa-solid fa-envelope-open-text text-primary me-2"></i>Correspondence & Letter Management
            </h3>
            <p class="text-muted small mb-0">Manage incoming & outgoing official letters, routing, attachments, and departmental forwarding workflows.</p>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->hasAnyRole(['admin', 'global_admin', 'secretary']))
            <a href="{{ route('letters.create') }}" class="btn btn-primary shadow-sm px-3 fw-bold">
                <i class="fa-solid fa-plus me-1"></i> New Letter
            </a>
            @endif
            <a href="{{ route('letters.index') }}" class="btn btn-outline-primary shadow-sm px-3">
                <i class="fa-solid fa-inbox me-1"></i> My Inbox
                @if(($metrics['my_inbox'] ?? 0) > 0)
                    <span class="badge bg-danger ms-1">{{ $metrics['my_inbox'] }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- Metric Cards --}}
    <div class="row g-3 mb-4">
        {{-- Total --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-muted small fw-bold">Total Letters</div>
                            <div class="fs-3 fw-bold text-dark mt-1">{{ number_format($metrics['total']) }}</div>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary">
                            <i class="fa-solid fa-envelopes-bulk fa-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Incoming --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-muted small fw-bold">Incoming Letters</div>
                            <div class="fs-3 fw-bold text-info mt-1">{{ number_format($metrics['incoming']) }}</div>
                        </div>
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info">
                            <i class="fa-solid fa-inbox fa-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Outgoing --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-muted small fw-bold">Outgoing Letters</div>
                            <div class="fs-3 fw-bold text-warning mt-1">{{ number_format($metrics['outgoing']) }}</div>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning">
                            <i class="fa-solid fa-paper-plane fa-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Review --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-4 border-danger">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-muted small fw-bold">Pending Review</div>
                            <div class="fs-3 fw-bold text-danger mt-1">{{ number_format($metrics['pending']) }}</div>
                        </div>
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 text-danger">
                            <i class="fa-solid fa-clock fa-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Compose Banner & Recent Activity --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="fa-solid fa-clock-rotate-left text-muted me-2"></i>Recent Correspondence
                    </h5>
                    <a href="{{ route('letters.index', ['tab' => 'all']) }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Letter #</th>
                                    <th>Type</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Sender / Target</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLetters as $letter)
                                <tr>
                                    <td class="ps-3 fw-bold">
                                        <a href="{{ route('letters.show', $letter->id) }}" class="text-decoration-none text-primary">
                                            {{ $letter->letter_number }}
                                        </a>
                                        @if($letter->priority === 'urgent')
                                            <span class="badge bg-danger ms-1" style="font-size: 0.7rem;">URGENT</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($letter->type === 'incoming')
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                                <i class="fa-solid fa-arrow-down-left me-1"></i> Incoming
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">
                                                <i class="fa-solid fa-arrow-up-right me-1"></i> Outgoing
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-truncate text-dark" style="max-width: 220px;" title="{{ $letter->subject }}">
                                            {{ $letter->subject }}
                                        </div>
                                    </td>
                                    <td class="small text-muted">{{ $letter->date ? $letter->date->format('M d, Y') : '-' }}</td>
                                    <td class="small">
                                        {{ $letter->type === 'incoming' ? ($letter->sender ?? 'External') : ($letter->recipient_organization ?? 'External') }}
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match($letter->status) {
                                                'pending'    => 'bg-warning text-dark',
                                                'viewed'     => 'bg-info text-dark',
                                                'redirected' => 'bg-primary text-white',
                                                'closed'     => 'bg-success text-white',
                                                default      => 'bg-secondary text-white'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($letter->status) }}</span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('letters.show', $letter->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-folder-open fa-2x mb-2 d-block text-muted opacity-50"></i>
                                        No correspondence records found yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions & Status Summary Card --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0"><i class="fa-solid fa-bolt text-warning me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    @if(auth()->user()->hasAnyRole(['admin', 'global_admin', 'secretary']))
                    <div class="d-grid gap-2 mb-3">
                        <a href="{{ route('letters.create', ['type' => 'incoming']) }}" class="btn btn-info text-white fw-bold py-2">
                            <i class="fa-solid fa-file-import me-1"></i> Register Incoming Letter
                        </a>
                        <a href="{{ route('letters.create', ['type' => 'outgoing']) }}" class="btn btn-warning text-dark fw-bold py-2">
                            <i class="fa-solid fa-file-export me-1"></i> Register Outgoing Letter
                        </a>
                    </div>
                    @endif
                    <div class="d-grid gap-2">
                        <a href="{{ route('letters.index', ['tab' => 'inbox']) }}" class="btn btn-outline-primary py-2">
                            <i class="fa-solid fa-inbox me-1"></i> Open My Letters Inbox
                        </a>
                        <a href="{{ route('letters.index', ['tab' => 'sent']) }}" class="btn btn-outline-secondary py-2">
                            <i class="fa-solid fa-paper-plane me-1"></i> View Letters Created by Me
                        </a>
                    </div>
                </div>
            </div>

            {{-- Breakdown Card --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0"><i class="fa-solid fa-chart-pie text-primary me-2"></i>Status Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted"><i class="fa-solid fa-clock text-warning me-1"></i> Pending Review</span>
                        <span class="badge bg-warning text-dark fw-bold">{{ $metrics['pending'] }}</span>
                    </div>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-warning" style="width: {{ $metrics['total'] > 0 ? round(($metrics['pending'] / $metrics['total']) * 100) : 0 }}%"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted"><i class="fa-solid fa-check-double text-success me-1"></i> Closed / Reviewed</span>
                        <span class="badge bg-success fw-bold">{{ $metrics['closed'] }}</span>
                    </div>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: {{ $metrics['total'] > 0 ? round(($metrics['closed'] / $metrics['total']) * 100) : 0 }}%"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted"><i class="fa-solid fa-arrow-down-left text-info me-1"></i> Incoming vs Outgoing</span>
                        <span class="small fw-bold text-dark">{{ $metrics['incoming'] }} / {{ $metrics['outgoing'] }}</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: {{ $metrics['total'] > 0 ? round(($metrics['incoming'] / $metrics['total']) * 100) : 50 }}%"></div>
                        <div class="progress-bar bg-warning" style="width: {{ $metrics['total'] > 0 ? round(($metrics['outgoing'] / $metrics['total']) * 100) : 50 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
