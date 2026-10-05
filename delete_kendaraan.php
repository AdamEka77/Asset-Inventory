<?php
include 'config.php';

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM kendaraan WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: kendaraan.php");
        exit();
    } else {
        echo "Gagal menghapus data.";
    }
}
?>