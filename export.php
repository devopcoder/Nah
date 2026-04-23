<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="victims_' . date('Y-m-d_H-i-s') . '.csv"');

// Same DB config
$pdo = new PDO("mysql:host=localhost;dbname=pentest_db", "pentest_user", "your_secure_password");

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Name', 'Age', 'Email', 'IP', 'Geo', 'Reason', 'Timestamp']);

$stmt = $pdo->query("SELECT * FROM victims ORDER BY timestamp DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        $row['id'],
        $row['real_name'],
        $row['age'],
        $row['email'],
        $row['ip'],
        $row['geo'],
        substr($row['reason'], 0, 100),
        $row['timestamp']
    ]);
}
?>
