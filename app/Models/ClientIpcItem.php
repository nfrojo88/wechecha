<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientIpcItem extends Model
{
    protected $table = 'client_ipc_items';
    protected $guarded = ['id'];

    protected $casts = [
        'boq_quantity'        => 'decimal:4',
        'previous_quantity'   => 'decimal:4',
        'current_quantity'    => 'decimal:4',
        'cumulative_quantity' => 'decimal:4',
        'unit_rate'           => 'decimal:2',
        'current_amount'      => 'decimal:2',
        'cumulative_amount'   => 'decimal:2',
    ];

    public function clientIpc()
    {
        return $this->belongsTo(ClientIpc::class, 'client_ipc_id');
    }

    public function boqItem()
    {
        return $this->belongsTo(BoqItem::class, 'boq_item_id');
    }

    // Percentage of BOQ quantity certified to date
    public function completionPercent(): float
    {
        if ($this->boq_quantity <= 0) return 0;
        return round(($this->cumulative_quantity / $this->boq_quantity) * 100, 1);
    }
}
