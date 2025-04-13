<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

// Cek apakah pengguna sudah login dan memiliki role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../views/login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $role = 'admin'; // Set role sebagai admin

    // Cek apakah username sudah ada
    $checkStmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $checkStmt->execute(['username' => $username]);

    if ($checkStmt->rowCount() > 0) {
        $message = "Username sudah digunakan!";
    } else {
        // Menyimpan data admin baru ke database
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
        if ($stmt->execute(['username' => $username, 'password' => $password, 'role' => $role])) {
            $message = 'Admin berhasil ditambahkan.';
        } else {
            $message = 'Gagal menambahkan admin.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Tambah Admin</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Admin</button>
            <a href="../views/dashboard.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>