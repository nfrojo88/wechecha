<?php

$models = [
    'MaterialPlan' => <<<PHP
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MaterialPlan extends Model {
    protected \$guarded = [];
    public function project() { return \$this->belongsTo(Project::class); }
    public function planHeader() { return \$this->belongsTo(ErpPlanHeader::class); }
    public function createdBy() { return \$this->belongsTo(User::class, 'created_by'); }
    public function items() { return \$this->hasMany(MaterialPlanItem::class); }
}
PHP,

    'MaterialPlanItem' => <<<PHP
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MaterialPlanItem extends Model {
    protected \$guarded = [];
    public function materialPlan() { return \$this->belongsTo(MaterialPlan::class); }
    public function product() { return \$this->belongsTo(Product::class); }
    public function task() { return \$this->belongsTo(ErpPlanTask::class); }
    public function store() { return \$this->belongsTo(Store::class); }
    public function generatedPr() { return \$this->belongsTo(PurchaseRequest::class, 'generated_pr_id'); }
}
PHP,

    'MaterialUsage' => <<<PHP
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ScopesByStore;
class MaterialUsage extends Model {
    use ScopesByStore;
    protected \$guarded = [];
    protected \$casts = ['usage_date' => 'date'];
    public function project() { return \$this->belongsTo(Project::class); }
    public function store() { return \$this->belongsTo(Store::class); }
    public function task() { return \$this->belongsTo(ErpPlanTask::class); }
    public function createdBy() { return \$this->belongsTo(User::class, 'created_by'); }
    public function items() { return \$this->hasMany(MaterialUsageItem::class); }
}
PHP,

    'MaterialUsageItem' => <<<PHP
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MaterialUsageItem extends Model {
    protected \$guarded = [];
    public function materialUsage() { return \$this->belongsTo(MaterialUsage::class); }
    public function product() { return \$this->belongsTo(Product::class); }
}
PHP,

    'CutOptimization' => <<<PHP
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CutOptimization extends Model {
    protected \$guarded = [];
    protected \$casts = ['optimization_result' => 'array'];
    public function project() { return \$this->belongsTo(Project::class); }
    public function createdBy() { return \$this->belongsTo(User::class, 'created_by'); }
    public function items() { return \$this->hasMany(CutOptimizationItem::class); }
}
PHP,

    'CutOptimizationItem' => <<<PHP
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CutOptimizationItem extends Model {
    protected \$guarded = [];
    public function cutOptimization() { return \$this->belongsTo(CutOptimization::class); }
    public function product() { return \$this->belongsTo(Product::class); }
}
PHP,

    'Issue' => <<<PHP
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Issue extends Model {
    use SoftDeletes;
    protected \$guarded = [];
    protected \$casts = ['due_date' => 'date', 'resolved_at' => 'datetime'];
    public function project() { return \$this->belongsTo(Project::class); }
    public function reportedBy() { return \$this->belongsTo(User::class, 'reported_by'); }
    public function assignedTo() { return \$this->belongsTo(User::class, 'assigned_to'); }
    public function task() { return \$this->belongsTo(ErpPlanTask::class); }
    public function comments() { return \$this->hasMany(IssueComment::class); }
}
PHP,

    'IssueComment' => <<<PHP
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class IssueComment extends Model {
    protected \$guarded = [];
    public function issue() { return \$this->belongsTo(Issue::class); }
    public function user() { return \$this->belongsTo(User::class); }
}
PHP,

    'Waste' => <<<PHP
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ScopesByStore;
class Waste extends Model {
    use ScopesByStore;
    protected \$guarded = [];
    protected \$casts = ['waste_date' => 'date'];
    public function project() { return \$this->belongsTo(Project::class); }
    public function store() { return \$this->belongsTo(Store::class); }
    public function recordedBy() { return \$this->belongsTo(User::class, 'recorded_by'); }
    public function items() { return \$this->hasMany(WasteItem::class); }
}
PHP,

    'WasteItem' => <<<PHP
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WasteItem extends Model {
    protected \$guarded = [];
    public function waste() { return \$this->belongsTo(Waste::class); }
    public function product() { return \$this->belongsTo(Product::class); }
}
PHP,
];

foreach ($models as $name => $content) {
    file_put_contents(__DIR__ . "/app/Models/{$name}.php", $content);
}
echo "Models updated successfully.";
