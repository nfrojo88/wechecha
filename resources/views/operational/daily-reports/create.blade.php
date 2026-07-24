@extends('layouts.app')
@section('title', 'Log Daily Report')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">New Daily Report</h1>
        <a href="{{ route('daily-reports.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
        </a>
    </div>

    <form action="{{ route('daily-reports.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Report Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label>Project <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-control" required>
                                <option value="">-- Select Project --</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Report Date <span class="text-danger">*</span></label>
                            <input type="date" name="report_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <hr>
                        <div class="form-group mb-3">
                            <label>Weather Conditions</label>
                            <select name="weather_conditions" class="form-control">
                                <option value="Sunny">Sunny</option>
                                <option value="Cloudy">Cloudy</option>
                                <option value="Rainy">Rainy</option>
                                <option value="Stormy">Stormy</option>
                                <option value="Snow">Snow</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Temperature (°C)</label>
                            <input type="number" name="temperature" class="form-control">
                        </div>
                        <hr>
                        <div class="form-group mb-3">
                            <label>Total Manpower on Site <span class="text-danger">*</span></label>
                            <input type="number" name="total_manpower" class="form-control" min="0" value="0" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Tasks Performed</h6>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addTaskRow()">
                            <i class="fas fa-plus fa-sm"></i> Add Task
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0" id="tasksTable">
                            <thead>
                                <tr>
                                    <th>Work Description <span class="text-danger">*</span></th>
                                    <th width="12%">Qty Done</th>
                                    <th width="12%">Workers</th>
                                    <th width="20%">Equipment Used</th>
                                    <th width="20%">Issues/Notes</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" name="items[0][work_description]" class="form-control form-control-sm" required></td>
                                    <td><input type="number" name="items[0][qty_completed]" class="form-control form-control-sm" step="0.01"></td>
                                    <td><input type="number" name="items[0][workers_count]" class="form-control form-control-sm"></td>
                                    <td><input type="text" name="items[0][equipment_used]" class="form-control form-control-sm"></td>
                                    <td><input type="text" name="items[0][issues]" class="form-control form-control-sm"></td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">General Comments</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label>General Notes & Progress</label>
                            <textarea name="general_notes" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Safety Incidents (if any)</label>
                            <textarea name="safety_incidents" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Site Diary Remark</label>
                            <textarea name="site_diary_remark" class="form-control" rows="2" placeholder="Write diary remarks..."></textarea>
                        </div>
                        <div class="form-group mb-0">
                            <label>Site Book Picture (Optional)</label>
                            <input type="file" name="site_book_pic" class="form-control-file" accept="image/*">
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Submit Daily Report</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    let taskIndex = 1;
    function addTaskRow() {
        const tbody = document.querySelector('#tasksTable tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="items[${taskIndex}][work_description]" class="form-control form-control-sm" required></td>
            <td><input type="number" name="items[${taskIndex}][qty_completed]" class="form-control form-control-sm" step="0.01"></td>
            <td><input type="number" name="items[${taskIndex}][workers_count]" class="form-control form-control-sm"></td>
            <td><input type="text" name="items[${taskIndex}][equipment_used]" class="form-control form-control-sm"></td>
            <td><input type="text" name="items[${taskIndex}][issues]" class="form-control form-control-sm"></td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
        taskIndex++;
    }
</script>
@endsection
