<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManpowerRole extends Model
{
    protected $fillable = ['name', 'default_unit', 'category'];
}
