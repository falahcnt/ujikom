<?php
session_start();
require_once __DIR__ . '/config/config.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: app/views/login.php');
    exit;
}

// Cek apakah ada transaksi yang tersimpan untuk ditampilkan
if (isset($_SESSION['transaction'])) {
    $transaction = $_SESSION['transaction'];
    // Menampilkan rincian pembelian
    echo "<div class='alert alert-success mt-3'>";
    echo "<strong>Rincian Pembelian:</strong><br>";
    echo "Model: " . htmlspecialchars($transaction['model']) . "<br>";
    echo "Harga Sebelum Diskon: Rp " . number_format($transaction['harga_asli'], 0, ',', '.') . "<br>";
    echo "Diskon: " . htmlspecialchars($transaction['diskon']) . "%<br>";
    echo "Harga Setelah Diskon: Rp " . number_format($transaction['harga_diskon'], 0, ',', '.') . "<br>";
    echo "Nama Pembeli: " . htmlspecialchars($transaction['nama_pembeli']) . "<br>";
    echo "Metode Pembayaran: " . htmlspecialchars($transaction['metode']) . "<br>";
    // Tambahkan tombol untuk melihat struk
    echo "<a href='struk.php' class='btn btn-primary mt-3'>Lihat Rincian Pembelian (Struk)</a>";
    echo "</div>";

    // Hapus transaksi dari sesi setelah ditampilkan
    unset($_SESSION['transaction']);
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
                <a  href="app/views/riwayat.php" class="btn btn-success me-3">Riwayat Pembelian</a>
                </li>
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
                            <a href="#" class="btn btn-success" onclick="confirmPurchase(<?= $iphone['id'] ?>)">Beli</a>

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
        window.location.href = "app/views/payment.php?id=" + id;

    }
}
</script>
</body>
</html>
