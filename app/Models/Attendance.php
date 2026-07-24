<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'employee_id', 'attendance_date', 'check_in', 'check_out',
        'hours_worked', 'status', 'source', 'biometric_device_id',
        'notes', 'is_approved', 'approved_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'is_approved'     => 'boolean',
    ];

    public function employee()   { return $this->belongsTo(Employee::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
}
