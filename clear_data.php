<?php
// UPDATE DB CREDENTIALS
$host = 'localhost';
$dbname = 'pentest_db';
$username = 'pentest_user';
$password = 'your_secure_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->exec("TRUNCATE TABLE victims");
    header('Location: admin.html?cleared=1');
} catch (Exception $e) {
    header('Location: admin.html?error=1');
}
?>
