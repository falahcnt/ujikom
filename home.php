<?php
session_start();
require_once __DIR__ . '/config/config.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: app/views/login.php');
    exit;
}

// Mengambil data handphone termasuk stok
$stmt = $pdo->query("SELECT * FROM iphones");
$iphones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1e2124;
            color: white;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            background: #23272a;
            color: white;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Halo Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?>!</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="app/views/logout.php" class="btn btn-danger">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">   

    <div class="row">
        <?php
        if (empty($iphones)) {
            echo "<p class='text-center'>Tidak ada iPhone tersedia.</p>";
        } else {
            foreach ($iphones as $iphone):
                // Cek jika stok lebih dari 0
                if ($iphone['stok'] > 0): ?>
                    <div class="col-md-4">
                        <div class="card p-3">
                            <h5><?= htmlspecialchars($iphone['model']) ?></h5>
                            <p>Rp <?= number_format($iphone['harga'], 0, ',', '.') ?></p>
                            <p>Diskon: <?= $iphone['diskon'] ?>%</p>
                            <p>Stok: <?= $iphone['stok'] ?></p> <!-- Menampilkan stok -->
                            <a href="#" class="btn btn-primary" onclick="confirmPurchase(<?= $iphone['id'] ?>)">Beli</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="col-md-4">
                        <div class="card p-3">
                            <h5><?= htmlspecialchars($iphone['model']) ?></h5>
                            <p>Rp <?= number_format($iphone['harga'], 0, ',', '.') ?></p>
                            <p class="text-danger">Barang telah habis terjual</p>
                        </div>
                    </div>
                <?php endif;
            endforeach;
        } ?>
    </div>
</div>

<script>
function confirmPurchase(id) {
    if (confirm("Anda yakin ingin membeli produk ini?")) {
        window.location.href = "app/views/purchase.php?id=" + id;
    }
}
</script>
</body>
</html>