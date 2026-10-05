<?php
session_name("asset");
session_start();
include 'config.php';

// Cek role, hanya Owner dan Admin yang bisa menambah data
if (!isset($_SESSION["role"]) || ($_SESSION["role"] !== "owner" && $_SESSION["role"] !== "admin")) {
    echo "<script>alert('Akses ditolak!'); window.location='purchasing.php';</script>";
    exit;
}

// Jika form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_barang = trim($_POST['nama_barang']);
    $quantity = intval($_POST['quantity']);
    $deskripsi = trim($_POST['deskripsi']);
    $created_by = $_SESSION["username"];
    $created_at = date("Y-m-d H:i:s");

    // Upload gambar
    $foto_barang = "default.png"; // Gambar default jika tidak diunggah
    if (!empty($_FILES['foto_barang']['name'])) {
        $foto_barang = basename($_FILES['foto_barang']['name']);
        $target = "uploads/" . $foto_barang;

        // Cek apakah file adalah gambar
        $fileType = strtolower(pathinfo($target, PATHINFO_EXTENSION));
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($fileType, $allowedTypes)) {
            echo "<script>alert('Hanya file JPG, JPEG, PNG, dan GIF yang diperbolehkan!'); window.history.back();</script>";
            exit;
        }

        move_uploaded_file($_FILES['foto_barang']['tmp_name'], $target);
    }

    // Cek apakah barang dengan nama yang sama sudah ada (kategori dihapus)
    $stmt = $conn->prepare("SELECT id, quantity FROM purchasing WHERE nama_barang = ?");
    $stmt->bind_param("s", $nama_barang);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika barang sudah ada, update quantity
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;
        $updateStmt = $conn->prepare("UPDATE purchasing SET quantity = ?, deskripsi = ?, created_by = ?, created_at = ? WHERE id = ?");
        $updateStmt->bind_param("isssi", $new_quantity, $deskripsi, $created_by, $created_at, $row['id']);
        $updateStmt->execute();
    } else {
        // Jika barang belum ada, tambahkan data baru
        $insertStmt = $conn->prepare("INSERT INTO purchasing (foto_barang, nama_barang, quantity, deskripsi, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("ssisss", $foto_barang, $nama_barang, $quantity, $deskripsi, $created_by, $created_at);
        $insertStmt->execute();
    }

    echo "<script>alert('Data berhasil diperbarui atau ditambahkan!'); window.location='purchasing.php';</script>";
}
?>



<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Data Purchasing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center">Tambah Item Purchasing</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Foto Barang</label><br>
                <input type="file" name="foto_barang" class="form-control" id="imageInput">
                <img id="previewImage" src="uploads/default.png" class="img-thumbnail mt-2" width="150" alt="Preview">
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Barang</label>
                <input type="text" name="nama_barang" class="form-control" required>
            </div>



            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" class="form-control" required min="1">
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Tambah Barang</button>
            <a href="purchasing.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>

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