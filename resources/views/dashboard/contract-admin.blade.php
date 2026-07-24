@extends('layouts.app')

@section('title', 'Contract Admin Dashboard')

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-0 fw-bold" style="color:var(--brand-800)">
                <i class="fa-solid fa-handshake me-2 text-primary"></i>Contract Admin Dashboard
            </h1>
            <p class="text-muted small mb-0">BOQ tracking · IPC management · Payment certificates · Earned value</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('client-ipcs.create') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-file-invoice-dollar me-1"></i> New Payment Certificate (IPC)
            </a>
            <a href="{{ route('boqs.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-table-list me-1"></i> All BOQs
            </a>
        </div>
    </div>

    {{-- ── KPI CARDS ──────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        @php
        $cards = [
            ['label' => 'Active Projects',   'value' => $kpi['active_projects'],                            'icon' => 'fa-building',             'color' => '#3b82f6', 'suffix' => ''],
            ['label' => 'Approved BOQ Value','value' => 'ETB ' . number_format($kpi['total_boq_value'], 0), 'icon' => 'fa-file-contract',        'color' => '#10b981', 'suffix' => ''],
            ['label' => 'Certified (Client)','value' => 'ETB ' . number_format($kpi['total_certified'], 0), 'icon' => 'fa-certificate',           'color' => '#f59e0b', 'suffix' => ''],
            ['label' => 'Payment This Month','value' => 'ETB ' . number_format($kpi['payment_this_month'], 0), 'icon' => 'fa-money-bill-wave',   'color' => '#06b6d4', 'suffix' => ''],
            ['label' => 'Pending Client IPCs','value' => $kpi['pending_client_ipcs'],                       'icon' => 'fa-file-invoice',          'color' => '#8b5cf6', 'suffix' => ''],
            ['label' => 'Pending Subcon IPCs','value' => $kpi['pending_subcon_ipcs'],                       'icon' => 'fa-users-between-lines',   'color' => '#ef4444', 'suffix' => ''],
        ];
        @endphp
        @foreach($cards as $card)
        <div class="col-6 col-xl-2">
            <div class="card border-0 shadow-sm h-100" style="{{ 'border-left:4px solid '.$card['color'].' !important;' }}">
                <div class="card-body py-3 px-3">
                    <p class="text-muted small mb-1">{{ $card['label'] }}</p>
                    <h4 class="fw-bold mb-0" style="{{ 'color:'.$card['color'] }}">{{ $card['value'] }}</h4>
                    <i class="fa-solid {{ $card['icon'] }} mt-1" style="{{ 'color:'.$card['color'].';opacity:.2;font-size:1.5rem;position:absolute;right:14px;top:14px;' }}"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── PROJECT BOQ PROGRESS ─────────────────────────────────────────── --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <span class="fw-semibold"><i class="fa-solid fa-chart-gantt me-2 text-primary"></i>Project BOQ & Payment Progress</span>
            <a href="{{ route('boqs.index') }}" class="btn btn-sm btn-outline-primary">View All BOQs</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Project</th>
                            <th>Approved BOQ</th>
                            <th>IPC Certified</th>
                            <th>Actual Paid</th>
                            <th>% Certified</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projectBOQProgress as $row)
                        <tr>
                            <td class="ps-3 fw-semibold">{{ $row['project']->name }}</td>
                            <td>ETB {{ number_format($row['boq_total'], 0) }}</td>
                            <td>ETB {{ number_format($row['certified'], 0) }}</td>
                            <td>ETB {{ number_format($row['paid'], 0) }}</td>
                            <td style="min-width:160px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:8px;">
                                        <div class="progress-bar {{ $row['pct'] >= 75 ? 'bg-success' : ($row['pct'] >= 40 ? 'bg-warning' : 'bg-primary') }}"
                                             style="{{ 'width:'.$row['pct'].'%' }}"></div>
                                    </div>
                                    <small class="fw-bold">{{ $row['pct'] }}%</small>
                                </div>
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('boqs.index') }}?project_id={{ $row['project']->id }}" class="btn btn-xs btn-outline-info" title="BOQ"><i class="fa-solid fa-table-list"></i></a>
                                <a href="{{ route('client-ipcs.create') }}?project_id={{ $row['project']->id }}" class="btn btn-xs btn-outline-primary" title="New IPC"><i class="fa-solid fa-plus"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No active projects with BOQs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">

        {{-- ── CLIENT IPCs (Payment Certificates) ─────────────────────── --}}
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <span class="fw-semibold"><i class="fa-solid fa-file-invoice-dollar me-2 text-warning"></i>Company IPCs (Sent to Client)</span>
                    <a href="{{ route('client-ipcs.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">IPC No</th>
                                    <th>Project</th>
                                    <th>Period</th>
                                    <th>Gross</th>
                                    <th>Net</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clientIpcs as $ipc)
                                <tr>
                                    <td class="ps-3 fw-semibold"><a href="{{ route('client-ipcs.show', $ipc) }}" class="text-decoration-none">{{ $ipc->ipc_no }}</a></td>
                                    <td>{{ Str::limit($ipc->project->name ?? '—', 20) }}</td>
                                    <td class="small text-muted">{{ $ipc->period_from->format('d M') }}–{{ $ipc->period_to->format('d M Y') }}</td>
                                    <td>{{ number_format($ipc->gross_amount, 0) }}</td>
                                    <td class="fw-semibold text-success">{{ number_format($ipc->net_amount, 0) }}</td>
                                    <td>
                                        @php
                                        $sColors=['approved'=>'success','paid'=>'dark','submitted'=>'primary','under_review'=>'info','rejected'=>'danger','draft'=>'secondary'];
                                        @endphp
                                        <span class="badge bg-{{ $sColors[$ipc->status] ?? 'secondary' }} small">{{ ucwords(str_replace('_',' ',$ipc->status)) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('client-ipcs.show', $ipc) }}" class="btn btn-xs btn-outline-secondary"><i class="fa-solid fa-eye"></i></a>
                                        @if(in_array($ipc->status, ['draft','under_review']))
                                        <a href="{{ route('client-ipcs.edit', $ipc) }}" class="btn btn-xs btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted py-3">No IPCs found. <a href="{{ route('client-ipcs.create') }}">Create one →</a></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── DAILY REPORT EARNED VALUE ───────────────────────────────── --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <span class="fw-semibold"><i class="fa-solid fa-calculator me-2 text-success"></i>Earned Value (Daily Reports → BOQ)</span>
                    <p class="small text-muted mb-0 mt-1">Sum of approved daily report work × BOQ rates vs. certified amount</p>
                </div>
                <div class="card-body p-0">
                    @forelse($dailyEarnedValue as $row)
                    <div class="px-3 py-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="fw-semibold small">{{ $row['project']->name }}</span>
                            <span class="badge bg-light text-dark border small">BOQ: ETB {{ number_format($row['boq_total'], 0) }}</span>
                        </div>
                        <div class="row g-1 small">
                            <div class="col-6">
                                <p class="mb-0 text-muted">Earned (Site Work)</p>
                                <p class="mb-0 fw-bold text-primary">ETB {{ number_format($row['earned'], 0) }}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-0 text-muted">IPC Certified</p>
                                <p class="mb-0 fw-bold text-success">ETB {{ number_format($row['certified'], 0) }}</p>
                            </div>
                        </div>
                        @if($row['boq_total'] > 0)
                        @php $pct = min(round($row['certified'] / $row['boq_total'] * 100, 1), 100); @endphp
                        <div class="progress mt-1" style="height:4px;">
                            <div class="progress-bar bg-success" style="{{ 'width:'.$pct.'%' }}"></div>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="text-center text-muted py-4 small">
                        <i class="fa-solid fa-file-circle-xmark fa-2x d-block mb-2 opacity-25"></i>
                        No approved BOQs with daily reports yet.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- ── ALL BOQs TABLE ──────────────────────────────────────────── --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <span class="fw-semibold"><i class="fa-solid fa-table-list me-2 text-info"></i>All Project BOQs</span>
                    <a href="{{ route('boqs.create') }}" class="btn btn-sm btn-outline-success">
                        <i class="fa-solid fa-plus me-1"></i>New BOQ
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Ref No</th>
                                    <th>Project</th>
                                    <th>Title</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allBoqs as $boq)
                                @php $bColors = ['approved'=>'success','draft'=>'secondary','revised'=>'warning'] @endphp
                                <tr>
                                    <td class="ps-3 fw-semibold small">{{ $boq->reference_number }}</td>
                                    <td class="small">{{ $boq->project->name ?? '—' }}</td>
                                    <td class="small">{{ Str::limit($boq->title, 30) }}</td>
                                    <td class="fw-semibold">ETB {{ number_format($boq->total_amount, 0) }}</td>
                                    <td><span class="badge bg-{{ $bColors[$boq->status] ?? 'secondary' }}">{{ ucfirst($boq->status) }}</span></td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('boqs.show', $boq) }}" class="btn btn-xs btn-outline-secondary"><i class="fa-solid fa-eye"></i></a>
                                        <a href="{{ route('client-ipcs.create') }}?boq_id={{ $boq->id }}&project_id={{ $boq->project_id }}" class="btn btn-xs btn-outline-primary" title="Create IPC from BOQ"><i class="fa-solid fa-file-invoice-dollar"></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No BOQs found. <a href="{{ route('boqs.create') }}">Create one →</a></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── RIGHT COLUMN ────────────────────────────────────────────── --}}
        <div class="col-lg-4">

            {{-- Recent Payments --}}
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between">
                    <span class="fw-semibold"><i class="fa-solid fa-money-bill-wave me-2 text-success"></i>Recent Payments</span>
                    <a href="{{ route('payments.create') }}" class="btn btn-xs btn-outline-success"><i class="fa-solid fa-plus"></i></a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentPayments as $pmt)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <div>
                            <p class="mb-0 fw-semibold small">{{ $pmt->reference_number }}</p>
                            <p class="mb-0 text-muted" style="font-size:.72rem;">{{ $pmt->project->name ?? '—' }} · {{ $pmt->payment_type }}</p>
                        </div>
                        <div class="text-end">
                            <p class="mb-0 fw-bold small text-success">ETB {{ number_format($pmt->amount, 0) }}</p>
                            <p class="mb-0 text-muted" style="font-size:.72rem;">{{ $pmt->payment_date->format('d M Y') }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted py-3 small">No payments recorded.</p>
                    @endforelse
                </div>
            </div>

            {{-- Subcon IPC Quick View --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between">
                    <span class="fw-semibold"><i class="fa-solid fa-users-between-lines me-2 text-danger"></i>Subcon IPCs</span>
                    <a href="{{ route('ipcs.index') }}" class="btn btn-xs btn-outline-danger">All</a>
                </div>
                <div class="card-body p-0">
                    @forelse($subconIpcs as $ipc)
                    @php $sc = $ipc->agreement?->subcontractor ?? null; @endphp
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <div>
                            <p class="mb-0 fw-semibold small">{{ $ipc->ipc_no ?? 'IPC-' . $ipc->id }}</p>
                            <p class="mb-0 text-muted" style="font-size:.72rem;">{{ $sc->name ?? ($ipc->agreement->subcontractor_name ?? '—') }}</p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ ['draft'=>'secondary','submitted'=>'primary','approved'=>'success','paid'=>'dark'][$ipc->status] ?? 'secondary' }} small">{{ ucfirst($ipc->status) }}</span>
                            <p class="mb-0 text-muted" style="font-size:.72rem;">ETB {{ number_format($ipc->current_certified ?? $ipc->net_amount ?? 0, 0) }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted py-3 small">No subcon IPCs.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
