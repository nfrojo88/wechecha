<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'project_id', 'employee_code', 'full_name',
        'phone', 'email', 'role_title', 'department',
        'employment_type', 'contract_type', 'date_of_joining', 'basic_salary',
        'transport_allowance', 'house_allowance', 'position_allowance',
        'status', 'notes', 'bank_name', 'account_number',
        'guarantee_letter', 'guarantee_letter_submitted_at', 'guarantee_letter_required',
        'device_user_id', 'is_approved_by_gm', 'gm_approved_at', 'gm_approved_by',
    ];

    protected $casts = [
        'date_of_joining' => 'date',
        'basic_salary'    => 'decimal:2',
        'guarantee_letter_submitted_at' => 'date',
        'guarantee_letter_required' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function skills()
    {
        return $this->hasMany(EmployeeSkill::class);
    }

    public function availability()
    {
        return $this->hasMany(ResourceAvailability::class);
    }

    public function manpowerAssignments()
    {
        return $this->hasMany(ManpowerAssignment::class);
    }

    public function performanceReviews()
    {
        return $this->hasMany(PerformanceReview::class);
    }

    public function performanceMetrics()
    {
        return $this->hasMany(PerformanceMetric::class);
    }

    public function performanceGoals()
    {
        return $this->hasMany(PerformanceGoal::class);
    }

    public function competencyAssessments()
    {
        return $this->hasMany(CompetencyAssessment::class);
    }

    public function achievements()
    {
        return $this->hasMany(EmployeeAchievement::class);
    }

    public function contracts()
    {
        return $this->hasMany(EmployeeContract::class);
    }

    public function salaryStructure()
    {
        return $this->hasOne(SalaryStructure::class)->where('is_active', true);
    }

    public function ratings()
    {
        return $this->hasMany(EmployeeRating::class);
    }

    public function advances()
    {
        return $this->hasMany(EmployeeAdvance::class);
    }

    public function assets()
    {
        return $this->hasMany(EmployeeAsset::class);
    }

    public function activeAssets()
    {
        return $this->assets()->whereIn('status', ['assigned', 'in_use']);
    }

    public function education()
    {
        return $this->hasMany(EmployeeEducation::class)->orderBy('end_date', 'desc');
    }

    public function experience()
    {
        return $this->hasMany(EmployeeExperience::class)->orderBy('start_date', 'desc');
    }

    /**
     * Check if guarantee letter is overdue and account should be blocked (30+ days)
     */
    public function isGuaranteeLetterExpired()
    {
        if (!$this->guarantee_letter_required || $this->guarantee_letter) {
            return false;
        }
        
        return $this->date_of_joining->addDays(30)->isPast();
    }

    /**
     * Get guarantee letter URL
     */
    public function getGuaranteeLetterUrlAttribute()
    {
        if ($this->guarantee_letter) {
            return \Storage::url($this->guarantee_letter);
        }
        return null;
    }

    /**
     * Check if guarantee letter is overdue (30+ days without submission)
     */
    public function getIsGuaranteeOverdueAttribute()
    {
        if (!$this->guarantee_letter_required || $this->guarantee_letter) {
            return false;
        }
        
        return $this->date_of_joining->addDays(30)->isPast();
    }

    /**
     * Check if guarantee letter warning should show (20+ days without submission)
     */
    public function getShowGuaranteeWarningAttribute()
    {
        if (!$this->guarantee_letter_required || $this->guarantee_letter) {
            return false;
        }
        
        return $this->date_of_joining->addDays(20)->isPast();
    }

    /**
     * Get days until guarantee letter deadline
     */
    public function getDaysUntilGuaranteeDeadlineAttribute()
    {
        if (!$this->guarantee_letter_required || $this->guarantee_letter) {
            return null;
        }
        
        $deadline = $this->date_of_joining->addDays(30);
        return now()->diffInDays($deadline, false); // negative if overdue
    }

    public function getCurrentMonthlyDeductionAttribute()
    {
        return $this->advances()
            ->where('status', 'disbursed')
            ->where('recovered_at', null)
            ->sum(\DB::raw('amount / installments'));
    }

    public function deviceLogs()
    {
        return $this->hasMany(DeviceAttendanceLog::class, 'device_user_id', 'device_user_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
