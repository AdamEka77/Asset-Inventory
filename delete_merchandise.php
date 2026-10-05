<?php
include 'config.php';

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM merchandise WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: merchandise.php");
exit;
