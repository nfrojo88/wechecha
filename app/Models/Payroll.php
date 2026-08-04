<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'month', 'year',
        'basic_salary',
        'transport_allowance', 'house_allowance', 'position_allowance',
        'allowances',      // total allowances (sum of transport+house+position)
        'overtime_pay',
        'pension',         // 7% of basic (employee contribution)
        'deductions',      // other deductions
        'tax',             // income tax
        'gross_salary',
        'net_salary',
        'status',          // draft | pending | paid
        'gm_status',       // null | submitted | approved | rejected
        'gm_notes',
        'gm_approved_by',
        'gm_approved_at',
        'submitted_to_gm_at',
        'paid_at', 'created_by', 'notes',
        'payroll_ref', 'remarks',
        'payment_method', 'processed_at', 'processed_by',
    ];

    protected $casts = [
        'paid_at'             => 'datetime',
        'processed_at'        => 'datetime',
        'gm_approved_at'      => 'datetime',
        'submitted_to_gm_at'  => 'datetime',
        'basic_salary'        => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'house_allowance'     => 'decimal:2',
        'position_allowance'  => 'decimal:2',
        'allowances'          => 'decimal:2',
        'overtime_pay'        => 'decimal:2',
        'pension'             => 'decimal:2',
        'deductions'          => 'decimal:2',
        'tax'                 => 'decimal:2',
        'net_salary'          => 'decimal:2',
        'gross_salary'        => 'decimal:2',
    ];

    public function employee()   { return $this->belongsTo(Employee::class); }
    public function creator()    { return $this->belongsTo(User::class, 'created_by'); }
    public function processedBy(){ return $this->belongsTo(User::class, 'processed_by'); }
    public function gmApprover() { return $this->belongsTo(User::class, 'gm_approved_by'); }
    public function components() { return $this->hasMany(PayrollComponent::class); }
    public function adjustments(){ return $this->hasMany(PayrollAdjustment::class); }

    /** Auto-calculate gross & net before every save */
    protected static function booted()
    {
        static::saving(function (Payroll $p) {
            // Total allowances = individual parts
            $p->allowances  = ($p->transport_allowance ?? 0)
                            + ($p->house_allowance     ?? 0)
                            + ($p->position_allowance  ?? 0);

            // Pension = 7% of basic (employee portion)
            $p->pension     = round(($p->basic_salary ?? 0) * 0.07, 2);

            // Gross = basic + allowances + overtime
            $p->gross_salary = ($p->basic_salary  ?? 0)
                             + ($p->allowances     ?? 0)
                             + ($p->overtime_pay   ?? 0);

            // Net = gross − pension − tax − other deductions
            $p->net_salary  = $p->gross_salary
                            - ($p->pension     ?? 0)
                            - ($p->tax         ?? 0)
                            - ($p->deductions  ?? 0);
        });
    }

    public function isPaid() { return $this->status === 'paid' && $this->paid_at !== null; }

    public function getPeriodAttribute()
    {
        return date('F Y', strtotime($this->year . '-' . $this->month . '-01'));
    }

    /** Ethiopian income tax on (gross - pension) */
    public static function calculateIncomeTax(float $taxableIncome): float
    {
        if ($taxableIncome <= 600)  return 0;
        if ($taxableIncome <= 1650) return ($taxableIncome - 600)  * 0.10;
        if ($taxableIncome <= 3200) return ($taxableIncome - 1650) * 0.15 + 105;
        if ($taxableIncome <= 5250) return ($taxableIncome - 3200) * 0.20 + 332.50;
        if ($taxableIncome <= 7800) return ($taxableIncome - 5250) * 0.25 + 742.50;
        if ($taxableIncome <= 10900)return ($taxableIncome - 7800) * 0.30 + 1380;
        return ($taxableIncome - 10900) * 0.35 + 2310;
    }
}

