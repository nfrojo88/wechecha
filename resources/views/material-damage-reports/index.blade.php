@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Material Damage Reports</h2>
        <a href="{{ route('material-damage-reports.create') }}" class="btn btn-primary">Create Report</a>
    </div>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <p>List of damage reports goes here...</p>
        </div>
    </div>
</div>
@endsection
