<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoaTransfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'coa_transfers';

    protected $fillable = [
        'transfer_no',
        'from_coa_id',
        'to_coa_id',
        'amount',
        'transfer_date',
        'reference_no',
        'description',
        'attachment_path',
        'journal_entry_id',
        'created_by',
        'status',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'transfer_date' => 'date',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function fromCoa()
    {
        return $this->belongsTo(ChartOfAccount::class, 'from_coa_id');
    }

    public function toCoa()
    {
        return $this->belongsTo(ChartOfAccount::class, 'to_coa_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getAttachmentUrlAttribute(): ?string
    {
        return \App\Services\FileUploadService::url($this->attachment_path);
    }
}
