<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $fillable = ['title', 'department_id', 'min_salary', 'max_salary', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function department() { return $this->belongsTo(Department::class); }
    public function employees()  { return $this->hasMany(Employee::class); }
}
