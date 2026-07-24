<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'code', 'head_id', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function head()         { return $this->belongsTo(Employee::class, 'head_id'); }
    public function designations() { return $this->hasMany(Designation::class); }
    public function employees()    { return $this->hasMany(Employee::class, 'department', 'name'); }
}
