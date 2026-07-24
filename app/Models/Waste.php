<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ScopesByStore;

class Waste extends Model
{
    use ScopesByStore;
    protected $guarded = [];
    protected $casts = ['waste_date' => 'date'];

    public function project() { return $this->belongsTo(Project::class); }
    public function store() { return $this->belongsTo(Store::class); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }
    public function items() { return $this->hasMany(WasteItem::class); }
}
