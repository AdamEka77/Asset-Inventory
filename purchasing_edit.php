<?php
session_name("asset");
session_start();
include 'config.php';

// Atur zona waktu ke Asia/Jakarta
date_default_timezone_set('Asia/Jakarta');

// Cek role user
if (!isset($_SESSION["role"]) || ($_SESSION["role"] !== "owner" && $_SESSION["role"] !== "admin")) {
    echo "Akses ditolak!";
    exit;
}

// Pastikan user yang login tersedia
if (!isset($_SESSION["username"])) {
    echo "Akses ditolak! User tidak dikenali.";
    exit;
}

$current_user = $_SESSION["username"]; // User yang sedang login

// Pastikan ID dikirim dan valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID tidak valid!'); window.location='purchasing.php';</script>";
    exit;
}

$id = intval($_GET['id']); // Konversi ID ke integer untuk keamanan

// Ambil data berdasarkan ID
$stmt = $conn->prepare("SELECT * FROM purchasing WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='purchasing.php';</script>";
    exit;
}

// Jika form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_barang = trim($_POST['nama_barang']);
    $deskripsi = trim($_POST['deskripsi']);

    // Cek apakah user ingin mengubah quantity
    $quantity_change = isset($_POST['quantity_change']) ? intval($_POST['quantity_change']) : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : "";

    if ($action == "tambah") {
        $new_quantity = $row['quantity'] + $quantity_change;
    } elseif ($action == "kurang") {
        $new_quantity = max(0, $row['quantity'] - $quantity_change); // Hindari negatif
    } else {
        $new_quantity = $row['quantity'];
    }

    // Cek apakah ada file gambar baru
    if (!empty($_FILES['foto_barang']['name'])) {
        $foto_barang = basename($_FILES['foto_barang']['name']);
        $target = "uploads/" . $foto_barang;
        move_uploaded_file($_FILES['foto_barang']['tmp_name'], $target);
    } else {
        $foto_barang = $row['foto_barang']; // Gunakan foto lama
    }

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Perbarui data di purchasing + ubah "dibuat oleh" jika editor berbeda dari pembuat awal
        $stmt = $conn->prepare("UPDATE purchasing SET foto_barang=?, nama_barang=?, quantity=?, deskripsi=?, created_at=NOW(), created_by=? WHERE id=?");
        if ($stmt === false) {
            throw new Exception("Query UPDATE purchasing gagal dipersiapkan: " . $conn->error);
        }

        $stmt->bind_param("ssissi", $foto_barang, $nama_barang, $new_quantity, $deskripsi, $current_user, $id);
        if (!$stmt->execute()) {
            throw new Exception("Eksekusi UPDATE purchasing gagal: " . $stmt->error);
        }

        // Perbarui log perubahan di purchasing_log
        $stmt = $conn->prepare("UPDATE purchasing_log SET updated_at=NOW(), updated_by=? WHERE id=?");
        if ($stmt === false) {
            throw new Exception("Query UPDATE purchasing_log gagal dipersiapkan: " . $conn->error);
        }

        $stmt->bind_param("si", $current_user, $id);
        if (!$stmt->execute()) {
            throw new Exception("Eksekusi UPDATE purchasing_log gagal: " . $stmt->error);
        }

        $conn->commit();
        echo "<script>alert('Data berhasil diperbarui!'); window.location='purchasing.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Gagal memperbarui data: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}
?>



<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center">Edit Item</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Foto Barang</label><br>
                <input type="file" name="foto_barang" class="form-control" id="imageInput">
                <img id="previewImage" src="uploads/<?php echo htmlspecialchars($row['foto_barang']); ?>"
                    class="img-thumbnail mt-2" width="150" alt="Preview">
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Barang</label>
                <input type="text" name="nama_barang" class="form-control"
                    value="<?php echo htmlspecialchars($row['nama_barang']); ?>" required>
            </div>





            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control"
                    required><?php echo htmlspecialchars($row['deskripsi']); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="purchasing.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>

<br>
<br>

    <script>
        document.getElementById("imageInput").addEventListener("change", function (event) {
            let reader = new FileReader();
            reader.onload = function () {
                document.getElementById("previewImage").src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        });
    </script>
</body>

</html>