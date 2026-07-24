<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EngWorkOrderAssignee extends Model
{
    protected $table = 'eng_work_order_assignees';
    protected $guarded = ['id'];

    protected $casts = [
        'accepted_at'   => 'datetime',
        'completed_at'  => 'datetime',
    ];

    public function workOrder()
    {
        return $this->belongsTo(EngWorkOrder::class, 'work_order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
