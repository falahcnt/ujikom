<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

// Cek jika pengguna sudah login
if (isset($_SESSION['user_id'])) {
    // Jika pengguna sudah login, arahkan ke home (atau ke dashboard jika ingin)
    header('Location: home.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $role = 'user'; // Default role untuk pendaftar

    // Menyimpan data pengguna ke database
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");

    // Cek apakah username sudah ada
    $checkStmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $checkStmt->execute(['username' => $username]);

    if ($checkStmt->rowCount() > 0) {
        $error = "Username sudah digunakan!";
    } else {
        if ($stmt->execute(['username' => $username, 'password' => $password, 'role' => $role])) {
            header("Location: login.php"); // Arahkan ke halaman login setelah berhasil daftar
            exit();
        } else {
            $error = "Terjadi kesalahan saat mendaftar!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Registrasi</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
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
            <button type="submit" class="btn btn-primary">Daftar</button>
            <div class="mt-3">
                Sudah punya akun? <a href="login.php">Login</a>
            </div>
        </form>
    </div>
</body>
</html>