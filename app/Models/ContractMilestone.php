<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractMilestone extends Model
{
    protected $fillable = [
        'employee_contract_id',
        'milestone_name',
        'milestone_date',
        'description',
        'status',
    ];

    protected $casts = [
        'milestone_date' => 'date',
    ];

    public function contract()
    {
        return $this->belongsTo(EmployeeContract::class, 'employee_contract_id');
    }

    public function isUpcoming()
    {
        return $this->milestone_date > now() && $this->status === 'pending';
    }

    public function isOverdue()
    {
        return $this->milestone_date < now() && $this->status === 'pending';
    }
}
