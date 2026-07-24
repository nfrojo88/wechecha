<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    protected $fillable = [
        'bank_account_id', 'transaction_date', 'type', 'amount', 'balance_after',
        'reference_no', 'reference_type', 'reference_id', 'description', 'is_reconciled',
    ];

    protected $casts = ['transaction_date' => 'date', 'is_reconciled' => 'boolean'];

    public function bankAccount() { return $this->belongsTo(BankAccount::class); }
}
