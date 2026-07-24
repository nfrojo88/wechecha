<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'days_allowed',
        'is_paid',
        'requires_documentation',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'requires_documentation' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
