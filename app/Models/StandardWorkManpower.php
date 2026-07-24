<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StandardWorkManpower extends Model
{
    protected $table = 'standard_work_manpower';
    protected $guarded = ['id'];
    public function work() { return $this->belongsTo(StandardWork::class, 'standard_work_id'); }
}
