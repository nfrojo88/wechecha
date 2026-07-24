@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Tool Check-out & Return</h2>
        <a href="{{ route('tool-transactions.create') }}" class="btn btn-primary">Check Out Tool</a>
    </div>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <p>List of tool transactions goes here...</p>
        </div>
    </div>
</div>
@endsection
