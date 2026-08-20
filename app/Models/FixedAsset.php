<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fixed_assets';

    protected $fillable = [
        'name',
        'category',
        'code_prefix',
        'total_quantity',
        'unit_cost',
        'purchase_date',
        'supplier',
        'store_id',
        'description',
        'created_by',
    ];

    protected $casts = [
        'total_quantity' => 'integer',
        'unit_cost'      => 'decimal:2',
        'purchase_date'  => 'date',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'deleted_at'     => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function units()
    {
        return $this->hasMany(FixedAssetUnit::class, 'fixed_asset_id')->orderBy('sequence_number');
    }

    public function availableUnits()
    {
        return $this->hasMany(FixedAssetUnit::class, 'fixed_asset_id')
            ->where('status', FixedAssetUnit::STATUS_IN_STORE)
            ->orderBy('sequence_number');
    }

    public function assignedUnits()
    {
        return $this->hasMany(FixedAssetUnit::class, 'fixed_asset_id')
            ->where('status', FixedAssetUnit::STATUS_ASSIGNED)
            ->orderBy('sequence_number');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Accessors & Helpers ──────────────────────────────────────────────────

    public function getUnitsCountAttribute(): int
    {
        return $this->units()->count();
    }

    public function getAvailableCountAttribute(): int
    {
        return $this->availableUnits()->count();
    }

    public function getAssignedCountAttribute(): int
    {
        return $this->assignedUnits()->count();
    }

    public function getTotalValueAttribute(): float
    {
        return (float) ($this->total_quantity * $this->unit_cost);
    }

    public function getCategoryIconAttribute(): string
    {
        return match(strtolower(trim($this->category))) {
            'computer & it', 'computer', 'it', 'electronics' => 'fa-laptop',
            'vehicle', 'vehicles', 'automotive'               => 'fa-truck-pickup',
            'heavy machinery', 'machinery', 'heavy equipment' => 'fa-truck-monster',
            'furniture', 'furniture & fixture'                => 'fa-chair',
            'tools', 'tools & equipment'                      => 'fa-screwdriver-wrench',
            default                                           => 'fa-boxes-stacked',
        };
    }

    /**
     * Check if more units can be added based on strict quantity limit.
     */
    public function canAddUnit(): bool
    {
        return $this->units()->count() < $this->total_quantity;
    }

    /**
     * Generate unit code with clean prefix format (e.g. COMP-1).
     */
    public function generateUnitCode(int $sequenceNumber): string
    {
        $prefix = strtoupper(trim($this->code_prefix ?: 'AST'));
        return "{$prefix}-{$sequenceNumber}";
    }

    /**
     * Auto-generate missing units to match the total_quantity.
     */
    public function generateUnitsToMatchQuantity(array $defaultAttributes = []): int
    {
        $currentCount = $this->units()->count();
        $targetQty = $this->total_quantity;
        $created = 0;

        if ($currentCount >= $targetQty) {
            return 0;
        }

        // Find max sequence number used so far
        $maxSeq = (int) $this->units()->max('sequence_number');

        for ($i = $currentCount + 1; $i <= $targetQty; $i++) {
            $maxSeq++;
            $unitCode = $this->generateUnitCode($maxSeq);

            // Avoid collisions if previously deleted or customized
            while (FixedAssetUnit::where('unit_code', $unitCode)->exists()) {
                $maxSeq++;
                $unitCode = $this->generateUnitCode($maxSeq);
            }

            FixedAssetUnit::create(array_merge([
                'fixed_asset_id'  => $this->id,
                'unit_code'       => $unitCode,
                'sequence_number' => $maxSeq,
                'status'          => FixedAssetUnit::STATUS_IN_STORE,
                'condition'       => 'good',
                'purchase_price'  => $this->unit_cost,
                'current_location'=> $this->store->name ?? 'Main Store',
                'created_by'      => auth()->id(),
            ], $defaultAttributes));

            $created++;
        }

        return $created;
    }
}
