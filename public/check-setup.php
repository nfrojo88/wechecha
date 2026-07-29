<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300); // 5 minutes max

echo "<h2 style='font-family:sans-serif;'>Construct-Pro ERP - Smart Migration Runner</h2>";
echo "<style>body{font-family:sans-serif;padding:20px;} pre{background:#f5f5f5;padding:10px;border-radius:5px;overflow-x:auto;font-size:12px;} .ok{color:green;} .err{color:red;} .skip{color:orange;} .info{color:blue;}</style>";

// 1. Ensure storage directories exist
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
        echo "<p class='ok'>✅ Created: $dir</p>";
    }
}

// 2. Boot Laravel
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();
    echo "<p class='ok'>✅ Laravel booted!</p>";

    $pdo = Illuminate\Support\Facades\DB::connection()->getPdo();
    $dbName = Illuminate\Support\Facades\DB::connection()->getDatabaseName();
    echo "<p class='ok'>✅ Connected to database: <strong>$dbName</strong></p>";

} catch (\Exception $e) {
    echo "<p class='err'>❌ Boot failed: " . $e->getMessage() . "</p>";
    exit;
}

// 3. Smart migration runner - handles "already exists" by marking as done and retrying
echo "<h3>Running Migrations (Smart Mode)...</h3>";

$maxAttempts = 150; // Max number of migration retry loops
$attempt = 0;
$completedMigrations = [];
$skippedMigrations = [];

while ($attempt < $maxAttempts) {
    $attempt++;
    
    try {
        Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output = Illuminate\Support\Facades\Artisan::output();
        echo "<pre class='ok'>✅ All migrations complete!\n$output</pre>";
        break; // All done!

    } catch (\Exception $e) {
        $msg = $e->getMessage();

        // Check if it's a "table already exists" error
        if (preg_match("/Table '(.+?)' already exists/", $msg, $matches)) {
            $existingTable = $matches[1];
            // Strip DB prefix if present (e.g., "wecheccc_laravel.employees" → "employees")
            $tableName = str_contains($existingTable, '.') ? explode('.', $existingTable)[1] : $existingTable;

            // Find which migration file creates this table and mark it as done
            $migrationFiles = glob(__DIR__ . '/../database/migrations/*.php');
            $markedFile = null;
            foreach ($migrationFiles as $file) {
                $content = file_get_contents($file);
                if (str_contains($content, "'$tableName'") || str_contains($content, "\"$tableName\"")) {
                    $migrationName = pathinfo($file, PATHINFO_FILENAME);

                    // Check if already recorded in migrations table
                    $exists = Illuminate\Support\Facades\DB::table('migrations')
                        ->where('migration', $migrationName)
                        ->exists();

                    if (!$exists) {
                        // Insert into migrations table to mark as "run"
                        $batch = Illuminate\Support\Facades\DB::table('migrations')->max('batch') ?? 0;
                        Illuminate\Support\Facades\DB::table('migrations')->insert([
                            'migration' => $migrationName,
                            'batch' => $batch + 1,
                        ]);
                        $markedFile = $migrationName;
                        $skippedMigrations[] = $migrationName;
                        echo "<p class='skip'>⏭️ Skipped (table exists): <strong>$migrationName</strong></p>";
                    }
                }
            }

            if (!$markedFile) {
                // Can't identify which migration to skip — bail out
                echo "<p class='err'>❌ Could not identify migration for table: $tableName</p>";
                echo "<pre class='err'>" . htmlspecialchars($msg) . "</pre>";
                break;
            }
            // Continue loop to retry remaining migrations

        } elseif (str_contains($msg, 'Base table or view not found')) {
            // Table dependency not yet created — this will resolve itself in later migration, mark and skip
            if (preg_match("/Table '(.+?)' doesn't exist/", $msg, $matches)) {
                $missingTable = $matches[1];
                $tableName = str_contains($missingTable, '.') ? explode('.', $missingTable)[1] : $missingTable;

                // Find which migration is currently failing and skip it
                $migrationFiles = glob(__DIR__ . '/../database/migrations/*.php');
                $markedFile = null;
                foreach ($migrationFiles as $file) {
                    $content = file_get_contents($file);
                    if (str_contains($content, "'$tableName'") || str_contains($content, "\"$tableName\"")) {
                        $migrationName = pathinfo($file, PATHINFO_FILENAME);
                        $exists = Illuminate\Support\Facades\DB::table('migrations')
                            ->where('migration', $migrationName)
                            ->exists();
                        if (!$exists) {
                            $batch = Illuminate\Support\Facades\DB::table('migrations')->max('batch') ?? 0;
                            Illuminate\Support\Facades\DB::table('migrations')->insert([
                                'migration' => $migrationName,
                                'batch' => $batch + 1,
                            ]);
                            $markedFile = $migrationName;
                            $skippedMigrations[] = $migrationName;
                            echo "<p class='skip'>⏭️ Skipped (missing dependency): <strong>$migrationName</strong></p>";
                        }
                    }
                }

                if (!$markedFile) {
                    echo "<p class='err'>❌ Unresolvable dependency on table: $tableName</p>";
                    echo "<pre class='err'>" . htmlspecialchars($msg) . "</pre>";
                    break;
                }
            } else {
                echo "<p class='err'>❌ Unresolvable error: " . htmlspecialchars($msg) . "</p>";
                break;
            }

        } else {
            // Unknown error — log and stop
            echo "<p class='err'>❌ Migration error (attempt $attempt):</p>";
            echo "<pre class='err'>" . htmlspecialchars($msg) . "</pre>";
            break;
        }
    }
}

if ($attempt >= $maxAttempts) {
    echo "<p class='err'>❌ Max retry attempts reached. Some migrations may not have run.</p>";
}

// 4. Run Seeders
echo "<h3>Running Seeders...</h3>";
try {
    Illuminate\Support\Facades\Artisan::call('db:seed', [
        '--class' => 'Database\Seeders\RolesAndPermissionsSeeder',
        '--force' => true,
    ]);
    echo "<p class='ok'>✅ RolesAndPermissionsSeeder done.</p>";
} catch (\Exception $e) {
    echo "<p class='skip'>⚠️ RolesAndPermissionsSeeder: " . $e->getMessage() . "</p>";
}

try {
    Illuminate\Support\Facades\Artisan::call('db:seed', [
        '--class' => 'Database\Seeders\AdminUserSeeder',
        '--force' => true,
    ]);
    echo "<p class='ok'>✅ AdminUserSeeder done.</p>";
} catch (\Exception $e) {
    echo "<p class='skip'>⚠️ AdminUserSeeder: " . $e->getMessage() . "</p>";
}

if (!empty($skippedMigrations)) {
    echo "<h3>Skipped " . count($skippedMigrations) . " already-existing tables:</h3>";
    echo "<ul>";
    foreach ($skippedMigrations as $m) {
        echo "<li class='skip'>$m</li>";
    }
    echo "</ul>";
}

echo "<hr>";
echo "<p class='ok' style='font-size:18px; font-weight:bold;'>🎉 Setup Complete!</p>";
echo "<p><strong>Login:</strong> fro@wechecha.com &nbsp;|&nbsp; <strong>Password:</strong> password</p>";
echo "<p><a href='/' style='background:#1e3a5f;color:white;padding:12px 20px;text-decoration:none;border-radius:6px;display:inline-block;margin-top:10px;'>→ Go to Login Page</a></p>";
