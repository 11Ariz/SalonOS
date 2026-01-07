<?php
$host = '127.0.0.1';
$port = '3306'; // This matches your XAMPP screenshot
$db   = 'salon_system';
$user = 'root';
$pass = ''; 

try {
    // Notice the addition of port=3306 in the DSN string
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>