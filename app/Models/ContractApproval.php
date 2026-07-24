<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractApproval extends Model
{
    protected $fillable = [
        'employee_contract_id',
        'approver_id',
        'approval_level',
        'status',
        'comments',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(EmployeeContract::class, 'employee_contract_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function getLevelNameAttribute()
    {
        return match($this->approval_level) {
            1 => 'Manager',
            2 => 'HR Department',
            3 => 'Finance Department',
            4 => 'Legal Department',
            default => 'Level ' . $this->approval_level
        };
    }
}
