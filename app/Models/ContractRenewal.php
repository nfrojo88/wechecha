<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractRenewal extends Model
{
    protected $fillable = [
        'employee_contract_id',
        'renewal_date',
        'new_end_date',
        'new_salary',
        'renewal_terms',
        'status',
        'proposed_by',
        'approved_by',
        'approved_at',
        'approval_notes',
    ];

    protected $casts = [
        'renewal_date' => 'date',
        'new_end_date' => 'date',
        'new_salary' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(EmployeeContract::class, 'employee_contract_id');
    }

    public function proposedByUser()
    {
        return $this->belongsTo(User::class, 'proposed_by');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getSalaryIncreaseAttribute()
    {
        if ($this->new_salary && $this->contract->salary) {
            return $this->new_salary - $this->contract->salary;
        }
        return 0;
    }

    public function getSalaryIncreasePercentageAttribute()
    {
        if ($this->contract->salary == 0) return 0;
        return (($this->new_salary - $this->contract->salary) / $this->contract->salary) * 100;
    }
}
