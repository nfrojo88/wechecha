<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryStructure extends Model
{
    protected $fillable = [
        'employee_id',
        'base_salary',
        'house_allowance',
        'transport_allowance',
        'meal_allowance',
        'other_allowance',
        'gross_salary',
        'effective_from',
        'effective_to',
        'is_active',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'base_salary' => 'decimal:2',
        'house_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'other_allowance' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getTotalAllowancesAttribute()
    {
        return ($this->house_allowance ?? 0) + ($this->transport_allowance ?? 0) + 
               ($this->meal_allowance ?? 0) + ($this->other_allowance ?? 0);
    }
}
