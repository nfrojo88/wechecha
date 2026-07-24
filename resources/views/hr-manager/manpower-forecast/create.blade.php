@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Create Manpower Forecast
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('manpower-forecast.store') }}">
                        @csrf

                        <!-- Project Selection -->
                        <div class="mb-3">
                            <label for="project_id" class="form-label">Project *</label>
                            <select name="project_id" id="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                <option value="">Select Project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Week Starting -->
                        <div class="mb-3">
                            <label for="week_starting" class="form-label">Week Starting (Monday) *</label>
                            <input type="date" name="week_starting" id="week_starting" 
                                   class="form-control @error('week_starting') is-invalid @enderror" 
                                   value="{{ old('week_starting') }}" 
                                   min="{{ date('Y-m-d') }}" required>
                            <small class="text-muted">Select the Monday of the week</small>
                            @error('week_starting')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Designation -->
                        <div class="mb-3">
                            <label for="designation_id" class="form-label">Designation *</label>
                            <select name="designation_id" id="designation_id" class="form-select @error('designation_id') is-invalid @enderror" required>
                                <option value="">Select Designation</option>
                                @foreach ($designations as $designation)
                                    <option value="{{ $designation->id }}" {{ old('designation_id') == $designation->id ? 'selected' : '' }}>
                                        {{ $designation->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('designation_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Forecasted Headcount -->
                            <div class="col-md-6 mb-3">
                                <label for="forecasted_headcount" class="form-label">Forecasted Headcount *</label>
                                <input type="number" name="forecasted_headcount" id="forecasted_headcount" 
                                       class="form-control @error('forecasted_headcount') is-invalid @enderror" 
                                       step="0.5" min="1" value="{{ old('forecasted_headcount') }}" required>
                                @error('forecasted_headcount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Forecasted Hours -->
                            <div class="col-md-6 mb-3">
                                <label for="forecasted_hours" class="form-label">Forecasted Hours *</label>
                                <input type="number" name="forecasted_hours" id="forecasted_hours" 
                                       class="form-control @error('forecasted_hours') is-invalid @enderror" 
                                       step="0.5" min="1" value="{{ old('forecasted_hours') }}" required>
                                <small class="text-muted">Total hours for the week</small>
                                @error('forecasted_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" 
                                      rows="3" placeholder="Any special requirements or notes...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Forecast
                            </button>
                            <a href="{{ route('manpower-forecast.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('week_starting').addEventListener('change', function() {
    const date = new Date(this.value);
    const dayOfWeek = date.getDay();
    
    if (dayOfWeek !== 1) {
        const diff = 1 - dayOfWeek;
        const monday = new Date(date.setDate(date.getDate() + diff));
        const year = monday.getFullYear();
        const month = String(monday.getMonth() + 1).padStart(2, '0');
        const day = String(monday.getDate()).padStart(2, '0');
        this.value = `${year}-${month}-${day}`;
    }
});
</script>
@endsection
