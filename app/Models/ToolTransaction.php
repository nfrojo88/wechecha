<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'equipment_id',
        'foreman_id',
        'issued_by',
        'received_by',
        'checkout_time',
        'checkin_time',
        'status',
        'checkout_notes',
        'checkin_notes',
    ];

    protected $casts = [
        'checkout_time' => 'datetime',
        'checkin_time' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function equipment()
    {
        return $this->belongsTo(EquipmentMaster::class, 'equipment_id');
    }

    public function foreman()
    {
        return $this->belongsTo(User::class, 'foreman_id');
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
