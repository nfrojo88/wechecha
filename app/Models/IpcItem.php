<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpcItem extends Model
{
    protected $guarded = [];

    public function ipcRecord() { return $this->belongsTo(IpcRecord::class, 'ipc_record_id'); }
    public function agreementItem() { return $this->belongsTo(SubconAgreementItem::class, 'agreement_item_id'); }
}
