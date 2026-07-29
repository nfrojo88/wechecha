<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Construct-Pro ERP Diagnostic Script</h2>";

// 1. Check directories
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
    $fullPath = __DIR__ . '/' . $dir;
    if (!file_exists($fullPath)) {
        mkdir($fullPath, 0755, true);
        echo "<p style='color:green;'>✅ Created missing folder: $dir</p>";
    } else {
        $writable = is_writable($fullPath) ? "<span style='color:green;'>(Writable)</span>" : "<span style='color:red;'>(NOT Writable)</span>";
        echo "<p style='color:blue;'>ℹ️ Directory exists: $dir $writable</p>";
    }
}

// 2. Boot Laravel properly and test DB + Migrations
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Bootstrap HTTP kernel so facades work properly
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();

    echo "<p style='color:green;'>✅ Laravel booted successfully!</p>";

    // Test PDO connection
    $pdo = Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "<p style='color:green;'>✅ Connected to database: <strong>" . Illuminate\Support\Facades\DB::connection()->getDatabaseName() . "</strong></p>";

    // Run migrations safely skipping existing tables
    echo "<h3>Running Database Migrations & Admin Seeder...</h3>";
    
    // Check if migrations table exists, if not run migrate
    Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo "<pre style='background:#f4f4f4; padding:10px;'>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";

    Illuminate\Support\Facades\Artisan::call('db:seed', [
        '--class' => 'Database\Seeders\RolesAndPermissionsSeeder',
        '--force' => true,
    ]);
    Illuminate\Support\Facades\Artisan::call('db:seed', [
        '--class' => 'Database\Seeders\AdminUserSeeder',
        '--force' => true,
    ]);
    echo "<p style='color:green; font-weight:bold; font-size: 18px;'>🎉 Migrations & Admin User setup complete!</p>";
    echo "<p><strong>Email:</strong> fro@wechecha.com</p>";
    echo "<p><strong>Password:</strong> password</p>";
    echo "<p><a href='/' style='background:#10b981; color:white; padding:10px 15px; text-decoration:none; border-radius:5px; display:inline-block; margin-top:10px;'>Go to Login Page</a></p>";

} catch (\Exception $e) {
    echo "<h3 style='color:red;'>Error details:</h3>";
    echo "<pre style='background:#ffebee; color:#c62828; padding:15px; border-radius:5px;'>" . $e->getMessage() . "\n\n" . $e->getTraceAsString() . "</pre>";
}
