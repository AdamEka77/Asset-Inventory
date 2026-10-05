<?php
include 'config.php';
include 'service_helper.php';
service_require_access();
date_default_timezone_set('Asia/Jakarta');

$errors = [];
$data = ['tanggal_servis' => date('Y-m-d')];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    list($data, $errors) = service_collect_post();

    if (empty($errors)) {
        $created_at = date("Y-m-d H:i:s");
        $created_by = $_SESSION['username'];

        $stmt = mysqli_prepare($conn, "INSERT INTO service (
            kategori_servis, tanggal_servis, jenis_kendaraan, plat, km_awal,
            lokasi_ac, merk_ac, jenis_pekerjaan, bengkel, biaya,
            created_at, created_by
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssssssss",
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
            $created_at,
            $created_by
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: service.php");
            exit;
        }
        $errors[] = "Gagal menyimpan: " . mysqli_error($conn);
    }
}

$judul = "Form Input Data Service";
$tombol = "Simpan";
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Input Data Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <?php include 'service_form.php'; ?>
    </div>
</body>

</html>