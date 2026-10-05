<?php
session_name("asset");
session_start();
include 'config.php';

// Atur zona waktu ke Asia/Jakarta
date_default_timezone_set('Asia/Jakarta');

// Cek apakah user sudah login
if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit;
}

// Mengambil data dari tabel purchasing dan mengelompokkan berdasarkan gambar yang sama
$result = $conn->query("
    SELECT 
        MIN(id) AS id,  -- Menggunakan ID terkecil agar bisa digunakan untuk Edit/Delete
        foto_barang, 
        nama_barang, 
        SUM(quantity) AS total_quantity, 
        deskripsi, 
        created_by, 
        MAX(created_at) AS created_at 
    FROM purchasing 
    GROUP BY foto_barang, nama_barang, deskripsi, created_by
    ORDER BY created_at DESC
");

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Merchandise</title>
    <style>
        .akun {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 17px;
        }

        img {
            cursor: pointer;
        }
        .nav_button{
            position: relative;
        }
        .print_dokumen{
            position: absolute;
            top: 0;
            right: 0;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css">
</head>

<body class="bg-light">


    <div class="container mt-5">
        <a href="dashboard.php"><button type="button" class="btn btn-danger"><i
                    class="bi bi-caret-left-fill"></i>Kembali ke dashboard</button></a>
        <h2 class="text-center mb-4">Data Merchandise</h2>
        <div class="nav_button">
        <?php if ($_SESSION["role"] !== "user"): ?>
            <a href="purchasing_add.php" class="btn btn-primary mb-2">Tambah Data</a>
        <?php endif; ?>
        <a href="export_purchasing.php" class="btn btn-success mb-2">Export ke Excel</a>
          <?php if ($_SESSION["role"] !== "user"): ?>
        <a href="atur_quantity.php" class="btn btn-info mb-2">
          
                <span>Atur Quantity</span>
           
           </a>
            <?php endif; ?>
            
                <span class="print_dokumen">
            <a href="file/Form_Order_Merchandise_Sumato.pdf" class="btn btn-warning mb-2">Print Form Order</a>
            <a href="file/Form_Retur_Merchandise_Sumato.pdf" class="btn btn-secondary mb-2">Print Form Retur/Restock</a>
        </span>    
        </div>
            <?php if ($_SESSION["role"] !== "user"): ?>
            <p>*Note, jangan menginput nama barang dengan berakhiran hanya satu huruf.</p>
        <?php endif; ?>
        <table class="table table-bordered table-hover" id="table">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Gambar</th>
                    <th>Nama Barang</th>
                    <th>Deskripsi</th>
                    <th>Waktu Dibuat/Diedit</th>
                    <th>Dibuat Oleh</th>
                    <th>Total Quantity</th>
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
                            <img src="uploads/<?= htmlspecialchars($row['foto_barang']); ?>" width="100"
                                class="img-thumbnail klik--gambar" data-nama="<?= $row['nama_barang']; ?>"
                                data-quantity="<?= $row['total_quantity']; ?>" data-deskripsi="<?= $row['deskripsi']; ?>"
                                data-img="uploads/<?= $row['foto_barang']; ?>">
                        </td>
                        <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                        <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                        <td><?= date("d-m-Y H:i:s", strtotime($row['created_at'])); ?></td>
                        <td><?= htmlspecialchars($row['created_by']); ?></td>
                        <td><?= htmlspecialchars($row['total_quantity']); ?></td>
                        <?php if ($_SESSION["role"] !== "user"): ?>
                            <td>
                                <a href="purchasing_edit.php?id=<?= intval($row['id']); ?>"
                                    class="btn btn-warning btn-sm mb-3">Edit</a>
                                <a href="purchasing_delete.php?id=<?= intval($row['id']); ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>


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
                        <p><strong>Quantity:</strong> <span id="modalQuantity"></span></p>
                        <p><strong>Deskripsi:</strong> <span id="modalDeskripsi"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br>
    <br>
    <br>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
    <script>new DataTable('#table');</script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".klik--gambar").forEach(img => {
                img.addEventListener("click", function (event) {
                    event.stopPropagation(); // Mencegah klik merambat ke elemen lain di tabel

                    // Ambil data dari atribut data-*
                    let nama = this.getAttribute("data-nama");
                    let quantity = this.getAttribute("data-quantity");
                    let deskripsi = this.getAttribute("data-deskripsi");
                    let imgSrc = this.getAttribute("data-img");

                    // Masukkan data ke dalam modal
                    document.getElementById("modalNama").textContent = nama;
                    document.getElementById("modalQuantity").textContent = quantity;
                    document.getElementById("modalDeskripsi").textContent = deskripsi;
                    document.getElementById("modalImage").src = imgSrc;

                    // Tampilkan modal
                    let modal = new bootstrap.Modal(document.getElementById("detailModal"));
                    modal.show();
                });
            });

            // **FIX:** Mencegah modal terbuka jika klik di bagian lain tabel
            document.querySelector("table").addEventListener("click", function (event) {
                if (!event.target.classList.contains("klik--gambar")) {
                    event.stopPropagation();
                }
            });
        });
    </script>
</body>



</html>