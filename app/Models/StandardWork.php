<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StandardWork extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function materials()
    {
        return $this->hasMany(StandardWorkMaterial::class);
    }

    public function manpower()
    {
        return $this->hasMany(StandardWorkManpower::class);
    }

    public function equipment()
    {
        return $this->hasMany(StandardWorkEquipment::class);
    }
}
