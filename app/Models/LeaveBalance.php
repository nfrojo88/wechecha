<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'year',
        'total_days',
        'used_days',
        'remaining_days',
    ];

    protected $casts = [
        'total_days' => 'decimal:2',
        'used_days' => 'decimal:2',
        'remaining_days' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function updateBalance($daysTaken)
    {
        $this->used_days += $daysTaken;
        $this->remaining_days = $this->total_days - $this->used_days;
        $this->save();
    }

    public function hasEnoughBalance($daysRequired)
    {
        return $this->remaining_days >= $daysRequired;
    }
}
