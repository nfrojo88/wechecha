@extends('layouts.app')
@section('title', 'Add Equipment')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add Equipment Master</h1>
        <a href="{{ route('equipment.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Equipment Details</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('equipment.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Equipment Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Excavator CAT-320" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Equipment Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control" placeholder="e.g. EXC-001" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Category</label>
                                <select name="category" class="form-control">
                                    <option value="Earthmoving">Earthmoving</option>
                                    <option value="Lifting">Lifting & Hoisting</option>
                                    <option value="Concrete">Concrete Equipment</option>
                                    <option value="Transport">Transport</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Usage Unit <span class="text-danger">*</span></label>
                                <select name="unit" class="form-control" required>
                                    <option value="hour">Hour</option>
                                    <option value="day">Day</option>
                                    <option value="km">Kilometer</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Hourly Rate ($) <span class="text-danger">*</span></label>
                                <input type="number" name="hourly_rate" class="form-control" step="0.01" value="0.00" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Daily Rate ($) <span class="text-danger">*</span></label>
                                <input type="number" name="daily_rate" class="form-control" step="0.01" value="0.00" required>
                            </div>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Save Equipment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
