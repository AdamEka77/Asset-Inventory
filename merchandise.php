<?php
session_name("asset");
session_start();
include 'config.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Merchandise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css">
</head>

<body>
    <div class="container py-4">
        <a href="purchasing.php" class="btn btn-danger">Kembali</a>
        <h2 class="mb-3 text-center">Data Merchandise</h2>
        <center>
            <div class="mb-3">
                <a href="input_merchandise.php" class="btn btn-primary">+ Tambah Data</a>
                <a href="file/Form_Order_Merchandise_Sumato.pdf" class="btn btn-danger">Print Dokumen</a>
            </div>
        </center>
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered align-middle" id="merchandise">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>No.File</th>
                            <th>Tanggal</th>
                            <th>User</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Keperluan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM merchandise ORDER BY id DESC");
                        if ($result->num_rows > 0) {
                            $no = 1; // mulai nomor urut
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>$no</td>
                                    <td>" . ($row['no_file'] ?: "-") . "</td>
                                    <td>{$row['tanggal']}</td>
                                    <td>{$row['user']}</td>
                                    <td>{$row['nama_barang']}</td>
                                    <td>{$row['jumlah']}</td>
                                    <td>{$row['keperluan']}</td>
                                    <td>
                                        <a href='edit_merchandise.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                        <a href='delete_merchandise.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin hapus data ini?\")'>Delete</a>
                                    </td>
                                </tr>";
                                $no++; // naikkan nomor
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>Belum ada data</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>
    <script>
        new DataTable('#merchandise');
    </script>
</body>

</html>