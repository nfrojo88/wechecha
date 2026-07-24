<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_name', 'account_number', 'bank_name', 'branch',
        'account_type', 'currency', 'current_balance', 'coa_id',
        'is_active', 'is_default', 'notes',
    ];

    protected $casts = ['is_active' => 'boolean', 'is_default' => 'boolean'];

    public function coa()         { return $this->belongsTo(ChartOfAccount::class, 'coa_id'); }
    public function transactions() { return $this->hasMany(BankTransaction::class); }
}
