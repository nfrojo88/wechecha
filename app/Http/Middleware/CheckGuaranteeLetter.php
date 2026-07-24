<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckGuaranteeLetter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // Check if user has an employee record
        if ($user && $user->employee) {
            $employee = $user->employee;
            
            // Check if guarantee letter is overdue (30+ days without submission)
            if ($employee->is_guarantee_overdue) {
                Auth::logout();
                
                return redirect()->route('login')->withErrors([
                    'email' => 'Your account has been temporarily blocked. Please submit your guarantee letter to HR. It has been over 30 days since your joining date.'
                ]);
            }
        }
        
        return $next($request);
    }
}
