<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractAmendment extends Model
{
    protected $fillable = [
        'employee_contract_id',
        'amendment_title',
        'changes_description',
        'effective_date',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'approval_notes',
        'amendment_document',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(EmployeeContract::class, 'employee_contract_id');
    }

    public function requestedByUser()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
