<?php
session_name("asset");
session_start();
include 'config.php';

// Redirect ke login jika belum masuk
if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit;
}

$result = $conn->query("SELECT * FROM items ORDER BY id DESC");

// Ambil daftar kategori unik dari database untuk dropdown
$kategori_query = $conn->query("SELECT DISTINCT kategori_barang FROM items");

// Ambil kategori yang dipilih dari request (jika ada)
$kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : "";

// Query untuk mengambil data sesuai filter kategori
if ($kategori_filter && $kategori_filter !== "all") {
    $stmt = $conn->prepare("SELECT * FROM items WHERE kategori_barang = ?");
    $stmt->bind_param("s", $kategori_filter);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM items"); // Ambil semua data jika tidak difilter
}
?>

<!DOCTYPE html>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    table img {
        cursor: pointer;
    }
</style>

<html lang="id">

<head>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Asset Barang</title>
    <style>
        .akun {
            align-items: center;
            vertical-align: middle;
            font-size: 17px;
            text-transform: uppercase;
            font-weight: bold;
        }

        svg {
            vertical-align: middle;
        }

        a.logout:hover {
            background-color: red;
            color: white;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">



    <div class="container mt-5">

        <a href="dashboard.php"><button type="button" class="btn btn-danger"><i
                    class="bi bi-caret-left-fill"></i>Kembali ke dashboard</button></a>
        <h2 class="text-center mb-4">Data Asset</h2>
        <a href="export_asset.php" class="btn btn-success mb-2">Export ke Excel</a>
        <?php if ($_SESSION["role"] == "owner" || $_SESSION["role"] == "admin"): ?>
            <a class="btn btn-primary mb-2" href="create.php">Tambah Barang</a>
        <?php endif; ?>
        <!-- Dropdown Filter -->
        <select id="filterKategori" class="form-select">
            <option value="all">Semua Kategori</option>
            <?php while ($row = $kategori_query->fetch_assoc()): ?>
                <option value="<?= $row['kategori_barang']; ?>" <?= ($row['kategori_barang'] == $kategori_filter) ? "selected" : ""; ?>>
                    <?= $row['kategori_barang']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <table class="table table-bordered table-hover" id="table">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Gambar</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>No Asset</th>
                    <th>Ruangan</th>
                    <th>Deskripsi</th>
                    <th>Dibuat Oleh</th>
                    <th>Waktu Dibuat</th>
                    <?php if ($_SESSION["role"] !== "user"): ?>
                        <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td>
                            <img src="uploads/<?= $row['foto_barang']; ?>" width="100" class="img-thumbnail klik--gambar"
                                data-nama="<?= $row['nama_barang']; ?>" data-kategori="<?= $row['kategori_barang']; ?>"
                                data-no-asset="<?= $row['no_asset']; ?>" data-deskripsi="<?= $row['deskripsi']; ?>"
                                data-ruangan="<?= $row['ruangan']; ?>"
                                data-img="uploads/<?= $row['foto_barang']; ?>">
                        </td>
                        <td><?= $row['nama_barang']; ?></td>
                        <td><?= $row['kategori_barang']; ?></td>
                        <td><?= $row['no_asset']; ?></td>
                        <td><?= $row['ruangan']; ?></td>
                        <td><?= $row['deskripsi']; ?></td>
                        <td><?= $row['created_by']; ?></td>
                        <td><?= date("d M Y H:i", strtotime($row['created_at'])); ?></td>
                        <?php if ($_SESSION["role"] !== "user"): ?>
                            <td>
                                <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm mb-3">Edit</a>
                               <a href="delete.php?id=<?= $row['id']; ?>&kategori=<?= urlencode($kategori_filter); ?>"
   class="btn btn-danger btn-sm"
   onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>

                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Modal -->
        <!-- Modal -->
        <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Detail Barang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img id="modalImage" src="" class="img-fluid mb-3" alt="Gambar Barang">
                        <p><strong>Nama:</strong> <span id="modalNama"></span></p>
                        <p><strong>Kategori:</strong> <span id="modalKategori"></span></p>
                        <p><strong>No Asset:</strong> <span id="modalNoAsset"></span></p>
                         <p><strong>Ruangan:</strong> <span id="modalRuangan"></span></p>
                        <p><strong>Deskripsi:</strong> <span id="modalDeskripsi"></span></p>
                    </div>
                </div>
            </div>
        </div>


    </div>


    <br>
    <br>
    <br>


    <!-- JavaScript untuk AJAX Filter -->
    <script>
        document.getElementById("filterKategori").addEventListener("change", function () {
            let kategori = this.value;
            window.location.href = "asset.php?kategori=" + kategori;
        });
    </script>


    <script>
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>

    <script>
        document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(item => {
            item.addEventListener("click", function () {
                let icon = this.querySelector("i");
                let isExpanded = this.getAttribute("aria-expanded") === "true";

                // Toggle panah naik atau turun
                icon.classList.toggle("bi-chevron-down", !isExpanded);
                icon.classList.toggle("bi-chevron-up", isExpanded);
            });
        });
    </script>

    <script>
        document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(item => {
            item.addEventListener("click", function () {
                let icon = this.querySelector("i");
                let target = document.querySelector(this.getAttribute("href"));

                target.addEventListener("shown.bs.collapse", () => {
                    icon.classList.add("rotate");
                });

                target.addEventListener("hidden.bs.collapse", () => {
                    icon.classList.remove("rotate");
                });
            });
        });
    </script>
    <!-- Asset plugin datatables -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelector("#table tbody").addEventListener("click", function (event) {
                let target = event.target;

                // Pastikan yang diklik adalah gambar dengan kelas klik--gambar
                if (target.classList.contains("klik--gambar")) {
                    event.stopPropagation(); // Mencegah klik merambat ke elemen lain di tabel

                    // Ambil data dari atribut data-*
                    let nama = target.getAttribute("data-nama");
                    let kategori = target.getAttribute("data-kategori");
                    let noAsset = target.getAttribute("data-no-asset");
                    let ruangan = target.getAttribute("data-ruangan");
                    let deskripsi = target.getAttribute("data-deskripsi");
                    let imgSrc = target.getAttribute("data-img");

                    // Masukkan data ke dalam modal
                    document.getElementById("modalNama").textContent = nama;
                    document.getElementById("modalKategori").textContent = kategori;
                    document.getElementById("modalNoAsset").textContent = noAsset;
                    document.getElementById("modalRuangan").textContent = ruangan;
                    document.getElementById("modalDeskripsi").textContent = deskripsi;
                    document.getElementById("modalImage").src = imgSrc;

                    // Tampilkan modal
                    let modal = new bootstrap.Modal(document.getElementById("detailModal"));
                    modal.show();
                }
            });
        });

    </script>





    <script>
    new DataTable('#table', {
        stateSave: true
    });
</script>

</body>

</html>