<?php

namespace App\Http\Controllers;

use App\Models\IncomeRecord;
use App\Models\Project;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    public function index()
    {
        $incomes = IncomeRecord::with(['project', 'createdBy'])->latest()->paginate(20);
        
        $totalRevenue = IncomeRecord::where('status', 'confirmed')->sum('amount');
        // Total balance is usually total income minus total expenses, but for this view we'll just show bank account total
        $totalBalance = BankAccount::where('is_active', true)->sum('current_balance') ?? 0;
        
        $transportationTotal = IncomeRecord::where('category', 'transportation')->where('status', 'confirmed')->sum('amount');
        $rentalTotal = IncomeRecord::where('category', 'rental')->where('status', 'confirmed')->sum('amount');
        $constructionTotal = IncomeRecord::where('category', 'construction')->where('status', 'confirmed')->sum('amount');
        
        $projects = Project::where('status', 'active')->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();

        return view('finance.income.index', compact('incomes', 'totalRevenue', 'totalBalance', 'transportationTotal', 'rentalTotal', 'constructionTotal', 'projects', 'bankAccounts'));
    }

    public function create()
    {
        $projects     = Project::where('status', 'active')->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();
        return view('finance.income.create', compact('projects', 'bankAccounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'      => 'nullable|exists:projects,id',
            'category'        => 'required|in:transportation,rental,construction,other',
            'income_date'     => 'required|date',
            'amount'          => 'required|numeric|min:0.01',
            'payment_method'  => 'required|string|max:20',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'description'     => 'nullable|string',
            'notes'           => 'nullable|string',
        ]);

        $no = 'INC-' . date('Ymd') . '-' . str_pad(IncomeRecord::count() + 1, 4, '0', STR_PAD_LEFT);
        $data['income_no']  = $no;
        $data['created_by'] = Auth::id();
        $data['status']     = 'draft';

        IncomeRecord::create($data);
        return redirect()->route('income.index')->with('success', 'Income record created.');
    }

    public function show(IncomeRecord $income)
    {
        return view('finance.income.show', compact('income'));
    }

    public function confirm(IncomeRecord $income)
    {
        $income->update(['status' => 'confirmed']);
        return back()->with('success', 'Income confirmed.');
    }
}
