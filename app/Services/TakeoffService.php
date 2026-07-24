<?php

namespace App\Services;

use App\Models\TakeoffSheet;
use App\Models\TakeoffItem;
use App\Models\TakeoffRebarDetail;

class TakeoffService
{
    public function createSheet(array $data)
    {
        return TakeoffSheet::create($data);
    }

    public function addItem(TakeoffSheet $sheet, array $data)
    {
        $data['takeoff_sheet_id'] = $sheet->id;
        return TakeoffItem::create($data);
    }

    public function addRebarDetail(TakeoffItem $item, array $data)
    {
        $data['takeoff_item_id'] = $item->id;
        $data['total_bars'] = $item->count * ($data['bars_per_member'] ?? 1);
        $data['total_length'] = $data['total_bars'] * $data['bar_length'];
        
        if (isset($data['weight_per_meter'])) {
            $data['total_weight'] = $data['total_length'] * $data['weight_per_meter'];
        }

        return TakeoffRebarDetail::create($data);
    }
}
