@extends('layouts.app')
@section('title', 'Work Standards')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">
                <i class="fa-solid fa-ruler-combined text-warning me-2"></i>Work Standards
            </h1>
            <p class="text-muted mb-0 small">Productivity benchmarks for material, manpower & equipment</p>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-pills mb-4 gap-2">
        <li class="nav-item">
            <a class="nav-link active px-4" data-bs-toggle="pill" href="#materialStd">
                <i class="fa-solid fa-boxes-stacked me-2"></i>Material Standards
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-4" data-bs-toggle="pill" href="#manpowerStd">
                <i class="fa-solid fa-hard-hat me-2"></i>Manpower Productivity
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-4" data-bs-toggle="pill" href="#equipmentStd">
                <i class="fa-solid fa-tractor me-2"></i>Equipment Productivity
            </a>
        </li>
    </ul>

    <div class="tab-content">
        {{-- Material Standards --}}
        <div class="tab-pane fade show active" id="materialStd">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning bg-opacity-10 border-0">
                    <h6 class="fw-bold mb-0 text-warning">
                        <i class="fa-solid fa-boxes-stacked me-2"></i>Material Consumption Standards
                    </h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Material Item</th>
                                <th>Unit of Measure</th>
                                <th>Standard Qty</th>
                                <th>Max Wastage</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($standards['material'] as $i => $row)
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $row['item'] }}</td>
                                <td><span class="badge bg-secondary">{{ $row['unit'] }}</span></td>
                                <td>
                                    <span class="fw-bold text-primary">{{ $row['standard'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                        {{ $row['wastage_limit'] }}
                                    </span>
                                </td>
                                <td><span class="badge bg-success">Active</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Manpower Standards --}}
        <div class="tab-pane fade" id="manpowerStd">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary bg-opacity-10 border-0">
                    <h6 class="fw-bold mb-0 text-primary">
                        <i class="fa-solid fa-hard-hat me-2"></i>Manpower Productivity Rates
                    </h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Trade / Activity</th>
                                <th>Productivity Rate</th>
                                <th>Standard Output</th>
                                <th>Crew Composition</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($standards['manpower'] as $i => $row)
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $row['trade'] }}</td>
                                <td><span class="badge bg-secondary">{{ $row['unit'] }}</span></td>
                                <td><span class="fw-bold text-primary">{{ $row['standard'] }}</span></td>
                                <td class="text-muted small">{{ $row['description'] }}</td>
                                <td><span class="badge bg-success">Active</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Equipment Standards --}}
        <div class="tab-pane fade" id="equipmentStd">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success bg-opacity-10 border-0">
                    <h6 class="fw-bold mb-0 text-success">
                        <i class="fa-solid fa-tractor me-2"></i>Equipment Productivity Rates
                    </h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Equipment Type</th>
                                <th>Output Rate</th>
                                <th>Standard</th>
                                <th>Fuel Rate</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($standards['equipment'] as $i => $row)
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $row['machine'] }}</td>
                                <td><span class="badge bg-secondary">{{ $row['unit'] }}</span></td>
                                <td><span class="fw-bold text-primary">{{ $row['standard'] }}</span></td>
                                <td class="text-muted small">{{ $row['fuel_rate'] }}</td>
                                <td><span class="badge bg-success">Active</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
