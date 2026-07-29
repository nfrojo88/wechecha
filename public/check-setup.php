<?php

echo "<h2>Construct-Pro ERP Diagnostic & Storage Fix Script</h2>";

// 1. Check and create storage directories
$directories = [
    '../storage/app',
    '../storage/framework',
    '../storage/framework/sessions',
    '../storage/framework/views',
    '../storage/framework/cache',
    '../storage/logs',
    '../bootstrap/cache'
];

foreach ($directories as $dir) {
    if (!file_exists(__DIR__ . '/' . $dir)) {
        if (mkdir(__DIR__ . '/' . $dir, 0755, true)) {
            echo "<p style='color:green;'>✅ Created missing folder: $dir</p>";
        } else {
            echo "<p style='color:red;'>❌ Failed to create folder: $dir. Please create it manually in cPanel File Manager.</p>";
        }
    } else {
        echo "<p style='color:blue;'>ℹ️ Directory exists: $dir</p>";
    }
}

// 2. Test DB Connection
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "<p style='color:green;'>✅ Database connection successful!</p>";
} catch (\Exception $e) {
    echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
}
