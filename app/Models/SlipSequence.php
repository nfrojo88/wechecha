<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlipSequence extends Model
{
    protected $fillable = [
        'store_id',
        'slip_type',
        'label',
        'prefix',
        'book_start_no',
        'book_end_no',
        'current_slip_no',
        'used_count',
        'status',
        'notes',
    ];

    protected $casts = [
        'slip_type' => 'string',
        'status' => 'string',
    ];

    /**
     * Relationship: belongs to Store
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get next slip number (raw numeric value)
     * @return int
     */
    public function getNextSlipNumber(): int
    {
        return $this->current_slip_no;
    }

    /**
     * Format slip number with prefix
     * @param int $number
     * @return string
     */
    public function formatSlipNumber(int $number): string
    {
        if ($this->prefix) {
            return $this->prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
        }
        return str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate next slip number and increment counter
     * @return string (formatted slip number)
     */
    public function generateSlipNumber(): string
    {
        // Check if book is full
        if ($this->current_slip_no > $this->book_end_no) {
            $this->update(['status' => 'full']);
            throw new \Exception('Slip sequence book is full. Cannot generate more slips.');
        }

        $formatted = $this->formatSlipNumber($this->current_slip_no);

        // Increment for next call
        $this->update([
            'current_slip_no' => $this->current_slip_no + 1,
            'used_count' => $this->used_count + 1,
        ]);

        return $formatted;
    }

    /**
     * Get percentage of book used
     * @return float
     */
    public function getPercentageUsed(): float
    {
        $total = $this->book_end_no - $this->book_start_no + 1;
        if ($total === 0) {
            return 0;
        }
        return round(($this->used_count / $total) * 100, 2);
    }

    /**
     * Get remaining slips in book
     * @return int
     */
    public function getRemainingSlips(): int
    {
        return max(0, $this->book_end_no - $this->current_slip_no + 1);
    }

    /**
     * Check if sequence is valid for a given slip number
     * @param int $slipNumber
     * @return bool
     */
    public function isValidSlipNumber(int $slipNumber): bool
    {
        return $slipNumber >= $this->book_start_no && $slipNumber <= $this->book_end_no;
    }

    /**
     * Mark slip as used (for validation/audit)
     * @param int $slipNumber
     */
    public function markSlipAsUsed(int $slipNumber): void
    {
        if (!$this->isValidSlipNumber($slipNumber)) {
            throw new \Exception("Slip number $slipNumber is outside book range.");
        }

        // If it's the next expected, increment properly
        if ($slipNumber === $this->current_slip_no) {
            $this->update([
                'current_slip_no' => $slipNumber + 1,
                'used_count' => $this->used_count + 1,
            ]);
        } else {
            // Out of sequence - just log usage, flag as gap
            $this->update(['used_count' => $this->used_count + 1]);
        }
    }
}
