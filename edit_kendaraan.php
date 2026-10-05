<?php
include 'config.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM kendaraan WHERE id = $id");
$data = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hari = $_POST['hari'];
    $tanggal = $_POST['tanggal'];
    $jenis_kendaraan = $_POST['jenis_kendaraan'];
    $plat = $_POST['plat'];
    $km_awal = floatval($_POST['km_awal']);
    $tujuan = $_POST['tujuan'];
    $user = $_POST['user'];
    $keterangan = $_POST['keterangan'];

    $stmt = $conn->prepare("UPDATE kendaraan SET hari=?, tanggal=?, jenis_kendaraan=?, plat=?, km_awal=?, tujuan=?, user=?, keterangan=? WHERE id=?");
    $stmt->bind_param("ssssdsssi", $hari, $tanggal, $jenis_kendaraan, $plat, $km_awal, $tujuan, $user, $keterangan, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: kendaraan.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h3>Edit Data Kendaraan</h3>
        <form method="post">
            <div class="row mb-3">
                <div class="col">
                    <label>Hari</label>
                    <select name="hari" class="form-control" required>
                        <option value="">-- Pilih Hari --</option>
                        <?php
                        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                        foreach ($hariList as $hari) {
                            $selected = ($data['hari'] == $hari) ? 'selected' : '';
                            echo "<option value=\"$hari\" $selected>$hari</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= $data['tanggal']; ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label>Jenis Kendaraan</label>
                    <select name="jenis_kendaraan" id="jenis_kendaraan" class="form-control" required onchange="updatePlatOptions()">
                        <option value="">-- Pilih Jenis Kendaraan --</option>
                        <option value="Motor" <?= $data['jenis_kendaraan'] == 'Motor' ? 'selected' : '' ?>>Motor</option>
                        <option value="Mobil" <?= $data['jenis_kendaraan'] == 'Mobil' ? 'selected' : '' ?>>Mobil</option>
                    </select>
                </div>
                <div class="col">
                    <label>Plat</label>
                    <select name="plat" id="plat" class="form-control" required>
                        <option value="">-- Pilih Plat --</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label>KM Awal</label>
                <input type="number" name="km_awal" class="form-control" value="<?= $data['km_awal']; ?>" required>
            </div>
            <div class="mb-3">
                <label>Tujuan</label>
                <input type="text" name="tujuan" class="form-control" value="<?= $data['tujuan']; ?>" required>
            </div>
            <div class="mb-3">
                <label>User</label>
                <input type="text" name="user" class="form-control" value="<?= $data['user']; ?>" required>
            </div>
            <div class="mb-3">
                <label>Keterangan/Keperluan</label>
                <textarea name="keterangan" class="form-control"><?= $data['keterangan']; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="kendaraan.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script>
        const dataPlat = {
            Motor: [
                { value: "F 2533 DY", text: "F 2533 DY (Motor, Honda Vario 150)" },
                 { value: "B 4088 SXK", text: "B 4088 SXK (Motor, Yamaha Gear 125)" }
            ],
            Mobil: [
                { value: "B 60Q", text: "B 60Q (Mobil, Toyota Avanza)" },
                { value: "B 300 XX", text: "B 300 XX (Mobil, Toyota Avanza)" },
                { value: "B 2779 SXR", text: "B 2779 SXR (Mobil, Daihatsu Luxio)" },
                { value: "B 9077 PAL", text: "B 9077 PAL (Mobil, Daihatsu Grandmax Pick UP M/T)" },
                { value: "B 2529 STW", text: "B 2529 STW (Mobil, Mitsubishi Expander)" }
            ]
        };

        function updatePlatOptions() {
            const jenis = document.getElementById("jenis_kendaraan").value;
            const platSelect = document.getElementById("plat");

            // Clear current options
            platSelect.innerHTML = '<option value="">-- Pilih Plat --</option>';

            if (dataPlat[jenis]) {
                dataPlat[jenis].forEach(function (item) {
                    const option = document.createElement("option");
                    option.value = item.value;
                    option.text = item.text;
                    if (item.value === "<?= $data['plat']; ?>") {
                        option.selected = true;
                    }
                    platSelect.appendChild(option);
                });
            }
        }

        // Jalankan fungsi saat halaman dimuat
        document.addEventListener("DOMContentLoaded", updatePlatOptions);
    </script>
</body>
</html>
