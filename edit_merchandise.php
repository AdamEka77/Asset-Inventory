<?php
include 'config.php';
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM merchandise WHERE id=$id");
$data = $result->fetch_assoc();

if (!$data) {
    die("Data tidak ditemukan!");
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Merchandise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-4">
        <h2 class="mb-4">Edit Data Merchandise</h2>

        <div class="card">
            <div class="card-body">
                <form action="" method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">No. File (Opsional)</label>
                        <input type="text" name="no_file" class="form-control" value="<?= $data['no_file'] ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Hari/Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= $data['tanggal'] ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">User</label>
                        <input type="text" name="user" class="form-control" value="<?= $data['user'] ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" value="<?= $data['nama_barang'] ?>"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Jumlah Merchandise</label>
                        <input type="number" name="jumlah" class="form-control" value="<?= $data['jumlah'] ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Keperluan</label>
                        <textarea name="keperluan" class="form-control" rows="2"><?= $data['keperluan'] ?></textarea>
                    </div>

                    <div class="col-12 d-flex justify-content-between">
                        <a href="merchandise.php" class="btn btn-secondary">Kembali</a>
                        <button type="submit" name="update" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>

<?php
if (isset($_POST['update'])) {
    $no_file = $_POST['no_file'];
    $tanggal = $_POST['tanggal'];
    $user = $_POST['user'];
    $nama_barang = $_POST['nama_barang'];
    $jumlah = $_POST['jumlah'];
    $keperluan = $_POST['keperluan'];

    $stmt = $conn->prepare("UPDATE merchandise SET no_file=?, tanggal=?, user=?, nama_barang=?, jumlah=?, keperluan=? WHERE id=?");
    $stmt->bind_param("ssssisi", $no_file, $tanggal, $user, $nama_barang, $jumlah, $keperluan, $id);
    $stmt->execute();

    header("Location: merchandise.php");
    exit;
}
?>