<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ResourceAvailability extends Model
{
    protected $fillable = [
        'employee_id',
        'available_from',
        'available_until',
        'available_hours_per_week',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'available_from' => 'date',
        'available_until' => 'date',
        'available_hours_per_week' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function isCurrentlyAvailable()
    {
        $today = Carbon::now()->toDateString();
        return $this->is_active 
            && $this->available_from <= $today 
            && $this->available_until >= $today;
    }

    public function getDaysAvailableAttribute()
    {
        return $this->available_from->diffInDays($this->available_until) + 1;
    }
}
