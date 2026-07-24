<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DeviceAttendanceLog;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DeviceAttendanceController extends Controller
{
    /**
     * Endpoint for the attendance device webhook or API push.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();

            // If the device sends a single object or an array of objects
            $records = isset($data['data']) ? $data['data'] : (is_array($data) && !isset($data['id']) ? $data : [$data]);

            foreach ($records as $record) {
                // Ensure required fields
                if (!isset($record['user_id']) || !isset($record['punch_time'])) {
                    continue; // Skip invalid records
                }

                // Parse the punch time
                $punchTime = Carbon::parse($record['punch_time']);
                $dateString = $punchTime->format('Y-m-d');
                $timeString = $punchTime->format('H:i:s');

                // 1. Log the raw punch
                DeviceAttendanceLog::create([
                    'device_sn'      => $record['device_sn'] ?? null,
                    'device_user_id' => $record['user_id'],
                    'punch_time'     => $punchTime,
                    'status'         => $record['status'] ?? null,
                    'verify_mode'    => $record['verify_mode'] ?? null,
                    'full_name'      => $record['full_name'] ?? null,
                ]);

                // 2. Find matching employee
                $employee = Employee::where('device_user_id', $record['user_id'])->first();

                if ($employee) {
                    // Update or create daily attendance record
                    $attendance = Attendance::firstOrNew([
                        'employee_id' => $employee->id,
                        'attendance_date' => $dateString,
                    ]);

                    $attendance->source = 'device';
                    $attendance->biometric_device_id = $record['device_sn'] ?? null;
                    
                    // Determine if it's check-in or check-out based on existing data
                    if (!$attendance->check_in) {
                        $attendance->check_in = $timeString;
                        $attendance->status = 'present';
                    } else {
                        // If there is already a check-in, subsequent punches might be check-out
                        // Only update check-out if this punch is later than the existing check-in
                        $existingIn = Carbon::parse($dateString . ' ' . $attendance->check_in);
                        if ($punchTime->gt($existingIn)) {
                            // If we already have a check out, we update it to the latest punch
                            // OR we just keep the latest punch of the day as check out
                            $attendance->check_out = $timeString;
                        }
                    }

                    // Calculate hours worked if both are present
                    if ($attendance->check_in && $attendance->check_out) {
                        $in = Carbon::parse($dateString . ' ' . $attendance->check_in);
                        $out = Carbon::parse($dateString . ' ' . $attendance->check_out);
                        $attendance->hours_worked = round($out->diffInMinutes($in) / 60, 2);
                    }

                    $attendance->save();
                }
            }

            return response()->json(['success' => true, 'message' => 'Attendance logged successfully'], 200);

        } catch (\Exception $e) {
            Log::error('Device Attendance API Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Internal Server Error'], 500);
        }
    }
}
