<?php
/**
 * SMS Ethiopia API Test Script
 * 
 * This script tests the SMS Ethiopia API connection and shows detailed error information.
 * DELETE THIS FILE after testing for security!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$apiKey = '6FSIE6UXV4S79DXA3GZDSJSBFWEEYV42';
$senderId = '1408';
$baseUrl = 'https://smsethiopia.com/api/v1';

?>
<!DOCTYPE html>
<html>
<head>
    <title>SMS Ethiopia API Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            margin-top: 0;
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
        .info {
            padding: 15px;
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
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
        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
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
            margin: 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>🔧 SMS Ethiopia API Test</h1>
        
        <div class="warning">
            <strong>⚠️ Security Warning:</strong> This file exposes sensitive API information. 
            <strong>DELETE this file immediately after testing!</strong><br>
            File location: <code>public/test-sms-api.php</code>
        </div>

        <h2>API Configuration</h2>
        <table>
            <tr>
                <th>Setting</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>Base URL</td>
                <td><code><?php echo $baseUrl; ?></code></td>
            </tr>
            <tr>
                <td>API Key</td>
                <td><code><?php echo substr($apiKey, 0, 10) . '...' . substr($apiKey, -5); ?></code></td>
            </tr>
            <tr>
                <td>Sender ID</td>
                <td><code><?php echo $senderId; ?></code></td>
            </tr>
        </table>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action']) && $_POST['action'] === 'check_balance') {
                echo '<h2>Balance Check Result</h2>';
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $baseUrl . '/balance?api_key=' . urlencode($apiKey));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For testing only
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);
                
                if ($error) {
                    echo '<div class="error">';
                    echo '<strong>❌ Connection Error:</strong><br>';
                    echo 'Error: ' . htmlspecialchars($error) . '<br>';
                    echo 'This could mean:<br>';
                    echo '• Server cannot reach smsethiopia.com<br>';
                    echo '• Firewall is blocking the connection<br>';
                    echo '• SSL/TLS certificate issue<br>';
                    echo '</div>';
                } else {
                    echo '<div class="info">';
                    echo '<strong>HTTP Status Code:</strong> ' . $httpCode . '<br>';
                    echo '<strong>Response:</strong><br>';
                    echo '<pre>' . htmlspecialchars($response) . '</pre>';
                    echo '</div>';
                    
                    if ($httpCode === 200) {
                        echo '<div class="success">✅ API is responding! Balance check successful.</div>';
                    } else {
                        echo '<div class="error">❌ API Error: HTTP ' . $httpCode . '</div>';
                    }
                }
            }
            
            if (isset($_POST['action']) && $_POST['action'] === 'send_test_sms') {
                $testPhone = $_POST['test_phone'] ?? '';
                
                if (empty($testPhone)) {
                    echo '<div class="error">❌ Please enter a phone number</div>';
                } else {
                    // Format phone
                    $phone = str_replace([' ', '-', '(', ')'], '', $testPhone);
                    $phone = ltrim($phone, '+');
                    if (substr($phone, 0, 1) === '0') {
                        $phone = '251' . substr($phone, 1);
                    }
                    if (substr($phone, 0, 3) !== '251') {
                        $phone = '251' . $phone;
                    }
                    
                    echo '<h2>Test SMS Result</h2>';
                    echo '<div class="info"><strong>Formatted Phone:</strong> ' . htmlspecialchars($phone) . '</div>';
                    
                    $testOTP = rand(100000, 999999);
                    $message = "Your Construct-Pro ERP test code is: {$testOTP}. This is a test message.";
                    
                    $postData = [
                        'api_key' => $apiKey,
                        'sender_id' => $senderId,
                        'phone' => $phone,
                        'message' => $message,
                    ];
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/send');
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For testing only
                    
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $error = curl_error($ch);
                    curl_close($ch);
                    
                    if ($error) {
                        echo '<div class="error">';
                        echo '<strong>❌ Connection Error:</strong><br>';
                        echo htmlspecialchars($error);
                        echo '</div>';
                    } else {
                        echo '<div class="info">';
                        echo '<strong>HTTP Status Code:</strong> ' . $httpCode . '<br>';
                        echo '<strong>Response:</strong><br>';
                        echo '<pre>' . htmlspecialchars($response) . '</pre>';
                        echo '</div>';
                        
                        if ($httpCode === 200) {
                            echo '<div class="success">✅ SMS sent successfully! Check phone: ' . htmlspecialchars($testPhone) . '</div>';
                            echo '<div class="info"><strong>Test OTP was:</strong> ' . $testOTP . '</div>';
                        } else {
                            echo '<div class="error">❌ SMS failed: HTTP ' . $httpCode . '</div>';
                        }
                    }
                }
            }
        }
        ?>

        <h2>Actions</h2>
        
        <form method="POST" style="margin-bottom: 20px;">
            <input type="hidden" name="action" value="check_balance">
            <button type="submit" class="btn">Check API Balance</button>
        </form>

        <form method="POST">
            <input type="hidden" name="action" value="send_test_sms">
            <div style="margin-bottom: 15px;">
                <label><strong>Test Phone Number:</strong></label><br>
                <input type="text" name="test_phone" placeholder="+251911234567 or 0911234567" 
                       style="padding: 10px; width: 300px; font-size: 16px;" required>
            </div>
            <button type="submit" class="btn">Send Test SMS</button>
        </form>

        <div class="info" style="margin-top: 30px;">
            <strong>📝 Troubleshooting Steps:</strong><br>
            1. Click "Check API Balance" to verify API connectivity<br>
            2. If balance check works, try "Send Test SMS" with your phone<br>
            3. Check if you receive the test SMS<br>
            4. If errors occur, note the HTTP status code and error message<br>
            5. Contact SMS Ethiopia support if needed
        </div>

        <div class="warning" style="margin-top: 20px;">
            <strong>🔒 After Testing:</strong><br>
            1. Note the results<br>
            2. <strong>DELETE this file: public/test-sms-api.php</strong><br>
            3. Update Laravel logs at: storage/logs/laravel.log
        </div>
    </div>
</body>
</html>
