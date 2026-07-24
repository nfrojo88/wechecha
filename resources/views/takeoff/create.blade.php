@extends('layouts.app')

@section('title', 'Create Take-Off Sheet')

@push('styles')
<style>
    .form-label { font-weight: 600; font-size: 13px; color: var(--gray-700); }
    .form-control, .form-select { border-color: var(--gray-200); box-shadow: none; }
    .form-control:focus, .form-select:focus { border-color: var(--brand-500); box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            
            <div class="card-header border-bottom-0 text-white" style="background-color: #1e293b; padding: 16px 20px;">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="fa-solid fa-plus-circle me-2 text-primary"></i>Create Take-Off Sheet
                    </h5>
                    <a href="{{ route('takeoff.index') }}" class="text-white opacity-75 text-decoration-none">
                        <i class="fa-solid fa-xmark fa-lg"></i>
                    </a>
                </div>
            </div>

            <div class="card-body p-4 bg-white">
                <form action="{{ route('takeoff.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4">
                        <!-- Sheet Title -->
                        <div class="col-12">
                            <label for="title" class="form-label">Sheet Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" placeholder="e.g. Ground Floor Concrete Take-Off" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Classification & Project -->
                        <div class="col-md-6">
                            <label for="sheet_type" class="form-label">Classification <span class="text-danger">*</span></label>
                            <select name="sheet_type" id="sheet_type" class="form-select @error('sheet_type') is-invalid @enderror" required>
                                <option value="standard" {{ old('sheet_type') == 'standard' ? 'selected' : '' }}>Standard Quantity</option>
                                <option value="rebar_schedule" {{ old('sheet_type') == 'rebar_schedule' ? 'selected' : '' }}>Rebar Schedule (BBS)</option>
                            </select>
                            @error('sheet_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="project_id" class="form-label">Project Assignment <span class="text-danger">*</span></label>
                            <select name="project_id" id="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                <option value="">— Select —</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>



                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="category" class="form-label mb-0">Take-Off Category</label>
                                <a href="#" class="text-primary text-decoration-none" style="font-size: 12px; font-weight:600;">
                                    <i class="fa-solid fa-gear me-1"></i>Manage
                                </a>
                            </div>
                            <select name="category" id="category" class="form-select @error('category') is-invalid @enderror">
                                <option value="">— Select —</option>
                                <option value="Substructure" {{ old('category') == 'Substructure' ? 'selected' : '' }}>Substructure</option>
                                <option value="Superstructure" {{ old('category') == 'Superstructure' ? 'selected' : '' }}>Superstructure</option>
                                <option value="Finishing" {{ old('category') == 'Finishing' ? 'selected' : '' }}>Finishing</option>
                                <option value="MEP" {{ old('category') == 'MEP' ? 'selected' : '' }}>MEP</option>
                            </select>
                            @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Discipline & Ref Drawing -->
                        <div class="col-md-6">
                            <label for="discipline" class="form-label">Discipline</label>
                            <select name="discipline" id="discipline" class="form-select @error('discipline') is-invalid @enderror">
                                <option value="">— Select —</option>
                                <option value="Architectural" {{ old('discipline') == 'Architectural' ? 'selected' : '' }}>Architectural</option>
                                <option value="Structural" {{ old('discipline') == 'Structural' ? 'selected' : '' }}>Structural</option>
                                <option value="Electrical" {{ old('discipline') == 'Electrical' ? 'selected' : '' }}>Electrical</option>
                                <option value="Mechanical" {{ old('discipline') == 'Mechanical' ? 'selected' : '' }}>Mechanical</option>
                            </select>
                            @error('discipline') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="ref_drawing" class="form-label">Ref Drawing</label>
                            <input type="text" name="ref_drawing" id="ref_drawing" class="form-control @error('ref_drawing') is-invalid @enderror" 
                                   value="{{ old('ref_drawing') }}" placeholder="e.g. DWG-ST-001">
                            @error('ref_drawing') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Execution Type & Measurement Std -->
                        <div class="col-md-6">
                            <label for="execution_type" class="form-label text-primary">Execution Type</label>
                            <select name="execution_type" id="execution_type" class="form-select @error('execution_type') is-invalid @enderror">
                                <option value="">— Select Execution Type —</option>
                                <option value="sub_con_with_material" {{ old('execution_type') == 'sub_con_with_material' ? 'selected' : '' }}>Sub Con Work With Material</option>
                                <option value="sub_con_without_material" {{ old('execution_type') == 'sub_con_without_material' ? 'selected' : '' }}>Sub Con Without Material</option>
                                <option value="company_work" {{ old('execution_type') == 'company_work' ? 'selected' : '' }}>Our Company Work</option>
                            </select>
                            @error('execution_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="measurement_std" class="form-label">Measurement Std</label>
                            <select name="measurement_std" id="measurement_std" class="form-select @error('measurement_std') is-invalid @enderror">
                                <option value="IS-1200 (Standard)">IS-1200 (Standard)</option>
                            </select>
                            @error('measurement_std') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label for="description" class="form-label">Notes</label>
                            <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" style="background-color:#0d6efd;">
                            Launch Sheet <i class="fa-solid fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
