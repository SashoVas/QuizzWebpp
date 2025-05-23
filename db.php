<?php
$host = 'localhost';
$db = 'test_system';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>
