<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'month', 'year',
        'basic_salary', 'allowances', 'overtime_pay',
        'deductions', 'tax', 'net_salary',
        'status', 'paid_at', 'created_by', 'notes',
        'payroll_ref', 'gross_salary', 'remarks',
        'payment_method', 'processed_at', 'processed_by',
    ];

    protected $casts = [
        'paid_at'      => 'datetime',
        'processed_at' => 'datetime',
        'basic_salary' => 'decimal:2',
        'allowances'   => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'deductions'   => 'decimal:2',
        'tax'          => 'decimal:2',
        'net_salary'   => 'decimal:2',
        'gross_salary' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function components()
    {
        return $this->hasMany(PayrollComponent::class);
    }

    public function adjustments()
    {
        return $this->hasMany(PayrollAdjustment::class);
    }

    /** Recalculate net_salary before saving */
    protected static function booted()
    {
        static::saving(function (Payroll $payroll) {
            $payroll->gross_salary = $payroll->basic_salary + $payroll->allowances + $payroll->overtime_pay;
            $payroll->net_salary = $payroll->gross_salary - ($payroll->deductions + $payroll->tax);
        });
    }

    public function isPaid()
    {
        return $this->status === 'paid' && $this->paid_at !== null;
    }

    public function getPeriodAttribute()
    {
        return date('F Y', strtotime($this->year . '-' . $this->month . '-01'));
    }
}
