<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ManpowerForecast;

class ManpowerForecastPolicy
{
    /**
     * Determine whether the user can view any manpower forecasts.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['planning_manager', 'hr_manager', 'admin']);
    }

    /**
     * Determine whether the user can view the manpower forecast.
     */
    public function view(User $user, ManpowerForecast $forecast): bool
    {
        return $user->hasRole(['planning_manager', 'hr_manager', 'admin']);
    }

    /**
     * Determine whether the user can create a manpower forecast.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['planning_manager', 'admin']);
    }

    /**
     * Determine whether the user can update the manpower forecast.
     */
    public function update(User $user, ManpowerForecast $forecast): bool
    {
        return $user->hasRole(['planning_manager', 'admin']) && $forecast->status === 'draft';
    }

    /**
     * Determine whether the user can approve the manpower forecast.
     */
    public function approve(User $user): bool
    {
        return $user->hasRole(['hr_manager', 'admin']);
    }
}
