<?php
/**
 * ZKTeco ADMS Diagnostics & Live Receiver Page
 * URL: http://wechechaconstruction.com/iclock/debug.php
 */

$logFile = __DIR__ . '/adms.log';

// Record all incoming requests that have query parameters or POST body (from ZKTeco device)
$isMachineRequest = !empty($_GET) || !empty($body) || isset($_GET['ping']) || str_contains($uri, 'iclock');

// If requested with ?ping=1, record a manual test ping
if (isset($_GET['test_ping'])) {
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[{$timestamp}] TEST PING RECEIVED SUCCESSFUL FROM BROWSER!\n\n", FILE_APPEND | LOCK_EX);
    header('Location: /iclock/debug.php');
    exit;
}

if ($isMachineRequest && !isset($_GET['view'])) {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "========================================\n";
    $logEntry .= "[{$timestamp}] INCOMING REQUEST DETECTED!\n";
    $logEntry .= "Method: {$method}\n";
    $logEntry .= "URI: {$uri}\n";
    $logEntry .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "\n";
    $logEntry .= "Headers: " . json_encode(function_exists('getallheaders') ? getallheaders() : $_SERVER) . "\n";
    $logEntry .= "GET Params: " . json_encode($_GET) . "\n";
    if (!empty($body)) {
        $logEntry .= "BODY: {$body}\n";
    }
    $logEntry .= "========================================\n\n";

    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

    header('Content-Type: text/plain');
    echo "OK\n";
    exit;
}

// ── HTML Dashboard for HR / Admin viewing ──────────────────────────────────
$logs = file_exists($logFile) ? file_get_contents($logFile) : 'No requests recorded yet.';
$logLines = array_filter(explode("\n", $logs));
$recentLogs = implode("\n", array_slice($logLines, -100));

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ZKTeco Device Live Debugger</title>
    <meta http-equiv="refresh" content="5">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0f172a; color: #f8fafc; font-family: monospace; }
        .log-box { background: #1e293b; color: #38bdf8; border: 1px solid #334155; height: 500px; overflow-y: auto; padding: 15px; border-radius: 8px; font-size: 13px; }
        .badge-live { animation: pulse 1.5s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }
    </style>
</head>
<body class="p-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><span class="badge bg-success badge-live">LIVE</span> ZKTeco Machine Connection Tester</h2>
            <div>
                <a href="/iclock/debug.php" class="btn btn-primary btn-sm">Refresh Now</a>
                <a href="/attendance/zkteco-status" class="btn btn-outline-light btn-sm ms-2">Back to ERP Dashboard</a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card bg-dark border-secondary text-white p-3">
                    <span class="text-secondary small">EXPECTED MACHINE SN</span>
                    <h4 class="text-warning mb-0">AF6P230860018</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark border-secondary text-white p-3">
                    <span class="text-secondary small">TEST ENDPOINT URL</span>
                    <h5 class="text-info mb-0" style="font-size: 14px;">http://wechechaconstruction.com/iclock/cdata.php</h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark border-secondary text-white p-3">
                    <span class="text-secondary small">LOG FILE STATUS</span>
                    <h5 class="text-success mb-0" style="font-size: 14px;"><?php echo file_exists($logFile) ? 'Active (' . round(filesize($logFile)/1024, 2) . ' KB)' : 'Waiting for first ping...'; ?></h5>
                </div>
            </div>
        </div>

        <div class="card bg-dark border-secondary">
            <div class="card-header border-secondary d-flex justify-content-between">
                <span class="text-warning font-weight-bold">📥 Live Machine Raw Incoming Request Logs (Auto-refreshes every 5s)</span>
                <small class="text-secondary">File: public/iclock/adms.log</small>
            </div>
            <div class="card-body">
                <pre class="log-box"><?php echo htmlspecialchars($recentLogs); ?></pre>
            </div>
        </div>
    </div>
</body>
</html>
