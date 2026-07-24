<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleBaseline extends Model
{
    use HasFactory;

    protected $fillable = ['schedule_id', 'version_name', 'snapshot_data'];

    protected $casts = [
        'snapshot_data' => 'array'
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
