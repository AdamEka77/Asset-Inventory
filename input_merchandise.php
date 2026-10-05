<?php
session_name("asset");
session_start();
include 'config.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Input Merchandise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-4">
        <h2 class="mb-4">Form Input Merchandise</h2>

        <div class="card mb-4">
            <div class="card-body">
                <form action="save_merchandise.php" method="POST" class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">No. File (Opsional)</label>
                        <input type="text" name="no_file" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Hari/Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">User</label>
                        <input type="text" name="user" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Jumlah Merchandise</label>
                        <input type="number" name="jumlah" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Keperluan</label>
                        <textarea name="keperluan" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="col-12 d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="merchandise.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</body>

</html>