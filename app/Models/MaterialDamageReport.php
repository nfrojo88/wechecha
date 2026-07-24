<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialDamageReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'product_id',
        'reported_by',
        'quantity',
        'damage_reason',
        'description',
        'status',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
