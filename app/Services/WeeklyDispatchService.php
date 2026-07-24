<?php
namespace App\Services;

use App\Models\WeeklyPlanDispatch;
use App\Models\WeeklyPlanDispatchTask;

class WeeklyDispatchService {
    public function createDispatch(array $data) {
        return WeeklyPlanDispatch::create($data);
    }
    public function dispatchTask(WeeklyPlanDispatch $dispatch, array $data) {
        $data['dispatch_id'] = $dispatch->id;
        return WeeklyPlanDispatchTask::create($data);
    }
}
