<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Cek ID iPhone
if (!isset($_GET['id'])) {
    die('ID iPhone tidak ditemukan!');
}

$iphone_id = $_GET['id'];

// Ambil data iPhone
$stmt = $pdo->prepare("SELECT * FROM iphones WHERE id = ?");
$stmt->execute([$iphone_id]);
$iphone = $stmt->fetch();

if (!$iphone) {
    die('iPhone tidak ditemukan!');
}

// Hitung harga diskon
$harga_diskon = $iphone['harga'] - ($iphone['harga'] * $iphone['diskon'] / 100);

// Proses pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pembeli = $_POST['nama_pembeli'];
    $metode = $_POST['metode'];

    // Insert ke transaksi
    $stmt = $pdo->prepare("INSERT INTO transaksi (user_id, iphone_id, model, harga_total, tanggal, nama_pembeli, metode_pembayaran) 
                           VALUES (?, ?, ?, ?, NOW(), ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $iphone_id,
        $iphone['model'],
        $harga_diskon,
        $nama_pembeli,
        $metode
    ]);

    // Update stok
    $pdo->prepare("UPDATE iphones SET stok = stok - 1 WHERE id = ?")->execute([$iphone_id]);

    // Tampilkan pop-up berhasil
    echo "<script>
            alert('Pembayaran berhasil!');
            window.location.href = '/jual_iphone/index.php';
          </script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function validateForm() {
            var namaPembeli = document.getElementById("nama_pembeli").value;

            // Cek jika nama pembeli tidak hanya huruf
            var regex = /^[A-Za-z\s]+$/;
            if (!regex.test(namaPembeli)) {
                alert("Nama pembeli hanya boleh huruf dan tidak mengandung angka!");
                return false; // Mencegah pengiriman formulir
            }
            return true; // Mengizinkan pengiriman formulir
        }
    </script>
</head>
<body class="bg-dark text-white">
<div class="container mt-5">
    <h2>Pembayaran Mobil</h2>
    <p><strong>Model:</strong> <?= htmlspecialchars($iphone['model']) ?></p>
    <p><strong>Harga:</strong> Rp <?= number_format($harga_diskon, 0, ',', '.') ?></p>

    <form method="POST" onsubmit="return validateForm();">
        <div class="mb-3">
            <label for="nama_pembeli" class="form-label">Nama Pembeli:</label>
            <input type="text" class="form-control" id="nama_pembeli" name="nama_pembeli" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Metode Pembayaran:</label><br>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="metode" id="bank" value="Bank Transfer (BCA)" required>
                <label class="form-check-label" for="bank">BCA</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="metode" id="qris" value="QRIS" required>
                <label class="form-check-label" for="qris">QRIS</label>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Bayar Sekarang</button>
    </form>
</div>
</body>
</html>