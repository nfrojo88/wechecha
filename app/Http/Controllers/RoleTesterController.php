<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RoleTesterController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('dev.roles', compact('roles'));
    }

    public function loginAsRole(Request $request)
    {
        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        $roleName = $request->role;
        
        // Find a user with this role
        $user = User::role($roleName)->first();

        if (!$user) {
            // Create a dummy user for this role if none exists
            $user = User::create([
                'name' => 'Test ' . ucfirst(str_replace('_', ' ', $roleName)),
                'email' => $roleName . '_test@example.com',
                'password' => bcrypt('password'),
            ]);
            $user->assignRole($roleName);
        }

        // Login as this user
        Auth::login($user);
        
        $request->session()->regenerate();

        // Redirect to their dashboard
        return redirect()->route('dashboard');
    }
}
