<?php
/**
 * ZKTeco ADMS Heartbeat Handler — iclock/getrequest.php
 *
 * The ZKTeco device pings this endpoint every ~30 seconds to confirm it
 * is still connected to the server. The server must respond "OK".
 *
 * Device sends: GET /iclock/getrequest.php?SN=XXXX
 * Server responds: OK
 */

// ── 1. Bootstrap Laravel ────────────────────────────────────────────────────
$laravelBase = dirname(__DIR__, 2);
require $laravelBase . '/vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once $laravelBase . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// ── 2. Log heartbeat ─────────────────────────────────────────────────────────
$logFile = __DIR__ . '/adms.log';
$sn = '';
foreach ($_GET as $k => $v) {
    if (strtolower($k) === 'sn') $sn = trim($v);
}

$line = '[' . date('Y-m-d H:i:s') . '] HEARTBEAT | SN: ' . $sn . PHP_EOL;
file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);

// ── 3. Update device last-seen timestamp ─────────────────────────────────────
if (!empty($sn)) {
    try {
        DB::table('zk_devices')->updateOrInsert(
            ['serial_number' => $sn],
            ['last_seen_at' => now(), 'updated_at' => now()]
        );
    } catch (\Exception $e) {
        // Table may not exist yet — just ignore
        file_put_contents(
            $logFile,
            '[' . date('Y-m-d H:i:s') . '] NOTE: zk_devices update skipped: ' . $e->getMessage() . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }
}

// ── 4. Respond OK ─────────────────────────────────────────────────────────────
header('Content-Type: text/plain');
echo "OK";
exit;
