<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    /**
     * Move stock into a store (receipt, transfer-in, adjustment+).
     */
    public function stockIn(
        int $storeId,
        int $productId,
        float $quantity,
        float $unitCost,
        string $type,          // 'in' | 'transfer' | 'adjustment' | 'return'
        int $performedBy,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $remarks = null
    ): Inventory {
        return DB::transaction(function () use (
            $storeId, $productId, $quantity, $unitCost, $type,
            $performedBy, $referenceType, $referenceId, $remarks
        ) {
            $inventory = Inventory::firstOrCreate(
                ['store_id' => $storeId, 'product_id' => $productId],
                ['quantity_on_hand' => 0, 'quantity_reserved' => 0, 'unit_cost' => $unitCost, 'min_stock' => 0]
            );

            // Weighted average cost
            $totalOld  = $inventory->quantity_on_hand * ($inventory->unit_cost ?? 0);
            $totalNew  = $quantity * $unitCost;
            $newQty    = $inventory->quantity_on_hand + $quantity;
            $newCost   = $newQty > 0 ? ($totalOld + $totalNew) / $newQty : $unitCost;

            $inventory->quantity_on_hand  = $newQty;
            $inventory->unit_cost         = $newCost;
            $inventory->last_movement_at  = now();
            $inventory->save();

            InventoryMovement::create([
                'inventory_id'   => $inventory->id,
                'type'           => $type,
                'quantity'       => $quantity,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
                'performed_by'   => $performedBy,
                'remarks'        => $remarks,
            ]);

            return $inventory->fresh();
        });
    }

    /**
     * Move stock out of a store (issue, transfer-out, adjustment-).
     *
     * @throws \Exception if insufficient stock
     */
    public function stockOut(
        int $storeId,
        int $productId,
        float $quantity,
        string $type,          // 'out' | 'transfer' | 'adjustment' | 'return'
        int $performedBy,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $remarks = null
    ): Inventory {
        return DB::transaction(function () use (
            $storeId, $productId, $quantity, $type,
            $performedBy, $referenceType, $referenceId, $remarks
        ) {
            $inventory = Inventory::where('store_id', $storeId)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($inventory->quantity_on_hand < $quantity) {
                throw new \Exception(
                    "Insufficient stock. Available: {$inventory->quantity_on_hand}, Requested: {$quantity}"
                );
            }

            $inventory->quantity_on_hand -= $quantity;
            $inventory->last_movement_at  = now();
            $inventory->save();

            InventoryMovement::create([
                'inventory_id'   => $inventory->id,
                'type'           => $type,
                'quantity'       => -$quantity,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
                'performed_by'   => $performedBy,
                'remarks'        => $remarks,
            ]);

            return $inventory->fresh();
        });
    }

    /**
     * Transfer stock between stores.
     */
    public function transfer(
        int $fromStoreId,
        int $toStoreId,
        int $productId,
        float $quantity,
        int $performedBy,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $remarks = null
    ): array {
        return DB::transaction(function () use (
            $fromStoreId, $toStoreId, $productId, $quantity,
            $performedBy, $referenceType, $referenceId, $remarks
        ) {
            $from = $this->stockOut(
                $fromStoreId, $productId, $quantity, 'transfer',
                $performedBy, $referenceType, $referenceId, $remarks
            );

            $unitCost = $from->unit_cost ?? 0;

            $to = $this->stockIn(
                $toStoreId, $productId, $quantity, $unitCost, 'transfer',
                $performedBy, $referenceType, $referenceId, $remarks
            );

            return ['from' => $from, 'to' => $to];
        });
    }

    /**
     * Reserve stock for a pending request.
     */
    public function reserve(int $storeId, int $productId, float $quantity): bool
    {
        return DB::transaction(function () use ($storeId, $productId, $quantity) {
            $inventory = Inventory::where('store_id', $storeId)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->firstOrFail();

            if (($inventory->quantity_on_hand - $inventory->quantity_reserved) < $quantity) {
                return false;
            }

            $inventory->quantity_reserved += $quantity;
            $inventory->save();

            return true;
        });
    }

    /**
     * Release a reservation (cancelled request).
     */
    public function releaseReservation(int $storeId, int $productId, float $quantity): void
    {
        DB::transaction(function () use ($storeId, $productId, $quantity) {
            $inventory = Inventory::where('store_id', $storeId)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->first();

            if ($inventory) {
                $inventory->quantity_reserved = max(0, $inventory->quantity_reserved - $quantity);
                $inventory->save();
            }
        });
    }

    /**
     * Get current stock balance for a product across all stores (or a specific store).
     */
    public function getBalance(int $productId, ?int $storeId = null): array
    {
        $query = Inventory::with(['store', 'product'])
            ->where('product_id', $productId);

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        return $query->get()->map(fn ($inv) => [
            'store'             => $inv->store->name,
            'quantity_on_hand'  => $inv->quantity_on_hand,
            'quantity_reserved' => $inv->quantity_reserved,
            'quantity_available'=> $inv->quantity_available,
            'unit_cost'         => $inv->unit_cost,
            'total_value'       => $inv->total_value,
        ])->toArray();
    }
}
