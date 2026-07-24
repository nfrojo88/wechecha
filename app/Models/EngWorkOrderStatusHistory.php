<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EngWorkOrderStatusHistory extends Model
{
    protected $table = 'eng_work_order_status_history';
    protected $guarded = ['id'];

    public function workOrder()
    {
        return $this->belongsTo(EngWorkOrder::class, 'work_order_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
