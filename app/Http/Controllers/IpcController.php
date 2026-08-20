<?php

namespace App\Http\Controllers;

use App\Models\IpcRecord;
use Illuminate\Http\Request;

class IpcController extends Controller
{
    public function index()
    {
        $query = IpcRecord::with(['project', 'agreement'])->latest();

        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if ($user && $user->hasRole('site_engineer') && !$user->hasAnyRole(['admin', 'global_admin', 'finance_head', 'finance'])) {
            $assignedProjectIds = $user->projects()->pluck('projects.id');
            if ($user->store && $user->store->project_id) {
                $assignedProjectIds->push($user->store->project_id);
            }
            $query->whereIn('project_id', $assignedProjectIds->unique());
        }

        $ipcs = $query->get();
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
