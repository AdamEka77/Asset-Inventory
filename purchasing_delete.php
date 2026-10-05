<?php
session_name("asset");
session_start();
include 'config.php';

// Pastikan hanya Admin atau Owner yang bisa menghapus data
if (!isset($_SESSION["role"]) || ($_SESSION["role"] !== "owner" && $_SESSION["role"] !== "admin")) {
    echo "<script>alert('Akses ditolak!'); window.location.href='purchasing.php';</script>";
    exit;
}

// Pastikan parameter 'id' tersedia dan valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID tidak valid!'); window.location.href='purchasing.php';</script>";
    exit;
}

$id = intval($_GET['id']); // Konversi ID ke integer

// Ambil nama file gambar untuk dihapus
$stmt = $conn->prepare("SELECT foto_barang FROM purchasing WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $file_path = "uploads/" . $row['foto_barang'];

    // Hapus gambar dari folder jika ada (selain default.png)
    if ($row['foto_barang'] !== "default.png" && file_exists($file_path)) {
        unlink($file_path);
    }

    // Hapus data dari database berdasarkan ID
    $deleteStmt = $conn->prepare("DELETE FROM purchasing WHERE id = ?");
    $deleteStmt->bind_param("i", $id);

    if ($deleteStmt->execute()) {
        echo "<script>alert('Data berhasil dihapus!'); window.location.href='purchasing.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!'); window.location.href='purchasing.php';</script>";
    }
} else {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='purchasing.php';</script>";
}
?>