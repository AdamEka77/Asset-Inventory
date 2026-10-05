<?php
include 'config.php';
include 'service_helper.php';
service_require_access();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = mysqli_prepare($conn, "SELECT * FROM service WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$data) {
    header("Location: service.php");
    exit;
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    list($data, $errors) = service_collect_post();

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "UPDATE service SET
            kategori_servis=?, tanggal_servis=?, jenis_kendaraan=?, plat=?, km_awal=?,
            lokasi_ac=?, merk_ac=?, jenis_pekerjaan=?, bengkel=?, biaya=?
            WHERE id=?");

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssssssi",
            $data['kategori_servis'],
            $data['tanggal_servis'],
            $data['jenis_kendaraan'],
            $data['plat'],
            $data['km_awal'],
            $data['lokasi_ac'],
            $data['merk_ac'],
            $data['jenis_pekerjaan'],
            $data['bengkel'],
            $data['biaya'],
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: service.php");
            exit;
        }
        $errors[] = "Gagal menyimpan: " . mysqli_error($conn);
    }
}

$judul = "Edit Data Service";
$tombol = "Update";
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Data Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <?php include 'service_form.php'; ?>
    </div>
</body>

</html>