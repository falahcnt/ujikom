<?php
session_start();
require_once '../../config/config.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: /jual_iphone/app/views/login.php');
    exit;
}

// Cek apakah ID produk diterima dari permintaan
if (!isset($_GET['id'])) {
    header('Location: /jual_iphone/app/views/index.php'); // Kembali jika tidak ada ID
    exit;
}

$id = $_GET['id'];

// Ambil detail produk dari database
$stmt = $pdo->prepare("SELECT * FROM iphones WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: /jual_iphone/app/views/index.php'); // Jika produk tidak ditemukan
    exit;
}

// Proses pembelian (misalnya dengan memeriksa stok, menghitung harga diskon, dll.)
$harga_asli = $product['harga'];
$diskon = $product['diskon'];
$harga_diskon = $harga_asli * (1 - ($diskon / 100));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Simpan detail pembayaran ke database
    $stmt = $pdo->prepare("INSERT INTO purchases (user_id, model, harga_asli, diskon, harga_diskon, nama_pembeli, metode) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'], // user_id
        $product['model'], // model produk
        $harga_asli, // harga asli
        $diskon, // diskon
        $harga_diskon, // harga setelah diskon
        $_POST['nama_pembeli'], // nama pembeli
        $_POST['metode'] // metode pembayaran
    ]);

    // Tampilkan pesan berhasil dan arahkan ke riwayat pembelian
    echo "<script>
        alert('Pembayaran berhasil!');
        window.location.href = 'riwayat.php';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Mobil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
<div class="container mt-5">
    <h2>Pembayaran Mobil</h2>
    <form method="post">
        <p>Model: <?= htmlspecialchars($product['model']) ?></p>
        <p>Harga Sebelum Diskon: Rp <?= number_format($harga_asli, 0, ',', '.') ?></p>
        <p>Diskon: <?= htmlspecialchars($diskon) ?>%</p>
        <p>Harga Setelah Diskon: Rp <?= number_format($harga_diskon, 0, ',', '.') ?></p>

        <div class="mb-3">
            <label for="nama_pembeli" class="form-label">Nama Pembeli:</label>
            <input type="text" class="form-control" name="nama_pembeli" required>
        </div>

        <div class="mb-3">
            <label for="metode" class="form-label">Metode Pembayaran:</label>
            <select name="metode" class="form-select" required>
                <option value="BCA">BCA</option>
                <option value="QRIS">QRIS</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Bayar Sekarang</button>
    </form>
</div>
</body>
</html>
