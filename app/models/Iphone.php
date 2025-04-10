<?php
class iPhone {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM iphones");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM iphones WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add($nama, $harga, $stok, $deskripsi, $gambar) {
        $stmt = $this->conn->prepare("INSERT INTO iphones (nama, harga, stok, deskripsi, gambar) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$nama, $harga, $stok, $deskripsi, $gambar]);
    }

    public function update($id, $nama, $harga, $stok, $deskripsi, $gambar) {
        $stmt = $this->conn->prepare("UPDATE iphones SET nama = ?, harga = ?, stok = ?, deskripsi = ?, gambar = ? WHERE id = ?");
        return $stmt->execute([$nama, $harga, $stok, $deskripsi, $gambar, $id]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM iphones WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
