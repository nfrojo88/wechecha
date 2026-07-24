<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EngWorkOrderComment extends Model
{
    protected $table = 'eng_work_order_comments';
    protected $guarded = ['id'];

    public function workOrder()
    {
        return $this->belongsTo(EngWorkOrder::class, 'work_order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
