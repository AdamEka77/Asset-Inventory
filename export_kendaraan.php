<?php
require 'vendor/autoload.php';
include 'config.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil filter dari GET
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';

// Siapkan query
if (!empty($kategori)) {
    $kategori = mysqli_real_escape_string($conn, $kategori);
    $query = "SELECT * FROM kendaraan WHERE jenis_kendaraan = '$kategori' ORDER BY id DESC";
} else {
    $query = "SELECT * FROM kendaraan ORDER BY id DESC";
}

$result = mysqli_query($conn, $query);

// Buat spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$sheet->setCellValue('A1', 'No');
$sheet->setCellValue('B1', 'Hari');
$sheet->setCellValue('C1', 'Tanggal');
$sheet->setCellValue('D1', 'Jenis Kendaraan');
$sheet->setCellValue('E1', 'Plat');
$sheet->setCellValue('F1', 'KM Awal');
$sheet->setCellValue('G1', 'Tujuan');
$sheet->setCellValue('H1', 'User');
$sheet->setCellValue('I1', 'Keterangan');
$sheet->setCellValue('J1', 'Created At');

// Isi data
$row = 2;
$no = 1;
while ($data = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue("A$row", $no++);
    $sheet->setCellValue("B$row", $data['hari']);
    $sheet->setCellValue("C$row", $data['tanggal']);
    $sheet->setCellValue("D$row", $data['jenis_kendaraan']);
    $sheet->setCellValue("E$row", $data['plat']);
    $sheet->setCellValue("F$row", $data['km_awal']);
    $sheet->setCellValue("G$row", $data['tujuan']);
    $sheet->setCellValue("H$row", $data['user']);
    $sheet->setCellValue("I$row", $data['keterangan']);
    $sheet->setCellValue("J$row", $data['created_at']);
    $row++;
}

// Download file
$filename = 'data_kendaraan_' . ($kategori ?: 'semua') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;