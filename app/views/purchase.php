<?php
require_once __DIR__ . '/../../config/config.php';

$receipt = false; // Inisialisasi variabel receipt
$iphone = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data produk dari database
    $stmt = $pdo->prepare("SELECT * FROM iphones WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $iphone = $stmt->fetch(PDO::FETCH_ASSOC);

    // Cek jika produk ditemukan
    if ($iphone) {
        // Mengurangi stok
        $stmt = $pdo->prepare("UPDATE iphones SET stok = stok - 1 WHERE id = :id AND stok > 0");
        $stmt->execute(['id' => $id]);

        // Hitung harga setelah diskon
        $hargaAwal = $iphone['harga'];
        $diskon = $iphone['diskon'];
        $hargaDiskon = $hargaAwal - ($hargaAwal * ($diskon / 100)); // Hitung harga setelah diskon

        // Menyimpan transaksi ke dalam tabel transaksi
        $stmt = $pdo->prepare("INSERT INTO transaksi (iphone_id, model, harga_total, tanggal) VALUES (:iphone_id, :model, :harga_total, NOW())");

        $stmt->execute([
            'iphone_id' => $iphone['id'],
            'model' => $iphone['model'],
            'harga_total' => $hargaDiskon // Pastikan ini adalah harga setelah diskon
        ]);

        $receipt = true; // Set variabel receipt setelah simpan berhasil
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <?php if ($receipt && $iphone): ?>
            <h2>Struk Pembelian</h2>
            <div class="card p-3">
                <h5>Model: <?= htmlspecialchars($iphone['model']) ?></h5>
                <p>Harga Awal: Rp <?= number_format($hargaAwal, 0, ',', '.') ?></p>
                <p>Diskon: <?= $diskon ?>%</p>
                <p>Harga Setelah Diskon: Rp <?= number_format($hargaDiskon, 0, ',', '.') ?></p>
                <a href="/jual_iphone/index.php" class="btn btn-primary mt-3">Kembali ke Home</a>
            </div>
        <?php else: ?>
            <h2>Produk tidak ditemukan atau stok habis.</h2>
            <a href="/jual_iphone/index.php" class="btn btn-primary mt-3">Kembali ke Home</a>
        <?php endif; ?>
    </div>
</body>
</html>