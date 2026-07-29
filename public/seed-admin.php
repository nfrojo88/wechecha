<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

try {
    Illuminate\Support\Facades\Artisan::call('db:seed', [
        '--class' => 'Database\Seeders\RolesAndPermissionsSeeder',
        '--force' => true,
    ]);
    Illuminate\Support\Facades\Artisan::call('db:seed', [
        '--class' => 'Database\Seeders\AdminUserSeeder',
        '--force' => true,
    ]);

    echo "<h2 style='color: green;'>Success! Admin user has been created/updated in the new database.</h2>";
    echo "<p><strong>Email:</strong> fro@wechecha.com</p>";
    echo "<p><strong>Password:</strong> password</p>";
    echo "<br><a href='/'>Go to Login Page</a>";
} catch (\Exception $e) {
    echo "<h2 style='color: red;'>Error seeding database:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
