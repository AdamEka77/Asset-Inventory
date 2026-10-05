<?php
include 'config.php';
session_start();

// // Cek login & role
// if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Owner', 'Admin'])) {
//     echo "Akses ditolak!";
//     exit;
// }

$id = $_GET['id'] ?? 0;

// Ambil data log berdasarkan id
$stmt = $conn->prepare("SELECT * FROM purchasing_log WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$log = $stmt->get_result()->fetch_assoc();

if (!$log) {
    echo "Data log tidak ditemukan!";
    exit;
}

// Update deskripsi log
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keterangan = $_POST['keterangan'];

    $update = $conn->prepare("UPDATE purchasing_log SET keterangan=? WHERE id=?");
    $update->bind_param("si", $keterangan, $id);

    if ($update->execute()) {
        echo "<script>alert('Deskripsi log berhasil diperbarui');window.location='atur_quantity.php';</script>";
    } else {
        echo "Gagal update log: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Log Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">
    <h3>Edit Log Barang - Hanya Deskripsi</h3>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Nama Barang</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($log['nama_barang']) ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Aksi</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($log['aksi']) ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Jumlah Perubahan</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($log['jumlah']) ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantity Akhir</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($log['jumlah_akhir']) ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="keterangan" class="form-control"
                rows="3"><?= htmlspecialchars($log['keterangan']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="atur_quantity.php" class="btn btn-secondary">Batal</a>
    </form>
</body>

</html>