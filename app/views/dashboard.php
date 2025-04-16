<?php

session_start();

require_once __DIR__ . '/../../config/config.php';

// Cek apakah pengguna sudah login dan memiliki role admin

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {

header('Location: login.php');

exit;

}


// Proses Tambah Handphone
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $model = $_POST['model'];
    $harga = $_POST['harga'];
    $diskon = !empty($_POST['diskon']) ? $_POST['diskon'] : 0; // Set diskon ke 0 jika tidak diisi
    $stok = $_POST['stok'];

    // Cek apakah model sudah ada
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM iphones WHERE model = :model");
    $stmt->execute(['model' => $model]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Jika model sudah ada, tampilkan pesan kesalahan
        $error_message = "Model '$model' sudah digunakan. Silahkan gunakan nama model yang berbeda.";
    } else {
        // Menyiapkan dan mengeksekusi query untuk menambah data
        $stmt = $pdo->prepare("INSERT INTO iphones (model, harga, diskon, stok) VALUES (:model, :harga, :diskon, :stok)");
        $stmt->execute(['model' => $model, 'harga' => $harga, 'diskon' => $diskon, 'stok' => $stok]);

        // Redirect kembali ke halaman yang sama setelah menambah
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Proses Edit Handphone

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {

$id = $_POST['id'];

$model = $_POST['model'];

$harga = $_POST['harga'];

$diskon = !empty($_POST['diskon']) ? $_POST['diskon'] : 0; // Set diskon ke 0 jika tidak diisi

$stok = $_POST['stok'];

// Menyiapkan dan mengeksekusi query untuk mengedit data

$stmt = $pdo->prepare("UPDATE iphones SET model = :model, harga = :harga, diskon = :diskon, stok = :stok WHERE id = :id");

$stmt->execute(['model' => $model, 'harga' => $harga, 'diskon' => $diskon, 'stok' => $stok, 'id' => $id]);

// Redirect kembali ke halaman yang sama setelah edit

header("Location: " . $_SERVER['PHP_SELF']);

exit();

}



// Proses Hapus Handphone
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Hapus handphone tanpa menghapus transaksi yang terkait
    $stmt = $pdo->prepare("DELETE FROM iphones WHERE id = :id");
    $stmt->execute(['id' => $id]);

    // Redirect kembali ke halaman yang sama
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Mengambil data handphone

$stmt = $pdo->query("SELECT * FROM iphones");

$iphones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mengambil data transaksi

// Mengambil data transaksi
$stmt = $pdo->query("SELECT * FROM transaksi ORDER BY tanggal DESC");
$transaksi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body {

background-color: #1e2124;

color: white;

}

.table {

background-color: white; /* Latar tabel putih */

}

.navbar {

background-color: #343a40; /* Navbar gelap */

}

.navbar-dark .navbar-brand, .navbar-dark .navbar-nav .nav-link {

color: white; /* Teks putih di navbar */

}

.btn-danger {

background-color: #dc3545; /* Tombol logout merah */

}

.table {

background-color: #1e1e1e !important;

color: #f8f9fa !important;

}

.table th, .table td {

background-color: #1e1e1e !important;

border-color: #343a40 !important;

color: #f8f9fa !important;

}

.table-striped > tbody > tr:nth-of-type(odd) {

background-color: #2a2a2a !important;

}

.table-hover tbody tr:hover {

background-color: #333 !important;

}
/* Modifikasi untuk modal */
.modal-content {
    background-color: #1e2124; /* Latar belakang modal */
    color: #f8f9fa; /* Warna teks dalam modal */
}
.form-label {
    color: #f8f9fa; /* Warna teks label */
}
.form-control {
    background-color: #2a2a2a; /* Latar belakang input */
    color: #f8f9fa;  /* Warna teks input */
    border: 1px solid #343a40; /* Gaya border input */
}
.form-control:focus {
    background-color: #2a2a2a; /* Latar belakang input saat fokus */
    border-color: #80bdff; /* Warna border saat fokus */
    color: #f8f9fa; /* Warna teks saat fokus */
}
</style>

</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark">

<div class="container-fluid">

<a class="navbar-brand" href="#">Dashboard Admin</a>

<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">

<span class="navbar-toggler-icon"></span>

</button>

<div class="collapse navbar-collapse" id="navbarNav">

<ul class="navbar-nav ms-auto">

<li class="nav-item">
<a href="../controllers/TambahAdmin.php" class="btn btn-success me-3">Tambah Admin</a>

</li>

<li class="nav-item">

<a href="logout.php" class="btn btn-danger">Logout</a>

</li>

</ul>

</div>

</div>

</nav>

<div class="container mt-5">

<h2 class="mt-4">Daftar Mobil</h2>
<?php if (isset($error_message)): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($error_message) ?>
    </div>
<?php endif; ?>

<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addPhoneModal">Tambah Mobil</button>

<!-- Modal untuk menambahkan mobil -->
<div class="modal fade" id="addPhoneModal" tabindex="-1" aria-labelledby="addPhoneModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPhoneModalLabel">Tambah Mobil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="model" class="form-label">Model</label>
                        <input type="text" class="form-control" id="model" name="model" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="harga" name="harga" required>
                    </div>
                    <div class="mb-3">
                        <label for="diskon" class="form-label">Diskon (%) (Opsional)</label>
                        <input type="number" class="form-control" id="diskon" name="diskon" min="0" max="100">
                    </div>
                    <div class="mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" required>
                    </div>
                    <button type="submit" name="add" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<table class="table table-bordered table-striped mt-4">
    <thead>
        <tr>
            <th>No</th>
            <th>ID</th>
            <th>Model</th>
            <th>Harga</th>
            <th>Diskon (%)</th>
            <th>Harga Setelah Diskon</th> <!-- Kolom baru -->
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($iphones as $index => $iphone): ?>
        <tr>
            <td><?= $index + 1 ?></td> <!-- Menambahkan nomor urut -->
            <td><?= $iphone['id'] ?></td>
            <td><?= htmlspecialchars($iphone['model']) ?></td>
            <td>Rp <?= number_format($iphone['harga'], 0, ',', '.') ?></td>
            <td><?= $iphone['diskon'] ?>%</td>
            <td>
                <?php
                // Hitung Harga Setelah Diskon
                $hargaSetelahDiskon = $iphone['harga'] - ($iphone['harga'] * ($iphone['diskon'] / 100));
                ?>
                Rp <?= number_format($hargaSetelahDiskon, 0, ',', '.') ?>
            </td>
            <td><?= $iphone['stok'] ?></td>
            <td>
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editPhoneModal<?= $iphone['id'] ?>">Edit</button>
                <a href="?delete=<?= $iphone['id'] ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus?');">Hapus</a>
            </td>
        </tr>
        <!-- Modal Edit -->
        <div class="modal fade" id="editPhoneModal<?= $iphone['id'] ?>" tabindex="-1" aria-labelledby="editPhoneModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPhoneModalLabel">Edit Handphone</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $iphone['id'] ?>">
                            <div class="mb-3">
                                <label for="model" class="form-label">Model</label>
                                <input type="text" class="form-control" id="model" name="model" value="<?= htmlspecialchars($iphone['model']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="harga" class="form-label">Harga</label>
                                <input type="number" class="form-control" id="harga" name="harga" value="<?= $iphone['harga'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="diskon" class="form-label">Diskon (%) (Opsional)</label>
                                <input type="number" class="form-control" id="diskon" name="diskon" value="<?= $iphone['diskon'] ?>" min="0" max="100">
                            </div>
                            <div class="mb-3">
                                <label for="stok" class="form-label">Stok</label>
                                <input type="number" class="form-control" id="stok" name="stok" value="<?= $iphone['stok'] ?>" required>
                            </div>
                            <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </tbody>
</table>
<div class="container mt-4">
    
    <a href="transaksi.php" class="btn btn-primary">Lihat Daftar Transaksi</a>


</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
