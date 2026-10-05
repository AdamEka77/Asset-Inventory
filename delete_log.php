<?php
session_start();
include 'config.php';

// Cek apakah ada ID log
if (!isset($_GET['id'])) {
    header("Location: atur_quantity.php");
    exit;
}

$id = intval($_GET['id']);

// Hapus log tanpa mengubah stok
$del = $conn->prepare("DELETE FROM purchasing_log WHERE id=?");
$del->bind_param("i", $id);

if ($del->execute()) {
    echo "<script>alert('Log berhasil dihapus (stok tidak berubah)!'); window.location='atur_quantity.php';</script>";
} else {
    echo "Gagal menghapus log: " . $conn->error;
}
