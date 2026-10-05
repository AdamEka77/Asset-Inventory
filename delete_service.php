<?php
include 'config.php';
include 'service_helper.php';
service_require_access();

// Hapus lewat POST (bukan GET) supaya tidak bisa terpicu lewat link/prefetch
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = mysqli_prepare($conn, "DELETE FROM service WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}
header("Location: service.php");
exit;