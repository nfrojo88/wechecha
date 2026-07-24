@extends('layouts.app')
@section('title', 'System Settings')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">System Settings</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-cogs mr-2"></i> Global Configuration</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        @php
                            // Group settings by their category/group field
                            $groupedSettings = $settings->groupBy('group');
                        @endphp

                        @forelse($groupedSettings as $group => $groupSettings)
                            <h5 class="font-weight-bold text-secondary mt-4 mb-3 border-bottom pb-2">{{ ucfirst($group) }} Settings</h5>
                            
                            @foreach($groupSettings as $setting)
                                <div class="form-group row align-items-center mb-3">
                                    <label class="col-sm-4 col-form-label font-weight-bold text-right">{{ $setting->display_name ?: ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                                    <div class="col-sm-8">
                                        @if($setting->type == 'textarea')
                                            <textarea name="{{ $setting->key }}" class="form-control">{{ $setting->value }}</textarea>
                                        @elseif($setting->type == 'boolean')
                                            <select name="{{ $setting->key }}" class="form-control">
                                                <option value="1" {{ $setting->value == '1' ? 'selected' : '' }}>Enabled</option>
                                                <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>Disabled</option>
                                            </select>
                                        @else
                                            <input type="{{ $setting->type ?: 'text' }}" name="{{ $setting->key }}" class="form-control" value="{{ $setting->value }}">
                                        @endif
                                        @if($setting->description)
                                            <small class="form-text text-muted">{{ $setting->description }}</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @empty
                            <div class="alert alert-warning text-center">
                                No system settings found in the database. Run migrations and seeders.
                            </div>
                        @endforelse

                        <hr>
                        <div class="form-group row mt-4">
                            <div class="col-sm-8 offset-sm-4">
                                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save"></i> Save Settings</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-info-circle mr-2"></i> About Settings</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">These settings control global application behavior. Changes here affect all users.</p>
                    <ul class="small text-muted pl-3">
                        <li><strong>General:</strong> Company details and branding.</li>
                        <li><strong>Finance:</strong> Default tax rates and currencies.</li>
                        <li><strong>System:</strong> Email configurations and feature toggles.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
