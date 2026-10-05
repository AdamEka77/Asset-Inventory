<?php
session_name("asset");
session_start();
include 'config.php';

if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'] ?? '';
$kategori = $_GET['kategori'] ?? 'all';

if ($id) {
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: asset.php?kategori=" . urlencode($kategori));
exit;
