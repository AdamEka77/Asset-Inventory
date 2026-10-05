<?php
// Partial form. Butuh variabel: $data (array), $errors (array), $judul, $tombol
$v = function ($k) use ($data) {
    return htmlspecialchars((string) ($data[$k] ?? ''));
};
$sel = function ($k, $val) use ($data) {
    return (($data[$k] ?? '') === $val) ? 'selected' : '';
};
?>
<h2 class="mb-4"><?= htmlspecialchars($judul) ?></h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" class="mb-5">
    <!-- Baris 1: kategori + tanggal -->
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label class="form-label">Kategori Servis</label>
            <select name="kategori_servis" id="kategori_servis" class="form-control" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="Kendaraan" <?= $sel('kategori_servis', 'Kendaraan') ?>>Kendaraan</option>
                <option value="AC" <?= $sel('kategori_servis', 'AC') ?>>AC</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Tanggal Servis</label>
            <input type="date" name="tanggal_servis" class="form-control" value="<?= $v('tanggal_servis') ?>" required>
        </div>
    </div>

    <!-- Khusus servis kendaraan -->
    <div id="blok-kendaraan" class="row g-3 mb-3" style="display:none">
        <div class="col-md-6">
            <label class="form-label">Jenis Kendaraan</label>
            <select name="jenis_kendaraan" id="jenis_kendaraan" class="form-control" data-wajib="1">
                <option value="">-- Pilih Jenis Kendaraan --</option>
                <option value="Motor" <?= $sel('jenis_kendaraan', 'Motor') ?>>Motor</option>
                <option value="Mobil" <?= $sel('jenis_kendaraan', 'Mobil') ?>>Mobil</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Plat / Merk</label>
            <select name="plat" id="plat" class="form-control" data-wajib="1">
                <option value="">-- Pilih Plat --</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">KM Awal</label>
            <input type="number" min="0" name="km_awal" class="form-control" value="<?= $v('km_awal') ?>"
                data-wajib="1">
        </div>
        <div class="col-md-6">
            <label class="form-label">Bengkel / Vendor</label>
            <input type="text" name="bengkel" class="form-control" value="<?= $v('bengkel') ?>">
        </div>
    </div>

    <!-- Khusus servis AC -->
    <div id="blok-ac" class="row g-3 mb-3" style="display:none">
        <div class="col-12">
            <label class="form-label">Lokasi AC</label>
            <select name="lokasi_ac" class="form-control" data-wajib="1">
                <option value="">-- Pilih Lokasi --</option>
                <?php
                $lokasiList = service_lokasi_ac_list();
                $lokasiSaatIni = $data['lokasi_ac'] ?? '';
                // Data lama dengan lokasi di luar daftar tetap tampil saat edit
                if ($lokasiSaatIni !== '' && !in_array($lokasiSaatIni, $lokasiList, true)) {
                    $lokasiList[] = $lokasiSaatIni;
                }
                foreach ($lokasiList as $l): ?>
                    <option value="<?= htmlspecialchars($l) ?>" <?= $lokasiSaatIni === $l ? 'selected' : '' ?>>
                        <?= htmlspecialchars($l) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Umum -->
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label class="form-label">Jenis Pekerjaan</label>
            <input type="text" name="jenis_pekerjaan" class="form-control"
                placeholder="Contoh: Ganti oli, Servis berkala, Cuci AC" value="<?= $v('jenis_pekerjaan') ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Biaya (Rp)</label>
            <input type="number" min="0" step="1" name="biaya" class="form-control" value="<?= $v('biaya') ?>">
        </div>
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-primary"><?= htmlspecialchars($tombol) ?></button>
        <a href="service.php" class="btn btn-secondary">Batal</a>
    </div>
</form>

<script>
    const platData = <?= json_encode(service_plat_list()) ?>;
    const platTerpilih = <?= json_encode($data['plat'] ?? '') ?>;

    function updatePlatOptions(pilih) {
        const jenis = document.getElementById("jenis_kendaraan").value;
        const platSelect = document.getElementById("plat");
        platSelect.innerHTML = '<option value="">-- Pilih Plat --</option>';
        (platData[jenis] || []).forEach(function (item) {
            const option = document.createElement("option");
            option.value = item.value;
            option.text = item.text;
            if (pilih && pilih === item.value) option.selected = true;
            platSelect.appendChild(option);
        });
    }

    function toggleKategori() {
        const k = document.getElementById("kategori_servis").value;
        const blokK = document.getElementById("blok-kendaraan");
        const blokA = document.getElementById("blok-ac");

        blokK.style.display = (k === "Kendaraan") ? "" : "none";
        blokA.style.display = (k === "AC") ? "" : "none";

        // Input di blok yang disembunyikan dinonaktifkan supaya tidak ikut terkirim.
        // Hanya field bertanda data-wajib yang required.
        [[blokK, "Kendaraan"], [blokA, "AC"]].forEach(function (pair) {
            pair[0].querySelectorAll("input, select").forEach(function (el) {
                el.disabled = (k !== pair[1]);
                el.required = (k === pair[1] && el.dataset.wajib === "1");
            });
        });
    }

    document.getElementById("kategori_servis").addEventListener("change", toggleKategori);
    document.getElementById("jenis_kendaraan").addEventListener("change", function () { updatePlatOptions(); });

    // Inisialisasi (penting untuk halaman edit / saat validasi gagal)
    updatePlatOptions(platTerpilih);
    toggleKategori();
</script>