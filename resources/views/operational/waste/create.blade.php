@extends('layouts.app')
@section('title', 'Log Waste')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Log Material Waste/Loss</h1>
        <a href="{{ route('waste.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
        </a>
    </div>

    <form action="{{ route('waste.store') }}" method="POST">
        @csrf
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Waste Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Waste Date <span class="text-danger">*</span></label>
                        <input type="date" name="waste_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Reason <span class="text-danger">*</span></label>
                        <select name="reason" class="form-control" required>
                            <option value="">-- Select Reason --</option>
                            <option value="damage">Damage</option>
                            <option value="excess_cutting">Excess Cutting</option>
                            <option value="quality_reject">Quality Reject</option>
                            <option value="theft">Theft</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Project <span class="text-danger">*</span></label>
                        <select name="project_id" class="form-control" required>
                            <option value="">-- Select Project --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Store (Where it happened) <span class="text-danger">*</span></label>
                        <select name="store_id" class="form-control" required>
                            <option value="">-- Select Store --</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label>Description / Notes</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Wasted Items</h6>
                <button type="button" class="btn btn-sm btn-primary" onclick="addWasteItemRow()">
                    <i class="fas fa-plus fa-sm"></i> Add Item
                </button>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="wasteItemsTable">
                    <thead>
                        <tr>
                            <th>Product / Material <span class="text-danger">*</span></th>
                            <th width="20%">Quantity <span class="text-danger">*</span></th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="items[0][product_id]" class="form-control" required>
                                    <option value="">-- Select Product --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->unit }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[0][quantity]" class="form-control" step="0.01" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-danger"><i class="fas fa-save"></i> Log Waste</button>
            </div>
        </div>
    </form>
</div>

<script>
    let wasteItemIndex = 1;
    function addWasteItemRow() {
        const tbody = document.querySelector('#wasteItemsTable tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <select name="items[${wasteItemIndex}][product_id]" class="form-control" required>
                    <option value="">-- Select Product --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->unit }})</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="items[${wasteItemIndex}][quantity]" class="form-control" step="0.01" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        wasteItemIndex++;
    }
</script>
@endsection
