<?php

namespace App\Http\Controllers;

use App\Models\SubconAgreement;
use Illuminate\Http\Request;

class SubcontractorController extends Controller
{
    public function index()
    {
        $agreements = SubconAgreement::with('project')->latest()->get();
        return view('finance.subcontractors.index', compact('agreements'));
    }

    public function create()
    {
        return view('finance.subcontractors.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('subcontractors.index')->with('success', 'Subcontractor agreement created.');
    }

    public function show(SubconAgreement $subcontractor)
    {
        $subcontractor->load(['project', 'items.boqItem', 'ipcs']);
        return view('finance.subcontractors.show', compact('subcontractor'));
    }
}
