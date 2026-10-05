<?php
session_name("asset");
session_start();
include 'config.php'; // Koneksi database

// Pastikan user sudah login
if (!isset($_SESSION["username"])) {
    echo "<script>alert('Harap login terlebih dahulu!'); window.location='login.php';</script>";
    exit;
}

$username = $_SESSION["username"]; // User yang sedang login

// Ambil data berdasarkan ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Pastikan ID berupa angka
    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        echo "<script>alert('Data tidak ditemukan!'); window.location='asset.php';</script>";
        exit;
    }

    $created_by = $row["created_by"]; // Ambil pembuat asli
}

// Proses update data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_barang = $_POST['nama_barang'];
    $kategori_barang = $_POST['kategori_barang'];
    $no_asset = $_POST['no_asset'];
    $ruangan = $_POST['ruangan'];
    $deskripsi = $_POST['deskripsi'];

    // Jika user yang mengedit berbeda dengan pembuat asli, ubah "dibuat oleh"
    if ($username !== $created_by) {
        $created_by = $username;
    }

    // Cek apakah ada file baru yang diupload
    if (!empty($_FILES['foto_barang']['name'])) {
        $foto_barang = $_FILES['foto_barang']['name'];
        $target = "uploads/" . basename($foto_barang);
        move_uploaded_file($_FILES['foto_barang']['tmp_name'], $target);

        // Update data termasuk foto
        $query = "UPDATE items SET 
          foto_barang=?, nama_barang=?, kategori_barang=?, 
          no_asset=?, ruangan=?, deskripsi=?, created_by=?, created_at=NOW() 
          WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssssi", $foto_barang, $nama_barang, $kategori_barang, $no_asset, $ruangan, $deskripsi, $created_by, $id);
    } else {
        // Update tanpa mengubah foto
        $query = "UPDATE items SET 
          nama_barang=?, kategori_barang=?, no_asset=?, 
          ruangan=?, deskripsi=?, created_by=?, created_at=NOW() 
          WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssi", $nama_barang, $kategori_barang, $no_asset, $ruangan, $deskripsi, $created_by, $id);
    }

    if ($stmt->execute()) {
        echo "<script>
        alert('Data berhasil diperbarui!');
        window.location='asset.php?kategori=" . urlencode($kategori_barang) . "';
    </script>";
    } else {
        echo "<script>alert('Gagal memperbarui data!'); window.history.back();</script>";
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
                <img id="previewImage" src="uploads/<?php echo $row['foto_barang']; ?>" class="img-thumbnail mt-2"
                    width="150" alt=""> <br>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Barang</label>
                <input type="text" name="nama_barang" class="form-control" value="<?php echo $row['nama_barang']; ?>"
                    required>
            </div>

            <div class="mb-3">
                <label class="form-label">Kategori Barang</label>
                <select name="kategori_barang" class="form-select" required>
                    <option value="Notebook" <?php if ($row['kategori_barang'] == "Notebook")
                        echo "selected"; ?>>Notebook
                    </option>
                    <option value="PC" <?php if ($row['kategori_barang'] == "PC")
                        echo "selected"; ?>>PC</option>
                    <option value="Printer" <?php if ($row['kategori_barang'] == "Printer")
                        echo "selected"; ?>>Printer
                    </option>
                    <option value="Meja" <?php if ($row['kategori_barang'] == "Meja")
                        echo "selected"; ?>>Meja
                    </option>
                    <option value="Kursi" <?php if ($row['kategori_barang'] == "Kursi")
                        echo "selected"; ?>>Kursi
                    </option>
                    <option value="Rak Lemari" <?php if ($row['kategori_barang'] == "Rak Lemari")
                        echo "selected"; ?>>Rak Lemari
                    </option>
                    <option value="Monitor" <?php if ($row['kategori_barang'] == "Monitor")
                        echo "selected"; ?>>Monitor
                    </option>
                    <option value="ETC" <?php if ($row['kategori_barang'] == "ETC")
                        echo "selected"; ?>>ETC
                    </option>
                    <option value="Phone Fax" <?php if ($row['kategori_barang'] == "Phone Fax")
                        echo "selected"; ?>>Phone
                        Fax
                    </option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">No Asset</label>
                <input type="text" name="no_asset" class="form-control" value="<?php echo $row['no_asset']; ?>"
                    required>
            </div>
            <div class="mb-3">
                <label class="form-label">Ruangan</label>
                <input type="text" name="ruangan" class="form-control" value="<?php echo $row['ruangan']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control"><?php echo $row['deskripsi']; ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="asset.php" class="btn btn-secondary">Batal</a>
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