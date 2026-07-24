@extends('layouts.app')
@section('title', 'Submit Support Ticket')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fa-solid fa-plus-circle me-2"></i>Submit Support Ticket</h1>
        <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to My Tickets</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Describe Your Issue or Request</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('tickets.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                    <option value="">Select a Category...</option>
                                    <option value="system_bug" {{ old('category') == 'system_bug' ? 'selected' : '' }}>System Bug / Error</option>
                                    <option value="feature_request" {{ old('category') == 'feature_request' ? 'selected' : '' }}>Feature Request</option>
                                    <option value="access_issue" {{ old('category') == 'access_issue' ? 'selected' : '' }}>Access or Permission Issue</option>
                                    <option value="account_help" {{ old('category') == 'account_help' ? 'selected' : '' }}>Account / Profile Help</option>
                                    <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other Query</option>
                                </select>
                                @error('category') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Priority <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low - No rush</option>
                                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium - Normal</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High - Needs attention</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent - Blocking work</option>
                                </select>
                                @error('priority') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}" placeholder="Brief summary of the issue" required>
                            @error('subject') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-weight-bold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" rows="6" class="form-control @error('description') is-invalid @enderror" placeholder="Provide as much detail as possible to help us assist you..." required>{{ old('description') }}</textarea>
                            @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('tickets.index') }}" class="btn btn-light me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane me-1"></i> Submit Ticket</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
