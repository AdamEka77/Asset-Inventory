<?php
session_name("asset");
session_start();
include 'config.php';

// Pastikan hanya Owner yang bisa menghapus user
if ($_SESSION["role"] !== "owner") {
    header("Location: users.php");
    exit;
}

// Cek apakah ID user dikirim
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID tidak valid.");
}

$id = $_GET['id'];

// Hapus user dari database
$delete = $conn->query("DELETE FROM users WHERE id='$id'");

if ($delete) {
    echo "<script>alert('User berhasil dihapus!'); window.location='users.php';</script>";
} else {
    echo "Gagal menghapus user.";
}
?>