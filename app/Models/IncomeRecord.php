<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomeRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'income_no', 'project_id', 'category', 'income_date', 'amount',
        'payment_method', 'bank_account_id', 'description', 'notes',
        'status', 'created_by', 'journal_entry_id',
    ];

    protected $casts = ['income_date' => 'date'];

    public function project()       { return $this->belongsTo(Project::class); }
    public function bankAccount()   { return $this->belongsTo(BankAccount::class); }
    public function createdBy()     { return $this->belongsTo(User::class, 'created_by'); }
    public function journalEntry()  { return $this->belongsTo(JournalEntry::class); }
}
