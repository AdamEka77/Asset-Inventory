<?php
// Dipakai bersama oleh: service.php, input_service.php, edit_service.php, delete_service.php, export_service.php

function service_require_access()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_name("asset");
        session_start();
    }
    if (!isset($_SESSION["username"])) {
        header("Location: index.php");
        exit;
    }
    // Sama seperti tombol Kendaraan di dashboard: user merchandise-only tidak boleh masuk
    $merchandiseOnly = ['marketing', 'ecolive', 'retail', 'project'];
    if (in_array(strtolower($_SESSION["username"]), $merchandiseOnly)) {
        header("Location: dashboard.php");
        exit;
    }
}

function service_plat_list()
{
    return [
        'Motor' => [
            ['value' => 'F 2533 DY', 'text' => 'F 2533 DY (Motor, Honda Vario 150)'],
            ['value' => 'B 4088 SXK', 'text' => 'B 4088 SXK (Motor, Yamaha Gear 125)'],
        ],
        'Mobil' => [
            ['value' => 'B 60Q', 'text' => 'B 60Q (Mobil, Toyota Avanza)'],
            ['value' => 'B 2779 SXR', 'text' => 'B 2779 SXR (Mobil, Daihatsu Luxio)'],
            ['value' => 'B 9077 PAL', 'text' => 'B 9077 PAL (Mobil, Daihatsu Grandmax Pick UP M/T)'],
            ['value' => 'B 941', 'text' => 'B 941 (Mobil, Mitsubishi Expander)'],
            ['value' => 'B 300 XX', 'text' => 'B 300 XX (Mobil, Avanza)'],
        ],
    ];
}

function service_lokasi_ac_list()
{
    return [
        'Ruang GM ( Depan )',
        'Ruang HRD',
        'Ruang Logistik',
        'Ruang Finance',
        'Ruang Sales',
        'Ruang Marketing Support',
        'Ruang IT/GA/Purchasing',
        'Ruang Teknisi',
        'Ruang GM ( Belakang )',
    ];
}

function service_hari($tanggal)
{
    $map = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
    return $map[(int) date('N', strtotime($tanggal))] ?? '';
}

// Ambil & validasi data POST. Return [$data, $errors]
function service_collect_post()
{
    $p = function ($k) {
        return trim($_POST[$k] ?? '');
    };
    $nul = function ($v) {
        return $v === '' ? null : $v;
    };

    $data = [
        'kategori_servis' => $p('kategori_servis'),
        'tanggal_servis' => $p('tanggal_servis'),
        'jenis_kendaraan' => null,
        'plat' => null,
        'km_awal' => null,
        'lokasi_ac' => null,
        'merk_ac' => null,
        'jenis_pekerjaan' => $p('jenis_pekerjaan'),
        'bengkel' => $nul($p('bengkel')),
        'biaya' => $nul($p('biaya')),
    ];
    $errors = [];

    if (!in_array($data['kategori_servis'], ['Kendaraan', 'AC'], true)) {
        $errors[] = "Kategori servis tidak valid.";
    }
    if ($data['tanggal_servis'] === '') {
        $errors[] = "Tanggal servis wajib diisi.";
    }
    if ($data['jenis_pekerjaan'] === '') {
        $errors[] = "Jenis pekerjaan wajib diisi.";
    }
    if ($data['biaya'] !== null && (!is_numeric($data['biaya']) || $data['biaya'] < 0)) {
        $errors[] = "Biaya harus berupa angka.";
    }

    if ($data['kategori_servis'] === 'Kendaraan') {
        $jenis = $p('jenis_kendaraan');
        $plat = $p('plat');
        $km = $p('km_awal');
        $list = service_plat_list();

        if (!isset($list[$jenis])) {
            $errors[] = "Jenis kendaraan wajib dipilih.";
        } elseif (!in_array($plat, array_column($list[$jenis], 'value'), true)) {
            $errors[] = "Plat tidak sesuai dengan jenis kendaraan.";
        }
        if ($km === '' || !ctype_digit($km)) {
            $errors[] = "KM awal wajib diisi (angka bulat).";
        }
        $data['jenis_kendaraan'] = $jenis;
        $data['plat'] = $plat;
        $data['km_awal'] = $km;
    } elseif ($data['kategori_servis'] === 'AC') {
        $lokasi = $p('lokasi_ac');
        if (!in_array($lokasi, service_lokasi_ac_list(), true)) {
            $errors[] = "Lokasi AC wajib dipilih.";
        }
        $data['lokasi_ac'] = $nul($lokasi);
        // AC: tanpa merk dan bengkel/vendor
        $data['merk_ac'] = null;
        $data['bengkel'] = null;
    }

    return [$data, $errors];
}