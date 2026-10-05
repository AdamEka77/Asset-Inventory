<?php
session_name("asset");
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hari = $_POST['hari'];
    $tanggal = $_POST['tanggal'];
    $jenis_kendaraan = $_POST['jenis_kendaraan'];
    $plat = $_POST['plat'];
    $km_awal = $_POST['km_awal'];
    $tujuan = $_POST['tujuan'];
    $user = $_POST['user'];
    $keterangan = $_POST['keterangan'];
    $created_at = date("Y-m-d H:i:s");

    // Ambil username dari session
    $created_by = $_SESSION['username'];

    $sql = "INSERT INTO kendaraan (
                hari, tanggal, jenis_kendaraan, plat, km_awal,
                tujuan, user, keterangan, created_at, created_by
            ) VALUES (
                '$hari', '$tanggal', '$jenis_kendaraan', '$plat', '$km_awal',
                '$tujuan', '$user', '$keterangan', '$created_at', '$created_by'
            )";

    if (mysqli_query($conn, $sql)) {
        header("Location: kendaraan.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>



<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Input Data Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Form Input Data Kendaraan</h2>
        <form method="POST" class="mb-5">
            <div class="mb-3">
                <label class="form-label">Hari</label>
                <select name="hari" class="form-control" required>
                    <option value="">-- Pilih Hari --</option>
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Jenis Kendaraan</label>
                <select name="jenis_kendaraan" id="jenis_kendaraan" class="form-control" required
                    onchange="updatePlatOptions()">
                    <option value="">-- Pilih Jenis Kendaraan --</option>
                    <option value="Motor">Motor</option>
                    <option value="Mobil">Mobil</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Plat</label>
                <select name="plat" id="plat" class="form-control" required>
                    <option value="">-- Pilih Plat --</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">KM Awal</label>
                <input type="number" class="form-control" name="km_awal" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tujuan</label>
                <input type="text" name="tujuan" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">User</label>
                <input type="text" name="user" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Keterangan/Keperluan</label>
                <textarea name="keterangan" class="form-control" rows="2"></textarea>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>



    </div>
    <script>
        function updatePlatOptions() {
            const jenis = document.getElementById("jenis_kendaraan").value;
            const platSelect = document.getElementById("plat");

            const motor = [
                { value: "F 2533 DY", text: "F 2533 DY (Motor, Honda Vario 150)" },
                { value: "B 4088 SXK", text: "B 4088 SXK (Motor, Yamaha Gear 125)" }
            ];

            const mobil = [
                { value: "B 60Q", text: "B 60Q (Mobil, Toyota Avanza)" },
                { value: "B 2779 SXR", text: "B 2779 SXR (Mobil, Daihatsu Luxio)" },
                { value: "B 9077 PAL", text: "B 9077 PAL (Mobil, Daihatsu Grandmax Pick UP M/T)" },
                { value: "B 941 STW", text: "B 941 STW (Mobil, Mitsubishi Expander)" },
                { value: "B 300", text: "B 300 XX (Mobil, Avanza)" }                
            ];

            // Kosongkan dulu opsi plat
            platSelect.innerHTML = '<option value="">-- Pilih Plat --</option>';

            let opsi = [];
            if (jenis === "Motor") opsi = motor;
            if (jenis === "Mobil") opsi = mobil;

            opsi.forEach(function (item) {
                const option = document.createElement("option");
                option.value = item.value;
                option.text = item.text;
                platSelect.appendChild(option);
            });
        }
    </script>
</body>

</html>