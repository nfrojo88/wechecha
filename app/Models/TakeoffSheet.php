<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TakeoffSheet extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function items()
    {
        return $this->hasMany(TakeoffItem::class, 'takeoff_sheet_id');
    }

    public function sections()
    {
        return $this->hasMany(TakeoffSection::class, 'takeoff_sheet_id')->orderBy('sort_order');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function editRequests()
    {
        return $this->hasMany(TakeoffEditRequest::class, 'takeoff_sheet_id');
    }
}
