<?php
/**
 * Emergency Route Cache Clearer
 * 
 * This script clears Laravel's route cache to fix the 
 * "Route [slip-sequences.create] not defined" error.
 * 
 * HOW TO USE:
 * 1. Access this file in your browser: https://www.wechechaconstruction.et/clear-route-cache.php
 * 2. Click "Clear All Caches"
 * 3. DELETE THIS FILE after use for security
 */

$baseDir = dirname(__DIR__);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Clear Laravel Route Cache</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-top: 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .success {
            padding: 15px;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            border-radius: 4px;
            margin: 20px 0;
        }
        .error {
            padding: 15px;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            border-radius: 4px;
            margin: 20px 0;
        }
        .warning {
            padding: 15px;
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            border-radius: 4px;
            margin: 20px 0;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        ul {
            line-height: 1.8;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>🔧 Clear Laravel Route Cache</h1>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cleared = [];
            $errors = [];
            
            try {
                // Clear route cache
                $routeCacheFile = $baseDir . '/bootstrap/cache/routes-v7.php';
                if (file_exists($routeCacheFile)) {
                    if (unlink($routeCacheFile)) {
                        $cleared[] = 'Route cache (routes-v7.php)';
                    }
                }
                
                // Clear config cache
                $configCacheFile = $baseDir . '/bootstrap/cache/config.php';
                if (file_exists($configCacheFile)) {
                    if (unlink($configCacheFile)) {
                        $cleared[] = 'Config cache (config.php)';
                    }
                }
                
                // Clear application cache (if file-based)
                $cacheDir = $baseDir . '/storage/framework/cache/data';
                if (is_dir($cacheDir)) {
                    $files = glob($cacheDir . '/*');
                    $count = 0;
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            if (unlink($file)) {
                                $count++;
                            }
                        }
                    }
                    if ($count > 0) {
                        $cleared[] = "Application cache ($count files)";
                    }
                }
                
                // Clear view cache
                $viewCacheDir = $baseDir . '/storage/framework/views';
                if (is_dir($viewCacheDir)) {
                    $files = glob($viewCacheDir . '/*');
                    $count = 0;
                    foreach ($files as $file) {
                        if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                            if (unlink($file)) {
                                $count++;
                            }
                        }
                    }
                    if ($count > 0) {
                        $cleared[] = "View cache ($count files)";
                    }
                }
                
                echo '<div class="success">';
                echo '<strong>✅ Success!</strong> Cleared the following caches:<ul>';
                foreach ($cleared as $item) {
                    echo '<li>' . htmlspecialchars($item) . '</li>';
                }
                echo '</ul>';
                echo '<p><strong>Your slip-sequences routes should now work correctly.</strong></p>';
                echo '<p><a href="/store-manager/slip-sequences">→ Go to Slip Sequences</a></p>';
                echo '</div>';
                
                echo '<div class="warning">';
                echo '<strong>⚠️ Important:</strong> For security, delete this file now:<br>';
                echo '<code>public/clear-route-cache.php</code>';
                echo '</div>';
                
            } catch (Exception $e) {
                echo '<div class="error">';
                echo '<strong>❌ Error:</strong> ' . htmlspecialchars($e->getMessage());
                echo '</div>';
            }
        } else {
            echo '<p>This will clear the following Laravel caches:</p>';
            echo '<ul>';
            echo '<li><strong>Route cache</strong> - fixes route not found errors</li>';
            echo '<li><strong>Config cache</strong> - ensures fresh configuration</li>';
            echo '<li><strong>Application cache</strong> - clears cached data</li>';
            echo '<li><strong>View cache</strong> - clears compiled views</li>';
            echo '</ul>';
            
            echo '<div class="warning">';
            echo '<strong>Why this is needed:</strong><br>';
            echo 'Laravel cached old routes before the <code>store-manager</code> prefix was added. ';
            echo 'Clearing the cache will fix the "Route [slip-sequences.create] not defined" error.';
            echo '</div>';
            
            echo '<form method="POST">';
            echo '<button type="submit" class="btn">Clear All Caches</button>';
            echo '</form>';
        }
        ?>
        
    </div>
</body>
</html>
