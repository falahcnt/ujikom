<?php
$host = 'localhost'; // Host database
$db = 'jual_iphone'; // Nama database
$user = 'root'; // Username database
$pass = ''; // Password database

try {
    // Membuat koneksi PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit(); // Menghentikan eksekusi jika koneksi gagal
}

?>