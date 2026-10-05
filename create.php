<?php
include 'config.php';
session_name("asset");
session_start();

// Cek role, hanya Owner dan Admin yang bisa menambah data
if (!isset($_SESSION["role"]) || ($_SESSION["role"] !== "owner" && $_SESSION["role"] !== "admin")) {
    echo "<script>alert('Akses ditolak!'); window.location='purchasing.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_barang = $_POST['nama_barang'];
    $kategori_barang = $_POST['kategori_barang'];
    $no_asset = $_POST['no_asset'];
    $ruangan = $_POST['ruangan'];
    $deskripsi = $_POST['deskripsi'];
    $created_by = $_SESSION["username"];
    $created_at = date("Y-m-d H:i:s");

    // Proses upload foto
    $foto_barang = "";
    if (!empty($_FILES['foto_barang']['name'])) {
        $foto_barang = time() . "_" . $_FILES['foto_barang']['name']; // Hindari nama duplikat
        $target = "uploads/" . basename($foto_barang);
        move_uploaded_file($_FILES['foto_barang']['tmp_name'], $target);
    }

    // Masukkan data ke database
    $stmt = $conn->prepare("INSERT INTO items (foto_barang, nama_barang, kategori_barang, no_asset, ruangan, deskripsi, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $foto_barang, $nama_barang, $kategori_barang, $no_asset, $ruangan, $deskripsi, $created_by, $created_at);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location='asset.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data!'); window.history.back();</script>";
    }
}
?>






<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center">Tambah Item</h2>
        <form action="create.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Foto Barang</label>
                <input type="file" name="foto_barang" class="form-control" id="imageInput">
                <img id="previewImage"
                    src="uploads/<?= !empty($row['foto_barang']) ? $row['foto_barang'] : 'default.png'; ?>"
                    class="img-thumbnail mt-2" width="150" alt="">
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Barang</label>
                <input type="text" name="nama_barang" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Kategori Barang</label>
                <select name="kategori_barang" class="form-select" required>
                    <option value="Notebook">Notebook</option>
                    <option value="PC">PC</option>
                    <option value="Printer">Printer</option>
                    <option value="Meja">Meja</option>
                    <option value="Kursi">Kursi</option>
                    <option value="Rak Lemari">Rak Lemari</option>
                    <option value="Monitor">Monitor</option>
                    <option value="ETC">ETC</option>
                    <option value="Phone Fax">Phone Fax</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">No Asset</label>
                <input type="text" name="no_asset" class="form-control" required>
            </div>
        <div class="mb-3">
            <label class="form-label">Ruangan</label>
            <input type="text" name="ruangan" class="form-control" required>
        </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
	<a href="asset.php" class="btn btn-secondary">Batal</a>
        </form>

    </div>

<br>
<br>


    <script>
        document.getElementById("imageInput").addEventListener("change", function (event) {
            let reader = new FileReader();
            reader.onload = function () {
                let img = document.getElementById("previewImage");
                img.src = reader.result;
                img.classList.remove("d-none");
            }
            reader.readAsDataURL(event.target.files[0]);
        });
    </script>
</body>

</html>