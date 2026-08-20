<?php

namespace App\Http\Controllers;

use App\Models\ToolTransaction;
use App\Models\Product;
use App\Models\Project;
use Illuminate\Http\Request;

class ToolTransactionController extends Controller
{
    public function index()
    {
        $transactions = ToolTransaction::with(['project', 'equipment', 'foreman', 'issuer', 'receiver'])->latest()->get();
        return view('tool-transactions.index', compact('transactions'));
    }

    public function create()
    {
        $projects = Project::where('status', 'active')->get();
        $equipments = Product::where('is_active', true)->whereIn('category', ['Fixed Asset', 'Equipment', 'Tool', 'Tools'])->get();
        return view('tool-transactions.create', compact('projects', 'equipments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'equipment_id' => 'required|exists:products,id',
            'checkout_notes' => 'nullable|string',
        ]);

        $validated['foreman_id'] = auth()->id();
        $validated['checkout_time'] = now();
        $validated['status'] = 'checked_out';

        ToolTransaction::create($validated);

        return redirect()->route('tool-transactions.index')->with('success', 'Tool checked out successfully.');
    }

    public function checkin(Request $request, ToolTransaction $toolTransaction)
    {
        $validated = $request->validate([
            'checkin_notes' => 'nullable|string',
        ]);

        $toolTransaction->update([
            'checkin_time' => now(),
            'status' => 'returned',
            'checkin_notes' => $validated['checkin_notes'],
        ]);

        return redirect()->route('tool-transactions.index')->with('success', 'Tool checked in successfully.');
    }

    public function show(ToolTransaction $toolTransaction)
    {
        $toolTransaction->load(['project', 'equipment', 'foreman', 'issuer', 'receiver']);
        return view('tool-transactions.show', compact('toolTransaction'));
    }
}
