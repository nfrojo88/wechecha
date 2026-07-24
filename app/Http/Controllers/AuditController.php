<?php

namespace App\Http\Controllers;

use App\Models\ActivityTimeLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index()
    {
        $timeLogs = ActivityTimeLog::with('user')->latest()->limit(500)->get();
        // Since spatie/laravel-activitylog handles standard audit logs, we would normally fetch them here too.
        // For now, we will display the custom time logs.
        return view('admin.audit.index', compact('timeLogs'));
    }
}
