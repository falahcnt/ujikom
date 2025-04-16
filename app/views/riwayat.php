<?php
session_start();
require_once '../../config/config.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: /jual_iphone/app/views/login.php'); // Pastikan jalur ini benar
    exit;
}

// Ambil riwayat pembelian dari database
$stmt = $pdo->prepare("SELECT * FROM purchases WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembelian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1e2124; /* Warna latar belakang gelap */
            color: white; /* Warna teks putih */
        }
        .table {
            background-color: #000; /* Warna latar tabel hitam */
            border: 1px solid #444; /* Tambahkan border untuk kontras */
        }
        .table th, .table td {
            color: white; /* Warna teks tabel putih */
            vertical-align: middle; /* Rata tengah vertikal */
            background-color: #000; /* Warna latar belakang sel tabel hitam */
        }
        .table th {
            background-color: #343a40; /* Warna latar header tabel */
        }
        .table-striped tbody tr {
            background-color: #23272b; /* Warna sel genap */
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #1f2124; /* Warna sel ganjil */
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Riwayat Pembelian Anda</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Model</th>
                <th>Harga Sebelum Diskon</th>
                <th>Diskon (%)</th>
                <th>Harga Setelah Diskon</th>
                <th>Nama Pembeli</th>
                <th>Metode Pembayaran</th>
                <th>Tanggal Pembelian</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($purchases)): ?>
                <tr>
                    <td colspan="7" class="text-center">Tidak ada riwayat pembelian.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($purchases as $purchase): ?>
                    <tr>
                        <td><?= htmlspecialchars($purchase['model']) ?></td>
                        <td>Rp <?= number_format($purchase['harga_asli'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($purchase['diskon']) ?>%</td>
                        <td>Rp <?= number_format($purchase['harga_diskon'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($purchase['nama_pembeli']) ?></td>
                        <td><?= htmlspecialchars($purchase['metode']) ?></td>
                        <td><?= htmlspecialchars($purchase['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="../../index.php" class="btn btn-primary">Kembali ke Beranda</a>
</div>
</body>
</html>
