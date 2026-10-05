<?php
session_name("asset");
session_start();
include 'config.php';

date_default_timezone_set('Asia/Jakarta');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

    if ($_POST['action'] == "tambah_barang") {
        $nama_barang = trim($_POST['nama_barang']);
        $kategori_barang = trim($_POST['kategori_barang']);
        $quantity = intval($_POST['quantity']);
        $deskripsi = trim($_POST['deskripsi']);
        $keterangan = trim($_POST['keterangan']); // ✅ Ambil keterangan dari form
        $created_by = $_SESSION["username"];
        $created_at = date("Y-m-d H:i:s");

        // Upload gambar
        $foto_barang = "default.png";
        if (!empty($_FILES['foto_barang']['name'])) {
            $foto_barang = basename($_FILES['foto_barang']['name']);
            $target = "uploads/" . $foto_barang;
            $fileType = strtolower(pathinfo($target, PATHINFO_EXTENSION));
            $allowedTypes = ["jpg", "jpeg", "png", "gif"];
            if (!in_array($fileType, $allowedTypes)) {
                echo "<script>alert('Hanya file JPG, JPEG, PNG, dan GIF yang diperbolehkan!'); window.history.back();</script>";
                exit;
            }
            move_uploaded_file($_FILES['foto_barang']['tmp_name'], $target);
        }

        // Cek apakah barang sudah ada
        $stmt = $conn->prepare("SELECT id, quantity FROM purchasing WHERE nama_barang = ?");
        $stmt->bind_param("s", $nama_barang);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $jumlah_awal = $row['quantity'];
            $jumlah_akhir = $jumlah_awal + $quantity;

            $updateStmt = $conn->prepare("UPDATE purchasing SET kategori_barang = ?, quantity = ?, deskripsi = ?, foto_barang = ?, created_by = ?, created_at = ? WHERE id = ?");
            $updateStmt->bind_param("sissssi", $kategori_barang, $jumlah_akhir, $deskripsi, $foto_barang, $created_by, $created_at, $row['id']);
            $updateStmt->execute();
        } else {
            $jumlah_awal = 0;
            $jumlah_akhir = $quantity;

            $insertStmt = $conn->prepare("INSERT INTO purchasing (foto_barang, nama_barang, kategori_barang, quantity, deskripsi, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insertStmt->bind_param("sssssss", $foto_barang, $nama_barang, $kategori_barang, $quantity, $deskripsi, $created_by, $created_at);
            $insertStmt->execute();
        }

        // ✅ Simpan log perubahan ke purchasing_log (dengan keterangan)
        $aksi = "tambah";
        $logStmt = $conn->prepare("INSERT INTO purchasing_log (nama_barang, foto_barang, aksi, keterangan, jumlah_awal, jumlah, jumlah_akhir, updated_by, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $logStmt->bind_param("ssssiiiss", $nama_barang, $foto_barang, $aksi, $keterangan, $jumlah_awal, $quantity, $jumlah_akhir, $created_by, $created_at);
        $logStmt->execute();

        echo "<script>alert('Data berhasil ditambahkan!'); window.location='purchasing.php';</script>";

    } elseif ($_POST['action'] == "update_quantity") {
        $nama_barang = $_POST['nama_barang'];
        $aksi = $_POST['aksi'];
        $jumlah = intval($_POST['jumlah']);
        $keterangan = isset($_POST['keterangan']) ? trim($_POST['keterangan']) : null; // ✅ Ambil keterangan
        $updated_by = $_SESSION["username"];
        $updated_at = date("Y-m-d H:i:s");

        $stmt = $conn->prepare("SELECT id, quantity, foto_barang FROM purchasing WHERE nama_barang = ?");
        $stmt->bind_param("s", $nama_barang);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $jumlah_awal = $row['quantity'];

            $jumlah_akhir = ($aksi == "tambah") ? $jumlah_awal + $jumlah : $jumlah_awal - $jumlah;

            // Update ke tabel purchasing
            $updateStmt = $conn->prepare("UPDATE purchasing SET quantity = ?, created_by = ?, created_at = ? WHERE id = ?");
            $updateStmt->bind_param("issi", $jumlah_akhir, $updated_by, $updated_at, $row['id']);
            $updateStmt->execute();

            // ✅ Simpan log ke purchasing_log (dengan keterangan)
            $logStmt = $conn->prepare("INSERT INTO purchasing_log (nama_barang, foto_barang, aksi, keterangan, jumlah_awal, jumlah, jumlah_akhir, updated_by, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $logStmt->bind_param("ssssiiiss", $nama_barang, $row['foto_barang'], $aksi, $keterangan, $jumlah_awal, $jumlah, $jumlah_akhir, $updated_by, $updated_at);
            $logStmt->execute();
        }
    }
}
?>



<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Quantity Purchasing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">

        <a href="purchasing.php"><button class="btn btn-danger"><i
                    class="bi bi-caret-left-fill"></i>Kembali</button></a>

        <?php if ($_SESSION["role"] !== "user"): ?>
            <h2 class="text-center">Atur Quantity Barang</h2>
            <div class="alert alert-danger" role="alert">
                Jangan merefresh halaman! Karna akan mengulang proses pengubahan data
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_quantity">
                <div class="mb-3">
                    <label class="form-label">Nama Barang</label>
                    <div id="stokBarang" class="text-muted"></div>
                    <input type="text" id="searchBarang" name="nama_barang" class="form-control" autocomplete="off" required>
                    <div id="suggestionList"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label d-block">Aksi</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="aksi" value="tambah" id="aksiTambah"
                                required>
                            <label class="form-check-label" for="aksiTambah">Tambah</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="aksi" value="kurang" id="aksiKurang"
                                required>
                            <label class="form-check-label" for="aksiKurang">Kurang</label>
                        </div>
                    </div>
                </div>


                <div class="mb-3">
                    <label class="form-label">Jumlah</label>
                    <input type="number" name="jumlah" class="form-control" required min="1">
                </div>
                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan / Alasan Perubahan</label>
                    <textarea class="form-control" name="keterangan" id="keterangan" rows="3" required></textarea>
                </div>


                <button type="submit" class="btn btn-primary">Update Quantity</button>
            </form>
        <?php endif; ?>


        <h2 class="mt-5">Log Perubahan Quantity</h2>
        <table class="table table-bordered bg-dark" id="table">
            <thead class="table-dark">
                <tr>
                    <th>Gambar</th>
                    <th>Nama Barang</th>
                    <th>Jumlah Awal</th>
                    <th>Jumlah Diedit</th>
                    <th>Aksi</th>
                    <th>Keterangan</th>
                    <th>Jumlah Akhir</th>
                    <th>Diubah Oleh</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $logResult = $conn->query("SELECT * FROM purchasing_log ORDER BY updated_at DESC");
                while ($log = $logResult->fetch_assoc()) {
                    echo "<tr>
                <td><img src='uploads/{$log['foto_barang']}' width='50' height='50' class='img-thumbnail'></td>
                <td>{$log['nama_barang']}</td>
                <td>{$log['jumlah_awal']}</td>
                <td>{$log['jumlah']}</td>
                <td>" . ucfirst($log['aksi']) . "</td>
                <td>{$log['keterangan']}</td>
                <td>{$log['jumlah_akhir']}</td>
                <td>{$log['updated_by']}</td>
                <td>{$log['updated_at']}</td>
                 <td>
                <a href='edit_log.php?id={$log['id']}' class='btn btn-warning btn-sm mb-2'><i class='bi bi-pencil-square'></i> Edit</a>
                <a href='delete_log.php?id={$log['id']}' class='btn btn-danger btn-sm mb-2' onclick='return confirm(\"Yakin hapus log ini?\")'><i class='bi bi-trash'></i></a>
            </td>
            </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function () {
            $("#searchBarang").keyup(function () {
                let query = $(this).val();
                if (query !== "") {
                    $.post("search_barang.php", { query: query }, function (data) {
                        $("#suggestionList").html(data).fadeIn();
                    });
                } else {
                    $("#suggestionList").fadeOut();
                }
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            // Ketika user mengetik di input search
            $("#searchBarang").keyup(function () {
                let query = $(this).val();
                if (query !== "") {
                    $.post("search_barang.php", { query: query }, function (data) {
                        $("#suggestionList").html(data).fadeIn();
                    });
                } else {
                    $("#suggestionList").fadeOut();
                }
            });

            // Ketika user memilih barang dari list
            $(document).on("click", ".pilih-barang", function () {
                let namaBarang = $(this).data("nama");
                let stokBarang = $(this).data("quantity");

                $("#searchBarang").val(namaBarang); // Masukkan nama barang ke input
                $("#suggestionList").fadeOut(); // Hilangkan list setelah dipilih

                // Menampilkan stok barang
                $("#stokBarang").text("Stok saat ini: " + stokBarang);
            });

            // Sembunyikan list saat klik di luar
            $(document).click(function (event) {
                if (!$(event.target).closest("#searchBarang, #suggestionList").length) {
                    $("#suggestionList").fadeOut();
                }
            });
        });
    </script>

    <!-- Asset plugin datatables -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
    <script>new DataTable('#table');</script>

</body>

</html>