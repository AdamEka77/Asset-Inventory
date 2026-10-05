<?php
session_name("asset");
session_start();
include 'config.php';

// Cek apakah user sudah login
if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit;
}
// Cegah cache di semua browser
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Ambil role user dari session (default kosong kalau belum ada)
$role = $_SESSION["role"] ?? '';

$role = $_SESSION["role"] ?? '';
$username = strtolower($_SESSION["username"] ?? '');


?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <style>
        body {
            background-image: url(img/dashboard-bg.jpg);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .dashboard-welcome {
            text-align: center;
            margin-top: 30vh;
        }

        .akun {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 17px;
        }

        .nav-item .nav-link {
            color: white;
        }

        .dashboard {
            margin-top: 40px;
        }

        .container-relative {
            position: relative;
        }


        .button-logout {
            position: absolute;
            top: 0;
            right: 0;
        }

        .dashboard h2 {
            font-size: 25px;
            font-weight: bold;
        }

        .keluar {
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #E1252F;
            font-weight: bold;
            color: #E1252F;
            padding: 10px;
            background-color: #FDFFF5;
            transition: 0.3s;
        }

        .keluar:hover {
            color: #FDFFF5;
            background-color: #E1252F;
        }

        .dashboard .tanggal {
            font-weight: 400;
        }

        .dashboard .dashboard-tampilan-kiri {
            margin-top: 50px;
            border-radius: 5px;
            background-color: white;
            padding: 15px;
            text-align: center;
        }

        .dashboard .dashboard-tampilan-kanan {
            margin-top: 50px;
            border-radius: 5px;
            background-color: white;
            padding: 15px;
            text-align: center;
            position: relative;
            height: 53vh;
        }

        .dashboard .dashboard-tampilan-kanan a {
            text-decoration: none;

        }

        #akses {
            margin-top: 16vh;
        }



        .dashboard .dashboard-tampilan-kiri i {
            font-size: 100px;
        }

        .dashboard-tanggal {
            color: #FDFFF5;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <div class="dashboard">
        <div class="container container-relative">
            <a href="logout.php" class="button-logout"><button class="btn">
                    <span class="keluar"><i class="bi bi-box-arrow-right"></i>
                        Keluar</span></button></a>
            <div class="dashboard-tanggal">
                <h2>Dashboard</h2>
                <div class="tanggal">
                    <?php
                    date_default_timezone_set('Asia/Jakarta'); // Set zona waktu ke Indonesia
                    
                    $hari = date("l"); // Mendapatkan nama hari dalam bahasa Inggris
                    $tanggal = date("d"); // Mendapatkan tanggal (01-31)
                    $bulan = date("F"); // Mendapatkan nama bulan dalam bahasa Inggris
                    $tahun = date("Y"); // Mendapatkan tahun 4 digit
                    
                    // Konversi nama hari ke Bahasa Indonesia
                    $nama_hari = [
                        'Sunday' => 'Minggu',
                        'Monday' => 'Senin',
                        'Tuesday' => 'Selasa',
                        'Wednesday' => 'Rabu',
                        'Thursday' => 'Kamis',
                        'Friday' => 'Jumat',
                        'Saturday' => 'Sabtu'
                    ];

                    // Konversi nama bulan ke Bahasa Indonesia
                    $nama_bulan = [
                        'January' => 'Januari',
                        'February' => 'Februari',
                        'March' => 'Maret',
                        'April' => 'April',
                        'May' => 'Mei',
                        'June' => 'Juni',
                        'July' => 'Juli',
                        'August' => 'Agustus',
                        'September' => 'September',
                        'October' => 'Oktober',
                        'November' => 'November',
                        'December' => 'Desember'
                    ];

                    // Tampilkan tanggal dalam format Bahasa Indonesia
                    echo $nama_hari[$hari] . ", " . $tanggal . " " . $nama_bulan[$bulan] . " " . $tahun;
                    ?>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="dashboard-tampilan-kiri">
                        <h3>Selamat datang, <?php echo $_SESSION["username"]; ?></h3>
                        <i class="bi bi-person-circle"></i>
                        <h5>Status Akun :</h5>
                        <span><?= htmlspecialchars($role); ?></span>
                        <br><br>
                        <?php if ($role === "owner"): ?>
                            <a href="users.php"><button class="btn btn-primary">Data User</button></a>
                            <a href="register.php"><button class="btn btn-primary">Buat User</button></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="dashboard-tampilan-kanan">
                        <div id="akses">
                            <h2>Mau akses apa?</h2>

                            <?php
                            // Username yang hanya boleh melihat Merchandise
                            $merchandiseOnly = ['marketing', 'ecolive', 'retail', 'project'];
                            ?>

                            <?php if (!in_array($username, $merchandiseOnly)): ?>
                                <a href="asset.php">
                                    <button class="btn btn-danger">
                                        <i class="bi bi-box-seam"></i> Asset
                                    </button>
                                </a>
                            <?php endif; ?>

                            <a href="purchasing.php">
                                <button class="btn btn-danger">
                                    <i class="bi bi-wallet"></i> Merchandise
                                </button>
                            </a>

                            <?php if (!in_array($username, $merchandiseOnly)): ?>
                                <a href="kendaraan.php">
                                    <button class="btn btn-danger">
                                        <i class="bi bi-car-front-fill"></i> Kendaraan
                                    </button>
                                </a>
                                <a href="service.php">
                                    <button class="btn btn-danger">
                                        <i class="bi bi-tools"></i> Service
                                    </button>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <script>
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>
    <!-- Bootstrap script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script>
        if (performance.navigation.type === 2) {
            // Jika user menekan tombol Back, redirect ke login
            window.location.href = "index.php";
        }
    </script>
</body>

</html>