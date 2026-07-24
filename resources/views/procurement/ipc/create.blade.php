@extends('layouts.app')
@section('title', 'Generate IPC')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Generate Interim Payment Certificate</h1>
        <a href="{{ route('ipcs.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
        </a>
    </div>

    <!-- Step 1: Select Agreement -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('ipcs.create') }}" method="GET" class="form-inline">
                <label class="mr-2">Select Subcontractor Agreement:</label>
                <select name="agreement_id" class="form-control mr-2" required onchange="this.form.submit()">
                    <option value="">-- Choose Agreement --</option>
                    @foreach($agreements as $agr)
                        <option value="{{ $agr->id }}" {{ request('agreement_id') == $agr->id ? 'selected' : '' }}>
                            {{ $agr->agreement_no }} - {{ $agr->subcontractor->name }}
                        </option>
                    @endforeach
                </select>
                <noscript><button type="submit" class="btn btn-primary">Load</button></noscript>
            </form>
        </div>
    </div>

    @if($selectedAgreement)
    <!-- Step 2: Fill Quantities -->
    <form action="{{ route('ipcs.store') }}" method="POST">
        @csrf
        <input type="hidden" name="agreement_id" value="{{ $selectedAgreement->id }}">
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">IPC Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Period From <span class="text-danger">*</span></label>
                        <input type="date" name="period_from" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Period To <span class="text-danger">*</span></label>
                        <input type="date" name="period_to" class="form-control" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Work Items Certification</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Total Contract Qty</th>
                            <th>Previous Qty</th>
                            <th width="15%">Current Qty <span class="text-danger">*</span></th>
                            <th>Unit Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selectedAgreement->items as $index => $item)
                        <!-- In a real app, you would query previous IPCs to get actual previous qty. For now we assume 0 or passed from backend -->
                        @php $previousQty = 0; @endphp
                        <tr>
                            <td>
                                {{ $item->task_description }}
                                <input type="hidden" name="items[{{$index}}][agreement_item_id]" value="{{ $item->id }}">
                            </td>
                            <td>{{ $item->quantity }} {{ $item->unit }}</td>
                            <td>
                                <input type="number" name="items[{{$index}}][previous_qty]" value="{{ $previousQty }}" class="form-control-plaintext" readonly>
                            </td>
                            <td>
                                <input type="number" name="items[{{$index}}][current_qty]" class="form-control" step="0.01" min="0" max="{{ $item->quantity - $previousQty }}" required>
                            </td>
                            <td>
                                <input type="number" name="items[{{$index}}][unit_rate]" value="{{ $item->unit_rate }}" class="form-control-plaintext" readonly>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success"><i class="fas fa-calculator"></i> Calculate & Draft IPC</button>
            </div>
        </div>
    </form>
    @endif
</div>
@endsection
