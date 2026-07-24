<?php

namespace App\Services\Finance;

use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;
use Exception;

class JournalEngine
{
    /**
     * Creates a new Journal Entry with lines.
     * Validates that total debits == total credits.
     */
    public function createEntry(
        string $referenceType,
        int $referenceId,
        string $description,
        array $lines,
        int $createdBy,
        ?int $approvedBy = null
    ): JournalEntry {
        // Calculate debits and credits
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($lines as $line) {
            if ($line['side'] === 'debit') {
                $totalDebit += (float)$line['amount'];
            } elseif ($line['side'] === 'credit') {
                $totalCredit += (float)$line['amount'];
            }
        }

        // Validate double-entry
        if (abs($totalDebit - $totalCredit) > 0.01) {
            throw new Exception("Journal entry unbalanced. Debits: {$totalDebit}, Credits: {$totalCredit}");
        }

        return DB::transaction(function () use (
            $referenceType, $referenceId, $description, $lines, $createdBy, $approvedBy
        ) {
            $entry = JournalEntry::create([
                'entry_no' => 'JE-' . date('Ymd') . '-' . rand(1000, 9999),
                'entry_date' => now()->toDateString(),
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'description' => $description,
                'status' => 'draft',
                'created_by' => $createdBy,
            ]);

            foreach ($lines as $line) {
                $entry->lines()->create([
                    'account_id' => $line['account_id'],
                    'side' => $line['side'],
                    'amount' => $line['amount'],
                    'description' => $line['description'] ?? null,
                ]);
            }

            if ($approvedBy) {
                $this->postEntry($entry->id, $approvedBy);
                $entry->refresh();
            }

            return $entry;
        });
    }

    /**
     * Posts a Journal Entry, locking it and updating COA balances.
     */
    public function postEntry(int $journalEntryId, int $approvedBy): JournalEntry
    {
        return DB::transaction(function () use ($journalEntryId, $approvedBy) {
            $entry = JournalEntry::with('lines.account')->findOrFail($journalEntryId);

            if ($entry->status !== 'draft') {
                throw new Exception("Only draft entries can be posted.");
            }

            foreach ($entry->lines as $line) {
                $account = $line->account;
                $amount = (float)$line->amount;
                $side = $line->side;

                // Asset / Expense: Debit increases balance, Credit decreases
                // Liability / Equity / Revenue: Credit increases balance, Debit decreases
                $normalBalanceIsDebit = in_array($account->type, ['asset', 'expense']);

                if ($normalBalanceIsDebit) {
                    if ($side === 'debit') {
                        $account->current_balance += $amount;
                    } else {
                        $account->current_balance -= $amount;
                    }
                } else {
                    if ($side === 'credit') {
                        $account->current_balance += $amount;
                    } else {
                        $account->current_balance -= $amount;
                    }
                }

                $account->save();
            }

            $entry->update([
                'status' => 'posted',
                'approved_by' => $approvedBy,
                'posted_at' => now(),
            ]);

            return $entry;
        });
    }

    /**
     * Cancels an entry and reverses its effects on COA balances if it was posted.
     */
    public function cancelEntry(int $journalEntryId, string $reason): JournalEntry
    {
        return DB::transaction(function () use ($journalEntryId, $reason) {
            $entry = JournalEntry::with('lines.account')->findOrFail($journalEntryId);

            if ($entry->status === 'cancelled') {
                return $entry;
            }

            if ($entry->status === 'posted') {
                // Reverse the balances
                foreach ($entry->lines as $line) {
                    $account = $line->account;
                    $amount = (float)$line->amount;
                    $side = $line->side;

                    $normalBalanceIsDebit = in_array($account->type, ['asset', 'expense']);

                    if ($normalBalanceIsDebit) {
                        if ($side === 'debit') {
                            $account->current_balance -= $amount; // Reverse debit
                        } else {
                            $account->current_balance += $amount; // Reverse credit
                        }
                    } else {
                        if ($side === 'credit') {
                            $account->current_balance -= $amount; // Reverse credit
                        } else {
                            $account->current_balance += $amount; // Reverse debit
                        }
                    }

                    $account->save();
                }
            }

            $entry->update([
                'status' => 'cancelled',
                'description' => $entry->description . " (Cancelled: $reason)"
            ]);

            return $entry;
        });
    }
}
