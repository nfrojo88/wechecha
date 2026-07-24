@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2"></i>Create Performance Review
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('performance-dashboard.store-review') }}">
                        @csrf

                        <!-- Employee Selection -->
                        <div class="mb-4">
                            <label for="employee_id" class="form-label">Employee *</label>
                            <select name="employee_id" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                                <option value="">Select Employee</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->name }} ({{ $emp->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Review Period -->
                        <div class="mb-4">
                            <label for="review_period" class="form-label">Review Period (End Date) *</label>
                            <input type="date" name="review_period" id="review_period" 
                                   class="form-control @error('review_period') is-invalid @enderror" 
                                   value="{{ old('review_period') }}" required>
                            @error('review_period')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Performance Scores -->
                        <div class="mb-4">
                            <h6 class="mb-3">Performance Scores (1-5 scale)</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="technical_skills_score" class="form-label">Technical Skills *</label>
                                    <div class="input-group">
                                        <input type="number" name="technical_skills_score" id="technical_skills_score" 
                                               class="form-control @error('technical_skills_score') is-invalid @enderror" 
                                               step="0.1" min="1" max="5" value="{{ old('technical_skills_score') }}" required>
                                        <span class="input-group-text">/5</span>
                                    </div>
                                    @error('technical_skills_score')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="soft_skills_score" class="form-label">Soft Skills *</label>
                                    <div class="input-group">
                                        <input type="number" name="soft_skills_score" id="soft_skills_score" 
                                               class="form-control @error('soft_skills_score') is-invalid @enderror" 
                                               step="0.1" min="1" max="5" value="{{ old('soft_skills_score') }}" required>
                                        <span class="input-group-text">/5</span>
                                    </div>
                                    @error('soft_skills_score')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="attendance_score" class="form-label">Attendance *</label>
                                    <div class="input-group">
                                        <input type="number" name="attendance_score" id="attendance_score" 
                                               class="form-control @error('attendance_score') is-invalid @enderror" 
                                               step="0.1" min="1" max="5" value="{{ old('attendance_score') }}" required>
                                        <span class="input-group-text">/5</span>
                                    </div>
                                    @error('attendance_score')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="productivity_score" class="form-label">Productivity *</label>
                                    <div class="input-group">
                                        <input type="number" name="productivity_score" id="productivity_score" 
                                               class="form-control @error('productivity_score') is-invalid @enderror" 
                                               step="0.1" min="1" max="5" value="{{ old('productivity_score') }}" required>
                                        <span class="input-group-text">/5</span>
                                    </div>
                                    @error('productivity_score')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="communication_score" class="form-label">Communication *</label>
                                    <div class="input-group">
                                        <input type="number" name="communication_score" id="communication_score" 
                                               class="form-control @error('communication_score') is-invalid @enderror" 
                                               step="0.1" min="1" max="5" value="{{ old('communication_score') }}" required>
                                        <span class="input-group-text">/5</span>
                                    </div>
                                    @error('communication_score')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="teamwork_score" class="form-label">Teamwork *</label>
                                    <div class="input-group">
                                        <input type="number" name="teamwork_score" id="teamwork_score" 
                                               class="form-control @error('teamwork_score') is-invalid @enderror" 
                                               step="0.1" min="1" max="5" value="{{ old('teamwork_score') }}" required>
                                        <span class="input-group-text">/5</span>
                                    </div>
                                    @error('teamwork_score')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Text Fields -->
                        <div class="mb-3">
                            <label for="comments" class="form-label">Comments</label>
                            <textarea name="comments" id="comments" class="form-control @error('comments') is-invalid @enderror" 
                                      rows="3" placeholder="General comments about the employee's performance">{{ old('comments') }}</textarea>
                            @error('comments')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="strengths" class="form-label">Strengths</label>
                            <textarea name="strengths" id="strengths" class="form-control @error('strengths') is-invalid @enderror" 
                                      rows="2" placeholder="Key strengths of the employee">{{ old('strengths') }}</textarea>
                            @error('strengths')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="areas_for_improvement" class="form-label">Areas for Improvement</label>
                            <textarea name="areas_for_improvement" id="areas_for_improvement" class="form-control @error('areas_for_improvement') is-invalid @enderror" 
                                      rows="2" placeholder="Areas where employee can improve">{{ old('areas_for_improvement') }}</textarea>
                            @error('areas_for_improvement')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="development_plan" class="form-label">Development Plan</label>
                            <textarea name="development_plan" id="development_plan" class="form-control @error('development_plan') is-invalid @enderror" 
                                      rows="3" placeholder="Suggested development activities and goals">{{ old('development_plan') }}</textarea>
                            @error('development_plan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Review
                            </button>
                            <a href="{{ route('performance-dashboard.index') }}" class="btn btn-outline-secondary">
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
// Auto-calculate overall score when any score changes
function calculateOverallScore() {
    const scores = [
        parseFloat(document.getElementById('technical_skills_score').value) || 0,
        parseFloat(document.getElementById('soft_skills_score').value) || 0,
        parseFloat(document.getElementById('attendance_score').value) || 0,
        parseFloat(document.getElementById('productivity_score').value) || 0,
        parseFloat(document.getElementById('communication_score').value) || 0,
        parseFloat(document.getElementById('teamwork_score').value) || 0,
    ];
    const average = scores.reduce((a, b) => a + b, 0) / scores.length;
    console.log('Overall Score:', average.toFixed(2));
}

document.querySelectorAll('input[type="number"]').forEach(input => {
    input.addEventListener('change', calculateOverallScore);
});
</script>
@endsection
