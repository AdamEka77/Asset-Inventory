<?php
include 'config.php';
date_default_timezone_set('Asia/Jakarta');


// Ambil data kendaraan dari database
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';

if (!empty($kategori)) {
    $kategori = mysqli_real_escape_string($conn, $kategori);
    $query = "SELECT * FROM kendaraan WHERE jenis_kendaraan = '$kategori' ORDER BY id DESC";
} else {
    $query = "SELECT * FROM kendaraan ORDER BY id DESC";
}

$result = mysqli_query($conn, $query);


?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <a href="dashboard.php"><button class="btn btn-danger mb-3">Kembali ke Dashboard</button></a>
        <h3 class="mb-4 text-center">Data Kendaraan</h3>
        <a href="input_kendaraan.php"><button class="btn btn-primary mb-3">Input Data</button></a>


        <!-- Form Filter Kategori -->
        <form method="GET" class="mb-3">
            <div class="input-group">
                <select name="kategori" class="form-select">
                    <option value="">-- Semua Kategori --</option>
                    <option value="Mobil" <?= isset($_GET['kategori']) && $_GET['kategori'] == 'Mobil' ? 'selected' : '' ?>>Mobil</option>
                    <option value="Motor" <?= isset($_GET['kategori']) && $_GET['kategori'] == 'Motor' ? 'selected' : '' ?>>Motor</option>
                    <!-- Tambah kategori lain jika ada -->
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="kendaraan.php" class="btn btn-secondary">Reset</a>
                <a href="export_kendaraan.php?kategori=<?= urlencode($_GET['kategori'] ?? '') ?>"
                    class="btn btn-success">Export Excel</a>
            </div>
        </form>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="kendaraan">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Hari</th>
                            <th>Tanggal</th>
                            <th>Jenis Kendaraan</th>
                            <th>Plat</th>
                            <th>KM Awal</th>
                            <th>Tujuan</th>
                            <th>User</th>
                            <th>Keterangan/Keperluan</th>
                            <th>Waktu Input</th>
                            <th>Diinput oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="fs-6">
                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)):
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['hari']) ?></td>
                                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                                <td class="jenis-kendaraan"><?= htmlspecialchars($row['jenis_kendaraan']) ?></td>
                                <td><?= htmlspecialchars($row['plat']) ?></td>
                                <td><?= htmlspecialchars($row['km_awal']) ?></td>
                                <td><?= htmlspecialchars($row['tujuan']) ?></td>
                                <td><?= htmlspecialchars($row['user']) ?></td>
                                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                <td><?= htmlspecialchars($row['created_at']) ?></td>
                                <td><?= htmlspecialchars($row['created_by']) ?></td> 
                                <td>
                                    <a href="edit_kendaraan.php?id=<?= $row['id']; ?>"
                                        class="btn btn-warning btn-sm mb-3">Edit</a>
                                    <a href="delete_kendaraan.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">Belum ada data kendaraan yang tersimpan.</div>
        <?php endif; ?>

        <br><br><br>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>
    <script>new DataTable('#kendaraan');</script>

    <script>
        function filterJenisKendaraan() {
            const selected = document.getElementById("filterJenis").value.toLowerCase();
            const rows = document.querySelectorAll("#kendaraan tbody tr");

            rows.forEach(row => {
                const jenis = row.querySelector(".jenis-kendaraan").textContent.toLowerCase();
                row.style.display = (selected === "" || jenis === selected) ? "" : "none";
            });
        }
    </script>

</body>

</html>