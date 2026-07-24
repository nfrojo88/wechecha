@extends('layouts.app')
@section('title', 'IPCs')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Interim Payment Certificates (IPCs)</h1>
        <a href="{{ route('ipcs.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New IPC</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr><th>IPC No</th><th>Agreement No</th><th>Project</th><th>Period</th><th>Certified Amount</th><th>Status</th><th class="text-center">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($ipcs as $ipc)
                    <tr>
                        <td><strong>{{ $ipc->ipc_no }}</strong></td>
                        <td>{{ $ipc->agreement->agreement_no }}</td>
                        <td>{{ $ipc->project->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($ipc->period_from)->format('d M y') }} - {{ \Carbon\Carbon::parse($ipc->period_to)->format('d M y') }}</td>
                        <td>{{ number_format($ipc->current_certified, 2) }}</td>
                        <td><span class="badge bg-{{ $ipc->status == 'approved' ? 'success' : 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $ipc->status)) }}</span></td>
                        <td class="text-center"><a href="{{ route('ipcs.show', $ipc) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
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
