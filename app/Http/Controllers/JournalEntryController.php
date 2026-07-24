<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class JournalEntryController extends Controller
{
    public function index()
    {
        $entries = JournalEntry::with('createdBy')->latest('entry_date')->paginate(20);
        return view('finance.journal-entries.index', compact('entries'));
    }

    public function create()
    {
        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();
        return view('finance.journal-entries.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entry_date'        => 'required|date',
            'reference'         => 'nullable|string|max:100',
            'description'       => 'nullable|string',
            'lines'             => 'required|array|min:2',
            'lines.*.coa_id'    => 'required|exists:chart_of_accounts,id',
            'lines.*.debit'     => 'required|numeric|min:0',
            'lines.*.credit'    => 'required|numeric|min:0',
        ]);

        $totalDebit = collect($request->lines)->sum('debit');
        $totalCredit = collect($request->lines)->sum('credit');

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            return back()->withInput()->withErrors(['lines' => 'Total Debits must equal Total Credits.']);
        }

        DB::transaction(function () use ($request) {
            $je = JournalEntry::create([
                'entry_no'    => 'JE-' . date('Ymd') . '-' . str_pad(JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT),
                'entry_date'  => $request->entry_date,
                'reference'   => $request->reference,
                'description' => $request->description,
                'created_by'  => Auth::id(),
                'status'      => 'posted',
            ]);

            foreach ($request->lines as $line) {
                if ($line['debit'] > 0 || $line['credit'] > 0) {
                    $je->lines()->create([
                        'coa_id'      => $line['coa_id'],
                        'debit'       => $line['debit'],
                        'credit'      => $line['credit'],
                        'description' => $line['description'] ?? null,
                    ]);

                    // Update COA balance (simplified logic: debit adds to asset/expense, subtracts from liab/eq/rev)
                    $coa = ChartOfAccount::find($line['coa_id']);
                    $amount = $line['debit'] - $line['credit'];
                    if (in_array($coa->type, ['liability', 'equity', 'revenue'])) {
                        $amount = -$amount;
                    }
                    $coa->increment('current_balance', $amount);
                }
            }
        });

        return redirect()->route('journal-entries.index')->with('success', 'Journal Entry posted.');
    }

    public function show(JournalEntry $journalEntry)
    {
        $journalEntry->load(['lines.coa', 'createdBy']);
        return view('finance.journal-entries.show', compact('journalEntry'));
    }
}
