@extends('layouts.app')
@section('title', 'IPC Records')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>IPC Records</h1>
        <a href="{{ route('ipcs.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New IPC</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr><th>IPC No</th><th>Subcontractor</th><th>Period</th><th>Gross Amt</th><th>Net Amt</th><th>Status</th><th class="text-center">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($ipcs as $i)
                    <tr>
                        <td><strong>{{ $i->ipc_no }}</strong></td>
                        <td>{{ $i->agreement->subcontractor->name }}</td>
                        <td>{{ $i->period_from->format('d M') }} - {{ $i->period_to->format('d M Y') }}</td>
                        <td>{{ number_format($i->gross_amount, 2) }}</td>
                        <td>{{ number_format($i->net_amount, 2) }}</td>
                        <td><span class="badge bg-{{ $i->status=='certified'?'success':($i->status=='draft'?'secondary':'warning') }}">{{ ucfirst($i->status) }}</span></td>
                        <td class="text-center"><a href="{{ route('ipcs.show', $i) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4">No IPC records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
