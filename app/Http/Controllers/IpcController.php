<?php

namespace App\Http\Controllers;

use App\Models\IpcRecord;
use Illuminate\Http\Request;

class IpcController extends Controller
{
    public function index()
    {
        $ipcs = IpcRecord::with(['project', 'agreement'])->latest()->get();
        return view('finance.ipcs.index', compact('ipcs'));
    }

    public function create()
    {
        return view('finance.ipcs.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('ipcs.index')->with('success', 'IPC created.');
    }

    public function show(IpcRecord $ipc)
    {
        $ipc->load(['project', 'agreement', 'items.agreementItem', 'createdBy']);
        return view('finance.ipcs.show', compact('ipc'));
    }
}
