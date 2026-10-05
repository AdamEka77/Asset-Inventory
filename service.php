<?php
include 'config.php';
include 'service_helper.php';
service_require_access();
date_default_timezone_set('Asia/Jakarta');

$kategori = $_GET['kategori'] ?? '';

$where = [];
$params = [];
$types = '';
if (in_array($kategori, ['Kendaraan', 'AC'], true)) {
    $where[] = "kategori_servis = ?";
    $params[] = $kategori;
    $types .= 's';
}
$sql = "SELECT * FROM service" . ($where ? " WHERE " . implode(" AND ", $where) : "") . " ORDER BY tanggal_servis DESC, id DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
    <div class="container-fluid mt-5 px-4">
        <a href="dashboard.php"><button class="btn btn-danger mb-3">Kembali ke Dashboard</button></a>
        <h3 class="mb-4 text-center">Data Service</h3>
        <a href="input_service.php"><button class="btn btn-primary mb-3">Input Data</button></a>

        <form method="GET" class="mb-3">
            <div class="input-group">
                <select name="kategori" class="form-select">
                    <option value="">-- Semua Kategori --</option>
                    <option value="Kendaraan" <?= $kategori === 'Kendaraan' ? 'selected' : '' ?>>Kendaraan</option>
                    <option value="AC" <?= $kategori === 'AC' ? 'selected' : '' ?>>AC</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="service.php" class="btn btn-secondary">Reset</a>
                <a href="export_service.php?kategori=<?= urlencode($kategori) ?>" class="btn btn-success">Export
                    Excel</a>
            </div>
        </form>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="service">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Hari</th>
                            <th>Tanggal Servis</th>
                            <th>Kategori</th>
                            <th>Objek Servis</th>
                            <th>KM Awal</th>
                            <th>Jenis Pekerjaan</th>
                            <th>Bengkel/Vendor</th>
                            <th>Biaya</th>
                            <th>Waktu Input</th>
                            <th>Diinput oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="fs-6">
                        <?php $no = 1;
                        while ($row = mysqli_fetch_assoc($result)): ?>
                            <?php
                            if ($row['kategori_servis'] === 'Kendaraan') {
                                $objek = trim($row['jenis_kendaraan'] . ' - ' . $row['plat']);
                            } else {
                                $objek = $row['lokasi_ac'] ?? '';
                            }
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= service_hari($row['tanggal_servis']) ?></td>
                                <td><?= htmlspecialchars($row['tanggal_servis']) ?></td>
                                <td><?= htmlspecialchars($row['kategori_servis']) ?></td>
                                <td><?= htmlspecialchars($objek) ?></td>
                                <td><?= $row['km_awal'] !== null ? number_format($row['km_awal'], 0, ',', '.') : '-' ?></td>
                                <td><?= htmlspecialchars($row['jenis_pekerjaan']) ?></td>
                                <td><?= htmlspecialchars($row['bengkel'] ?? '-') ?></td>
                                <td><?= $row['biaya'] !== null ? 'Rp ' . number_format($row['biaya'], 0, ',', '.') : '-' ?></td>
                                <td><?= htmlspecialchars($row['created_at']) ?></td>
                                <td><?= htmlspecialchars($row['created_by']) ?></td>
                                <td>
                                    <a href="edit_service.php?id=<?= (int) $row['id'] ?>" class="btn btn-warning btn-sm mb-2"><i
                                            class="bi bi-pencil-square"></i></a>
                                    <form method="POST" action="delete_service.php" class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"><i
                                                class="bi bi-trash-fill"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">Belum ada data service yang tersimpan.</div>
        <?php endif; ?>
        <br><br><br>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>
    <script>new DataTable('#service', { order: [] });</script>
</body>

</html>