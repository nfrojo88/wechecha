@extends('layouts.app')
@section('title', 'New Manpower Request')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-users-cog me-2"></i>New Manpower Request</h1>
        <a href="{{ route('manpower-requests.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </div>
    <form action="{{ route('manpower-requests.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-8">
                <div class="card shadow-sm mb-3">
                    <div class="card-header fw-semibold">Request Details</div>
                    <div class="card-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Project *</label>
                            <select name="project_id" class="form-select" required>
                                <option value="">-- Select Project --</option>
                                @foreach($projects as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Required Date *</label>
                            <input type="date" name="required_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type *</label>
                            <select name="type" class="form-select" required>
                                <option value="new_hire">New Hire</option>
                                <option value="replacement">Replacement</option>
                                <option value="temporary">Temporary</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Requirements</label>
                            <textarea name="requirements" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between fw-semibold">
                        <span>Roles Needed</span>
                        <button type="button" class="btn btn-sm btn-success" id="addRole"><i class="fas fa-plus"></i> Add Role</button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table align-middle mb-0">
                            <thead class="table-light"><tr><th>Role Title</th><th>Quantity</th><th>Skill Level</th><th></th></tr></thead>
                            <tbody id="rolesBody">
                                <tr>
                                    <td><input type="text" name="items[0][role_title]" class="form-control" required></td>
                                    <td><input type="number" name="items[0][quantity]" class="form-control" min="1" required></td>
                                    <td>
                                        <select name="items[0][skill_level]" class="form-select" required>
                                            <option value="unskilled">Unskilled</option>
                                            <option value="semi_skilled">Semi-skilled</option>
                                            <option value="skilled">Skilled</option>
                                            <option value="professional">Professional</option>
                                        </select>
                                    </td>
                                    <td><button type="button" class="btn btn-sm btn-outline-danger rm"><i class="fas fa-times"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body"><button type="submit" class="btn btn-primary w-100">Submit Request</button></div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    let ridx=1; 
    document.getElementById('addRole').onclick=function(){
        document.getElementById('rolesBody').insertAdjacentHTML('beforeend', `<tr><td><input type="text" name="items[${ridx}][role_title]" class="form-control" required></td><td><input type="number" name="items[${ridx}][quantity]" class="form-control" min="1" required></td><td><select name="items[${ridx}][skill_level]" class="form-select" required><option value="unskilled">Unskilled</option><option value="semi_skilled">Semi-skilled</option><option value="skilled">Skilled</option><option value="professional">Professional</option></select></td><td><button type="button" class="btn btn-sm btn-outline-danger rm"><i class="fas fa-times"></i></button></td></tr>`);
        ridx++;
    };
    document.addEventListener('click', e=>{if(e.target.closest('.rm')) e.target.closest('tr').remove();});
</script>
@endsection
