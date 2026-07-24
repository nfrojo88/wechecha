<?php
/**
 * ZKTeco ADMS Receiver — iclock/cdata.php
 *
 * This file handles all communication from the ZKTeco fingerprint/face
 * attendance device using the ADMS (Attendance Data Management System) protocol.
 *
 * ⚠️  Place this file at: public/iclock/cdata.php
 *      Device should be configured to push to: http(s)://yourdomain.com/iclock/cdata.php
 *
 * Handles:
 *   - GET  ?options=all          → Device handshake — sends back configuration
 *   - GET  ?table=ATTLOG         → Heartbeat (keep-alive ping)
 *   - POST ?table=ATTLOG         → Attendance punch push (tab-separated body)
 */

// ── 1. Bootstrap Laravel ────────────────────────────────────────────────────
// We load Laravel's bootstrap so we can use the DB and models cleanly.
$laravelBase = dirname(__DIR__, 2); // two levels up: public/ → project root
require $laravelBase . '/vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once $laravelBase . '/bootstrap/app.php';

// We need the console kernel to boot services (especially DB and config) without triggering HTTP routes.
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// ── 2. Logging helper ────────────────────────────────────────────────────────
$logFile = __DIR__ . '/adms.log';

function adms_log(string $message): void
{
    global $logFile;
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}

// ── 3. Request info ──────────────────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$body   = file_get_contents('php://input');

$sn = '';
$table = '';
$options = '';
foreach ($_GET as $k => $v) {
    $lk = strtolower($k);
    if ($lk === 'sn') $sn = trim($v);
    if ($lk === 'table') $table = trim($v);
    if ($lk === 'options') $options = trim($v);
}

adms_log("METHOD: {$method} | SN: {$sn} | TABLE: {$table} | PARAMS: " . json_encode($_GET));

if (!empty($body)) {
    adms_log("BODY: " . substr($body, 0, 500));
}

// Always respond as plain text
header('Content-Type: text/plain');

// ── 4. HANDSHAKE — Device boot / reconnect ───────────────────────────────────
// The device sends: GET /iclock/cdata.php?SN=XXXX&options=all
if ($method === 'GET' && $options === 'all') {
    adms_log("HANDSHAKE from SN: {$sn}");

    // Update device last-seen timestamp
    _updateDeviceSeen($sn);

    echo "GET OPTION FROM: {$sn}\n";
    echo "Stamp=9999\n";        // Send ALL records (no filter by timestamp)
    echo "OpStamp=9999\n";
    echo "ErrorDelay=60\n";     // Retry on error after 60 seconds
    echo "Delay=30\n";          // Heartbeat every 30 seconds
    echo "TransFlag=1111000000\n";
    echo "TransInterval=1\n";   // Push attendance every 1 minute
    echo "TransTables=ATTLOG\n";// Only send attendance logs
    exit;
}

// ── 5. HEARTBEAT — Keep-alive ping ───────────────────────────────────────────
// Device sends: GET /iclock/getrequest.php?SN=XXXX
// OR sometimes: GET /iclock/cdata.php?SN=XXXX (without options or body)
if ($method === 'GET' && empty($body)) {
    adms_log("HEARTBEAT from SN: {$sn}");
    _updateDeviceSeen($sn);
    echo "OK\n";
    exit;
}

// ── 6. ATTENDANCE PUSH — Tab-separated punch records ─────────────────────────
// Device sends: POST /iclock/cdata.php?SN=XXXX&table=ATTLOG
// Body format (tab-separated, one record per line):
//   user_id  punch_time           status  verify_mode  ...
//   1        2026-07-20 09:05:30  0       1            0  0  0  0  0  0
if ($method === 'POST' && strtoupper($table) === 'ATTLOG') {
    adms_log("ATTLOG PUSH from SN: {$sn} — parsing body...");

    $saved   = 0;
    $skipped = 0;
    $errors  = 0;

    $lines = explode("\n", trim($body));

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) {
            continue;
        }

        // Split by tab; fall back to multiple spaces
        $parts = preg_split('/\t+/', $line);
        if (count($parts) < 2) {
            $parts = preg_split('/\s{2,}/', $line);
        }

        $userId     = isset($parts[0]) ? trim($parts[0]) : '';
        $punchTime  = isset($parts[1]) ? trim($parts[1]) : '';
        $status     = isset($parts[2]) ? trim($parts[2]) : '0';
        $verifyMode = isset($parts[3]) ? trim($parts[3]) : '';

        if (empty($userId) || empty($punchTime)) {
            adms_log("SKIP — missing user_id or punch_time in line: {$line}");
            $skipped++;
            continue;
        }

        // Validate punch_time format roughly
        if (!preg_match('/\d{4}-\d{2}-\d{2}/', $punchTime)) {
            adms_log("SKIP — invalid punch_time format: {$punchTime}");
            $skipped++;
            continue;
        }

        try {
            // INSERT IGNORE — device resends same records; UNIQUE KEY prevents duplicates
            DB::table('device_attendance_logs')->insertOrIgnore([
                'device_sn'      => $sn ?: null,
                'device_user_id' => $userId,
                'punch_time'     => $punchTime,
                'status'         => $status,
                'verify_mode'    => $verifyMode ?: null,
                'full_name'      => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // Also auto-sync to attendance table if employee is mapped
            _autoSyncPunch($sn, $userId, $punchTime, $status);

            $saved++;
        } catch (\Exception $e) {
            adms_log("ERROR inserting punch — user_id={$userId} punch_time={$punchTime}: " . $e->getMessage());
            $errors++;
        }
    }

    adms_log("ATTLOG DONE — saved={$saved} skipped={$skipped} errors={$errors}");

    // MUST respond "OK" within 3 seconds or device retries
    echo "OK\n";
    exit;
}

// ── 7. Default fallback ───────────────────────────────────────────────────────
adms_log("UNKNOWN REQUEST — responding OK");
echo "OK\n";
exit;

// ── Helper: Update device last-seen ──────────────────────────────────────────
function _updateDeviceSeen(string $sn): void
{
    if (empty($sn)) return;

    try {
        DB::table('zk_devices')->updateOrInsert(
            ['serial_number' => $sn],
            ['last_seen_at' => now(), 'updated_at' => now()]
        );
    } catch (\Exception $e) {
        // Table may not exist yet — just log, don't break
        adms_log("NOTE: Could not update zk_devices (table may not exist yet): " . $e->getMessage());
    }
}

// ── Helper: Auto-sync a single punch to the attendance table ─────────────────
function _autoSyncPunch(string $sn, string $userId, string $punchTime, string $status): void
{
    try {
        // Find employee by device_user_id
        $employee = DB::table('employees')
            ->where('device_user_id', $userId)
            ->first();

        if (!$employee) {
            return; // No employee mapped to this device user ID yet
        }

        $dateStr     = substr($punchTime, 0, 10); // Y-m-d
        $timeStr     = strlen($punchTime) > 10 ? substr($punchTime, 11, 8) : '00:00:00'; // H:i:s

        // Get existing attendance record for this employee on this date
        $existing = DB::table('attendance')
            ->where('employee_id', $employee->id)
            ->where('attendance_date', $dateStr)
            ->first();

        if (!$existing) {
            // First punch of the day → check-in
            DB::table('attendance')->insert([
                'employee_id'     => $employee->id,
                'attendance_date' => $dateStr,
                'check_in'        => $timeStr,
                'check_out'       => null,
                'hours_worked'    => null,
                'status'          => 'present',
                'source'          => 'device',
                'biometric_device_id' => $sn ?: null,
                'is_approved'     => false,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        } else {
            // Subsequent punch — update check_out if this punch is later than check_in
            $checkIn = $existing->check_in;
            if ($checkIn && $timeStr > $checkIn) {
                // Calculate hours worked
                $inSecs  = strtotime($dateStr . ' ' . $checkIn);
                $outSecs = strtotime($dateStr . ' ' . $timeStr);
                $hours   = $outSecs > $inSecs ? round(($outSecs - $inSecs) / 3600, 2) : null;

                DB::table('attendance')
                    ->where('employee_id', $employee->id)
                    ->where('attendance_date', $dateStr)
                    ->update([
                        'check_out'    => $timeStr,
                        'hours_worked' => $hours,
                        'updated_at'   => now(),
                    ]);
            }
        }
    } catch (\Exception $e) {
        adms_log("ERROR in auto-sync for user_id={$userId}: " . $e->getMessage());
    }
}
