<?php
include 'config.php';
include 'service_helper.php';
service_require_access();

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

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=data_service_" . date('Ymd_His') . ".xls");
echo "\xEF\xBB\xBF";
?>
<table border="1">
    <tr>
        <th>No</th>
        <th>Hari</th>
        <th>Tanggal Servis</th>
        <th>Kategori</th>
        <th>Jenis Kendaraan</th>
        <th>Plat</th>
        <th>KM Awal</th>
        <th>Lokasi AC</th>
        <th>Merk AC</th>
        <th>Jenis Pekerjaan</th>
        <th>Bengkel/Vendor</th>
        <th>Biaya</th>
        <th>Waktu Input</th>
        <th>Diinput oleh</th>
    </tr>
    <?php $no = 1;
    while ($r = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= service_hari($r['tanggal_servis']) ?></td>
            <td><?= htmlspecialchars($r['tanggal_servis']) ?></td>
            <td><?= htmlspecialchars($r['kategori_servis']) ?></td>
            <td><?= htmlspecialchars($r['jenis_kendaraan'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['plat'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['km_awal'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['lokasi_ac'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['merk_ac'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['jenis_pekerjaan']) ?></td>
            <td><?= htmlspecialchars($r['bengkel'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['biaya'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['created_at']) ?></td>
            <td><?= htmlspecialchars($r['created_by']) ?></td>
        </tr>
    <?php endwhile; ?>
</table>