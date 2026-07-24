<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StandardWorkEquipment extends Model
{
    protected $table = 'standard_work_equipment';
    protected $guarded = ['id'];
    public function work() { return $this->belongsTo(StandardWork::class, 'standard_work_id'); }
}
