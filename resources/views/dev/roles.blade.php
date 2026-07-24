@extends('layouts.app')
@section('title', 'Role Tester')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-vial text-warning"></i> Role Tester</h1>
    </div>

    <div class="alert alert-info border-left-info shadow-sm">
        <strong>Developer Tool:</strong> Use this page to quickly login as different roles to test the UI, Sidebar visibility, and Dashboard logic.
    </div>

    <div class="row">
        @foreach($roles as $role)
            <div class="col-xl-3 col-md-4 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Role ID: {{ $role->id }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <form action="{{ route('dev.roles.login') }}" method="POST">
                                @csrf
                                <input type="hidden" name="role" value="{{ $role->name }}">
                                <button type="submit" class="btn btn-sm btn-primary w-100 shadow-sm">
                                    <i class="fas fa-sign-in-alt"></i> Login as {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
