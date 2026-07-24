<?php
namespace App\Services;

use App\Models\ErpPlanHeader;
use App\Models\ErpPlanTask;
use App\Models\PlanBaseline;

class ErpPlanService {
    public function createHeader(array $data) {
        return ErpPlanHeader::create($data);
    }
    public function addTask(ErpPlanHeader $header, array $data) {
        $data['plan_header_id'] = $header->id;
        return ErpPlanTask::create($data);
    }
    public function createBaseline(ErpPlanHeader $header, string $name, ?string $description = null) {
        $snapshot = $header->tasks()->with(['dependencies', 'resources'])->get()->toArray();
        return PlanBaseline::create([
            'plan_header_id' => $header->id,
            'name' => $name,
            'description' => $description,
            'snapshot_data' => $snapshot,
            'created_by' => auth()->id()
        ]);
    }
}
