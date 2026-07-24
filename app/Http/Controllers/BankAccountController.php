<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $accounts = BankAccount::with('coa')->latest()->get();
        return view('finance.bank.index', compact('accounts'));
    }

    public function create()
    {
        $coas = ChartOfAccount::where('type', 'asset')->where('is_active', true)->orderBy('code')->get();
        return view('finance.bank.create', compact('coas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'account_name'   => 'required|string|max:255',
            'account_number' => 'required|string|max:50|unique:bank_accounts',
            'bank_name'      => 'required|string|max:255',
            'branch'         => 'nullable|string|max:255',
            'account_type'   => 'required|in:checking,savings,fixed_deposit',
            'currency'       => 'required|string|max:3',
            'coa_id'         => 'nullable|exists:chart_of_accounts,id',
            'is_default'     => 'boolean',
            'notes'          => 'nullable|string',
        ]);

        BankAccount::create($data);
        return redirect()->route('bank-accounts.index')->with('success', 'Bank account created.');
    }

    public function edit(BankAccount $bankAccount)
    {
        $coas = ChartOfAccount::where('type', 'asset')->where('is_active', true)->orderBy('code')->get();
        return view('finance.bank.edit', compact('bankAccount', 'coas'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $data = $request->validate([
            'account_name'   => 'required|string|max:255',
            'account_number' => 'required|string|max:50|unique:bank_accounts,account_number,' . $bankAccount->id,
            'bank_name'      => 'required|string|max:255',
            'branch'         => 'nullable|string|max:255',
            'account_type'   => 'required|in:checking,savings,fixed_deposit',
            'currency'       => 'required|string|max:3',
            'coa_id'         => 'nullable|exists:chart_of_accounts,id',
            'is_active'      => 'boolean',
            'is_default'     => 'boolean',
            'notes'          => 'nullable|string',
        ]);

        $bankAccount->update($data);
        return redirect()->route('bank-accounts.index')->with('success', 'Bank account updated.');
    }

    public function show(BankAccount $bankAccount)
    {
        $bankAccount->load('transactions');
        return view('finance.bank.show', compact('bankAccount'));
    }
}
