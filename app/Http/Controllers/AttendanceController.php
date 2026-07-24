<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index()
    {
        $query = Attendance::with('employee')->latest('attendance_date');

        // Filter by date range
        if (request('date_from')) {
            $query->whereDate('attendance_date', '>=', request('date_from'));
        }
        if (request('date_to')) {
            $query->whereDate('attendance_date', '<=', request('date_to'));
        }

        // Filter by employee
        if (request('employee')) {
            $search = request('employee');
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('employee_code', 'like', "%$search%")
                  ->orWhere('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%");
            });
        }

        // Filter by status
        if (request('status')) {
            $query->where('status', request('status'));
        }

        $attendances = $query->paginate(30);

        return view('hr.attendance.index', compact('attendances'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->orderBy('full_name')->get();
        return view('hr.attendance.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id'     => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'status'          => 'required|in:present,absent,half_day,leave,holiday,weekend',
            'check_in'        => 'nullable|date_format:H:i',
            'check_out'       => 'nullable|date_format:H:i',
            'notes'           => 'nullable|string',
        ]);

        $hours = null;
        if ($request->check_in && $request->check_out) {
            $in    = \Carbon\Carbon::createFromFormat('H:i', $request->check_in);
            $out   = \Carbon\Carbon::createFromFormat('H:i', $request->check_out);
            $hours = round($out->diffInMinutes($in) / 60, 2);
        }

        Attendance::updateOrCreate(
            ['employee_id' => $request->employee_id, 'attendance_date' => $request->attendance_date],
            [
                'check_in'    => $request->check_in,
                'check_out'   => $request->check_out,
                'hours_worked'=> $hours,
                'status'      => $request->status,
                'source'      => 'manual',
                'notes'       => $request->notes,
                'is_approved' => true,
                'approved_by' => Auth::id(),
            ]
        );

        return redirect()->route('attendance.index')->with('success', 'Attendance recorded.');
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'attendance_date'              => 'required|date',
            'records'                      => 'required|array',
            'records.*.employee_id'        => 'required|exists:employees,id',
            'records.*.status'             => 'required|in:present,absent,half_day,leave,holiday,weekend',
        ]);

        $count = 0;
        foreach ($request->records as $rec) {
            Attendance::updateOrCreate(
                ['employee_id' => $rec['employee_id'], 'attendance_date' => $request->attendance_date],
                [
                    'status' => $rec['status'],
                    'source' => 'bulk_upload',
                    'is_approved' => true,
                    'approved_by' => Auth::id(),
                ]
            );
            $count++;
        }

        return back()->with('success', "$count attendance records saved successfully.");
    }

    public function deviceLogs()
    {
        $query = \App\Models\DeviceAttendanceLog::with('employee')->latest('punch_time');

        if (request('date_from')) {
            $query->whereDate('punch_time', '>=', request('date_from'));
        }
        if (request('date_to')) {
            $query->whereDate('punch_time', '<=', request('date_to'));
        }
        if (request('linked') === 'linked') {
            $query->whereHas('employee');
        } elseif (request('linked') === 'unlinked') {
            $query->whereDoesntHave('employee');
        }

        $logs = $query->paginate(50);
        return view('hr.attendance.device_logs', compact('logs'));
    }

    /**
     * Manually trigger ZKTeco punch → attendance sync via Artisan command.
     */
    public function syncZkteco(Request $request)
    {
        $date  = $request->input('date', now()->format('Y-m-d'));
        $force = $request->boolean('force', false);

        try {
            $args = ['--date' => $date];
            if ($force) {
                $args['--force'] = true;
            }

            Artisan::call('zkteco:sync', $args);
            $output = trim(Artisan::output());

            return redirect()
                ->route('attendance.deviceLogs')
                ->with('success', "ZKTeco sync completed for {$date}. " . ($output ? strip_tags($output) : ''));

        } catch (\Exception $e) {
            return redirect()
                ->route('attendance.deviceLogs')
                ->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Show device status page — last heartbeat per device.
     */
    public function zktecoStatus()
    {
        $devices = DB::table('zk_devices')->orderBy('last_seen_at', 'desc')->get();

        // Count unsynced punches per device
        $unsyncedCounts = DB::table('device_attendance_logs')
            ->whereNull('synced_at')
            ->selectRaw('device_sn, COUNT(*) as cnt')
            ->groupBy('device_sn')
            ->pluck('cnt', 'device_sn');

        // Total device logs today
        $todayPunches = DB::table('device_attendance_logs')
            ->whereDate('punch_time', now()->format('Y-m-d'))
            ->count();

        // Unmatched user IDs (device_user_id not in any employee)
        $unmatchedIds = DB::table('device_attendance_logs')
            ->leftJoin('employees', 'employees.device_user_id', '=', 'device_attendance_logs.device_user_id')
            ->whereNull('employees.id')
            ->distinct()
            ->pluck('device_attendance_logs.device_user_id');

        return view('hr.attendance.zkteco_status', compact(
            'devices', 'unsyncedCounts', 'todayPunches', 'unmatchedIds'
        ));
    }
}
