<?php
require_once __DIR__ . '/../../config/config.php';
session_start();

// Cek apakah user sudah login dan memiliki peran sebagai user
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: /login.php");
    exit;
}

$userId = $_SESSION['user']['id']; // ID user dari sesi
$iphoneId = $_POST['iphone_id']; // ID iPhone yang dibeli

// Ambil harga dari iPhone
$stmtHarga = $conn->prepare("SELECT harga FROM iphones WHERE id = ?");
$stmtHarga->execute([$iphoneId]);
$iphone = $stmtHarga->fetch(PDO::FETCH_ASSOC);

// Cek apakah harga ada
if (!$iphone) {
    echo "Produk tidak ditemukan.";
    exit;
}

$harga = $iphone['harga'];

// Cek apakah user sudah beli 1 unit
$cek = $conn->prepare("SELECT * FROM transaksi WHERE user_id = ?");
$cek->execute([$userId]);

if ($cek->rowCount() > 0) {
    echo "Kamu sudah membeli 1 unit iPhone!";
} else {
   // Tambahkan ini untuk menampilkan user_id
   echo "User ID: " . $userId; // Menampilkan User ID sebelum menyimpan transaksi

   // Simpan transaksi
   try {
       $stmt = $conn->prepare("INSERT INTO transaksi ( iphone_id, harga, tanggal) VALUES (?, ?, ?, NOW())");
       $stmt->execute([$userId, $iphoneId, $harga]);
       
       echo "Pembelian berhasil!";
   } catch (PDOException $e) {
       // Menangkap dan menampilkan error
       echo "Error: " . $e->getMessage();
   }

}
// Menampilkan daftar transaksi
$transaksiQuery = $conn->prepare("
   SELECT t.id AS transaksi_id, t.iphone_id, t.harga, t.tanggal  
FROM transaksi t 
JOIN users u ON t.user_id = u.id"); // Ganti 'user' dengan 'users'
$transaksiQuery->execute();
$transaksi = $transaksiQuery->fetchAll(PDO::FETCH_ASSOC);

echo '<pre>';
print_r($transaksi); // Melihat isi array transaksi
echo '</pre>';
?>