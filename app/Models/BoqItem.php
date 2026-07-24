<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoqItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'boq_id',
        'item_code',
        'description',
        'unit',
        'tender_quantity',
        'quantity',
        'unit_rate',
        'product_id',
        'schedule_task_id',
        'takeoff_item_id',
    ];

    protected $casts = [
        'tender_quantity' => 'decimal:3',
        'quantity' => 'decimal:3',
        'unit_rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function boq()
    {
        return $this->belongsTo(Boq::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scheduleTask()
    {
        return $this->belongsTo(ScheduleTask::class);
    }

    public function takeoffItem()
    {
        return $this->belongsTo(TakeoffItem::class);
    }
}
