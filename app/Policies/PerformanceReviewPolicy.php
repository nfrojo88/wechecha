<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PerformanceReview;

class PerformanceReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['hr_manager', 'hr_officer', 'admin']);
    }

    public function view(User $user, PerformanceReview $review): bool
    {
        return $user->hasRole(['hr_manager', 'hr_officer', 'admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['hr_manager', 'admin']);
    }

    public function update(User $user, PerformanceReview $review): bool
    {
        return $user->hasRole(['hr_manager', 'admin']) && $review->status === 'draft';
    }

    public function approve(User $user): bool
    {
        return $user->hasRole(['hr_manager', 'admin']);
    }
}
