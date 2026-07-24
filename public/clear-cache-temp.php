<!DOCTYPE html>
<html>
<head>
    <title>Clear Laravel Cache</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            font-weight: bold;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .btn {
            background: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Laravel Cache Clearer</h1>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
            try {
                require __DIR__.'/../vendor/autoload.php';
                $app = require_once __DIR__.'/../bootstrap/app.php';
                
                $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
                
                echo '<div class="success"><strong>✓ Running cache clear commands...</strong></div>';
                
                // Clear route cache
                echo '<pre>';
                $kernel->call('route:clear');
                echo "✓ Route cache cleared\n";
                
                // Clear application cache
                $kernel->call('cache:clear');
                echo "✓ Application cache cleared\n";
                
                // Clear view cache
                $kernel->call('view:clear');
                echo "✓ View cache cleared\n";
                
                // Clear config cache
                $kernel->call('config:clear');
                echo "✓ Config cache cleared\n";
                
                // Optimize
                $kernel->call('optimize');
                echo "✓ Application optimized\n";
                
                echo '</pre>';
                
                echo '<div class="success"><strong>✓ All caches cleared successfully!</strong></div>';
                echo '<div class="warning">⚠️ <strong>DELETE THIS FILE NOW!</strong><br>';
                echo 'For security, delete: <code>public/clear-cache-temp.php</code></div>';
                
                echo '<p>Your slip-sequences routes should now work correctly.</p>';
                echo '<p><a href="/store-manager/slip-sequences">→ Go to Slip Sequences</a></p>';
                
            } catch (Exception $e) {
                echo '<div class="error"><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        } else {
            ?>
            <div class="warning">
                ⚠️ This is a temporary maintenance tool
            </div>
            
            <h2>What this does:</h2>
            <ul>
                <li>Clears route cache</li>
                <li>Clears application cache</li>
                <li>Clears view cache</li>
                <li>Clears config cache</li>
                <li>Optimizes the application</li>
            </ul>
            
            <h2>Why this is needed:</h2>
            <p>Laravel cached old routes before the <code>store-manager</code> prefix was added. 
            Clearing the cache will fix the "Route [slip-sequences.create] not defined" error.</p>
            
            <form method="POST">
                <input type="hidden" name="confirm" value="1">
                <button type="submit" class="btn">Clear All Caches</button>
            </form>
            
            <div class="warning" style="margin-top: 20px;">
                <strong>⚠️ IMPORTANT:</strong> Delete this file immediately after use!
            </div>
            <?php
        }
        ?>
    </div>
</body>
</html>
