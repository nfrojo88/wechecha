<?php

namespace App\Services;

use App\Models\MaterialPlan;
use App\Models\MaterialUsage;
use App\Models\CutOptimization;
use App\Models\Waste;
use App\Models\ChartOfAccount;
use App\Services\Finance\JournalEngine;
use Illuminate\Support\Facades\DB;

class OperationalService
{
    protected InventoryService $inventoryService;
    protected JournalEngine $journalEngine;

    public function __construct(InventoryService $inventoryService, JournalEngine $journalEngine)
    {
        $this->inventoryService = $inventoryService;
        $this->journalEngine = $journalEngine;
    }

    public function recordMaterialUsage(MaterialUsage $usage): void
    {
        DB::transaction(function () use ($usage) {
            $totalCost = 0;
            foreach ($usage->items as $item) {
                $this->inventoryService->stockOut(
                    $usage->store_id,
                    $item->product_id,
                    $item->quantity, // Changed from used_quantity to quantity based on Phase 11
                    'material_issue',
                    auth()->id(),
                    'material_usage',
                    $usage->id
                );
                
                $unitCost = $item->product->unit_cost ?? 0;
                $totalCost += ($item->quantity * $unitCost);
            }
            $usage->update(['status' => 'confirmed']);

            // Create Journal Entry for Material Usage
            if ($totalCost > 0) {
                $materialCostAccount = ChartOfAccount::where('code', '5100')->first();
                $inventoryAccount = ChartOfAccount::where('code', '1310')->first() ?? ChartOfAccount::where('code', '1300')->first();

                if ($materialCostAccount && $inventoryAccount) {
                    $this->journalEngine->createEntry(
                        'MaterialUsage',
                        $usage->id,
                        'Material usage for project: ' . $usage->project->name,
                        [
                            ['account_id' => $materialCostAccount->id, 'side' => 'debit', 'amount' => $totalCost, 'description' => 'Material Cost'],
                            ['account_id' => $inventoryAccount->id, 'side' => 'credit', 'amount' => $totalCost, 'description' => 'Inventory Deduction']
                        ],
                        auth()->id(),
                        auth()->id() // Auto-post since it's verified operational data
                    );
                }
            }
        });
    }

    public function recordWaste(Waste $waste): void
    {
        DB::transaction(function () use ($waste) {
            $totalCost = 0;
            foreach ($waste->items as $item) {
                $this->inventoryService->stockOut(
                    $waste->store_id,
                    $item->product_id,
                    $item->quantity,
                    'waste',
                    auth()->id(),
                    'waste',
                    $waste->id
                );
                $unitCost = $item->product->unit_cost ?? 0;
                $totalCost += ($item->quantity * $unitCost);
            }
            $waste->update(['status' => 'verified']);

            // Create Journal Entry for Waste
            if ($totalCost > 0) {
                $wasteAccount = ChartOfAccount::where('code', '5500')->first();
                $inventoryAccount = ChartOfAccount::where('code', '1310')->first() ?? ChartOfAccount::where('code', '1300')->first();

                if ($wasteAccount && $inventoryAccount) {
                    $this->journalEngine->createEntry(
                        'Waste',
                        $waste->id,
                        'Material waste/loss logged: ' . $waste->reason,
                        [
                            ['account_id' => $wasteAccount->id, 'side' => 'debit', 'amount' => $totalCost, 'description' => 'Waste Cost'],
                            ['account_id' => $inventoryAccount->id, 'side' => 'credit', 'amount' => $totalCost, 'description' => 'Inventory Deduction']
                        ],
                        auth()->id(),
                        auth()->id() // Auto-post
                    );
                }
            }
        });
    }
}
