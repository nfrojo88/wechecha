<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedAssetAssignment extends Model
{
    use HasFactory;

    protected $table = 'fixed_asset_assignments';

    protected $fillable = [
        'fixed_asset_unit_id',
        'employee_id',
        'action',
        'assigned_date',
        'returned_date',
        'condition_on_assignment',
        'condition_on_return',
        'assigned_by',
        'received_by',
        'notes',
    ];

    protected $casts = [
        'assigned_date' => 'datetime',
        'returned_date' => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    public function unit()
    {
        return $this->belongsTo(FixedAssetUnit::class, 'fixed_asset_unit_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
