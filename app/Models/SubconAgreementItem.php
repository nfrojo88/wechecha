<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubconAgreementItem extends Model
{
    protected $guarded = [];

    public function agreement() { return $this->belongsTo(SubconAgreement::class, 'agreement_id'); }
    public function boqItem() { return $this->belongsTo(BoqItem::class, 'boq_item_id'); }
}
