<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ErpPlanHeader;
use App\Models\ErpPlanTaskResource;

class CoordinatorController extends Controller
{
    public function forecastDemand(Request $request)
    {
        $erpPlans = ErpPlanHeader::with('project')->latest()->get();

        $selectedPlanId = $request->query('erp_plan_id');
        $selectedPlan = null;

        $materialForecasts = [];
        $equipmentForecasts = [];
        $manpowerForecasts = [];

        if ($selectedPlanId) {
            $selectedPlan = ErpPlanHeader::with(['project', 'tasks.resources'])->find($selectedPlanId);
        }

        if ($selectedPlan) {
            foreach ($selectedPlan->tasks as $task) {
                $siteName = $selectedPlan->project ? $selectedPlan->project->name : 'N/A';
                $dateNeeded = $task->start_date ?? $selectedPlan->plan_start_date ?? now();

                $planTitle = $selectedPlan->title ?? ('Plan #' . $selectedPlan->id);
                $projectId = $selectedPlan->project_id;

                foreach ($task->resources as $res) {
                    $itemData = [
                        'site' => $siteName,
                        'project_id' => $projectId,
                        'plan_title' => $planTitle,
                        'task_name' => $task->name,
                        'item' => $res->resource_name,
                        'raw_quantity' => $res->quantity,
                        'unit' => $res->unit ?? '',
                        'required_qty' => number_format($res->quantity, 2) . ' ' . ($res->unit ?? ''),
                        'date_needed' => $dateNeeded,
                    ];

                    $type = strtolower($res->resource_type);
                    if ($type === 'material') {
                        $materialForecasts[] = $itemData;
                    } elseif ($type === 'equipment') {
                        $equipmentForecasts[] = $itemData;
                    } elseif ($type === 'manpower') {
                        $manpowerForecasts[] = $itemData;
                    }
                }
            }
        } else {
            // Fallback / default aggregate or all plans resources if no single plan selected
            $allResources = ErpPlanTaskResource::with(['task.header.project'])->get();
            foreach ($allResources as $res) {
                $task = $res->task;
                if (!$task || !$task->header) continue;
                
                $siteName = $task->header->project ? $task->header->project->name : 'N/A';
                $dateNeeded = $task->start_date ?? $task->header->plan_start_date ?? now();
                $planTitle = $task->header->title ?? ('Plan #' . $task->header->id);
                $projectId = $task->header->project_id;

                $itemData = [
                    'site' => $siteName,
                    'project_id' => $projectId,
                    'plan_title' => $planTitle,
                    'task_name' => $task->name,
                    'item' => $res->resource_name,
                    'raw_quantity' => $res->quantity,
                    'unit' => $res->unit ?? '',
                    'required_qty' => number_format($res->quantity, 2) . ' ' . ($res->unit ?? ''),
                    'date_needed' => $dateNeeded,
                ];

                $type = strtolower($res->resource_type);
                if ($type === 'material') {
                    $materialForecasts[] = $itemData;
                } elseif ($type === 'equipment') {
                    $equipmentForecasts[] = $itemData;
                } elseif ($type === 'manpower') {
                    $manpowerForecasts[] = $itemData;
                }
            }
        }

        // Fetch all existing material requests to check if request is created
        $existingRequests = \App\Models\MaterialRequest::where('source', 'like', 'Coordinator%')
            ->with(['items'])
            ->get();

        return view('coordinator.forecast', compact(
            'erpPlans',
            'selectedPlanId',
            'selectedPlan',
            'materialForecasts',
            'equipmentForecasts',
            'manpowerForecasts',
            'existingRequests'
        ));
    }
}

