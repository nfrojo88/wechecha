@extends('layouts.app')
@section('title', 'IPC Details')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Interim Payment Certificate: {{ $ipc->ipc_no }}</h1>
        <div>
            <a href="{{ route('ipcs.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
            </a>
            @if($ipc->status == 'draft')
                <!-- You could add an approval form here to transition to approved -->
                <button class="btn btn-sm btn-success shadow-sm"><i class="fas fa-check"></i> Approve IPC</button>
            @endif
            <button class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-print"></i> Print</button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Certificate Overview</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($ipc->status == 'draft') <span class="badge badge-secondary">Draft</span>
                                @elseif($ipc->status == 'approved') <span class="badge badge-success">Approved</span>
                                @else <span class="badge badge-info">{{ ucfirst($ipc->status) }}</span> @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Agreement No:</th>
                            <td>{{ $ipc->agreement->agreement_no }}</td>
                        </tr>
                        <tr>
                            <th>Subcontractor:</th>
                            <td>{{ $ipc->agreement->subcontractor->name }}</td>
                        </tr>
                        <tr>
                            <th>Period From:</th>
                            <td>{{ $ipc->period_from->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Period To:</th>
                            <td>{{ $ipc->period_to->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Prepared By:</th>
                            <td>{{ $ipc->preparedBy->name ?? 'System' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow mb-4 border-left-success">
                <div class="card-body">
                    <h6 class="font-weight-bold text-success mb-3">Payment Summary</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td>Gross Amount This Period:</td>
                            <td class="text-right">${{ number_format($ipc->gross_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Less Retention (5%):</td>
                            <td class="text-right text-danger">- ${{ number_format($ipc->retention_amount, 2) }}</td>
                        </tr>
                        <tr class="border-top font-weight-bold">
                            <td>Net Amount Certified:</td>
                            <td class="text-right h5 text-gray-800">${{ number_format($ipc->net_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Certified Work Details</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Task Description</th>
                                <th>Current Qty</th>
                                <th>Cum. Qty</th>
                                <th>Unit Rate</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ipc->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->agreementItem->task_description ?? 'Unknown Task' }}</td>
                                <td>{{ number_format($item->current_qty, 2) }}</td>
                                <td>{{ number_format($item->cumulative_qty, 2) }}</td>
                                <td>${{ number_format($item->agreementItem->unit_rate ?? 0, 2) }}</td>
                                <td>${{ number_format($item->current_amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No certified items.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-right">Total Gross:</th>
                                <th>${{ number_format($ipc->gross_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
