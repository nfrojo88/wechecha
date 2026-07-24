<?php

namespace App\Http\Controllers;

use App\Models\ManpowerRole;
use Illuminate\Http\Request;

class ManpowerRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:planning_manager|planning|technical_manager|admin|global_admin');
    }

    /** Return all roles as JSON (for modals / selects) */
    public function index()
    {
        return response()->json(ManpowerRole::orderBy('name')->get());
    }

    /** Save a new role */
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100|unique:manpower_roles,name',
            'default_unit' => 'required|in:day,hr',
            'category'     => 'nullable|string|max:100',
        ]);

        $role = ManpowerRole::create($request->only('name', 'default_unit', 'category'));

        return response()->json(['success' => true, 'role' => $role]);
    }

    /** Delete a role */
    public function destroy(ManpowerRole $manpowerRole)
    {
        $manpowerRole->delete();
        return response()->json(['success' => true]);
    }
}
