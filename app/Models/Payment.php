<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'reference_number', 'amount',
        'payment_date', 'payment_type', 'description',
        'created_by', 'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public const TYPES = [
        'progress'           => 'Progress Payment',
        'advance'            => 'Advance',
        'retention_release'  => 'Retention Release',
        'final'              => 'Final Payment',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
