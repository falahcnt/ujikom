<?php
// register_process.php

$host = 'localhost'; // Host database
$db = 'jual_iphone'; // Nama database
$user = 'root'; // Username database
$pass = ''; // Password database


// Membuat koneksi

$conn = new mysqli($host, $user, $pass, $db); // Perbaiki urutan parameter

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari form
$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password

// Query untuk menyimpan data
$sql = "INSERT INTO users (username, password) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);

if ($stmt->execute()) {
    // Redirect ke halaman login setelah berhasil registrasi
    header( "../views/login.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>