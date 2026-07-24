<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollSummary extends Model
{
    protected $fillable = [
        'year',
        'month',
        'total_employees',
        'total_gross',
        'total_allowances',
        'total_deductions',
        'total_taxes',
        'total_net',
        'processed_count',
        'paid_count',
        'status',
        'created_by',
        'finalized_at',
    ];

    protected $casts = [
        'total_gross' => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_taxes' => 'decimal:2',
        'total_net' => 'decimal:2',
        'finalized_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getProcessingPercentageAttribute()
    {
        if ($this->total_employees == 0) return 0;
        return ($this->processed_count / $this->total_employees) * 100;
    }

    public function getPaidPercentageAttribute()
    {
        if ($this->total_employees == 0) return 0;
        return ($this->paid_count / $this->total_employees) * 100;
    }
}
