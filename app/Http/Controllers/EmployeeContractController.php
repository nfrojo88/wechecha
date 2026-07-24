<?php

namespace App\Http\Controllers;

use App\Models\EmployeeContract;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeContractController extends Controller
{
    public function index()
    {
        $contracts = EmployeeContract::with('employee')->latest()->paginate(20);
        return view('hr.contracts.index', compact('contracts'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->orderBy('full_name')->get();
        return view('hr.contracts.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id'   => 'required|exists:employees,id',
            'contract_type' => 'required|in:permanent,temporary,contract,probation',
            'start_date'    => 'required|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'salary'        => 'required|numeric|min:0',
            'terms'         => 'nullable|string',
            'status'        => 'required|in:active,expired,terminated',
        ]);

        $data['created_by'] = Auth::id();

        EmployeeContract::create($data);
        return redirect()->route('contracts.index')->with('success', 'Contract saved.');
    }

    public function show(EmployeeContract $contract)
    {
        $contract->load('employee');
        return view('hr.contracts.show', compact('contract'));
    }
}
