<?php
require 'vendor/autoload.php'; // Jika menggunakan Composer
include 'config.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$sheet->setCellValue('A1', 'No');
$sheet->setCellValue('B1', 'Foto Barang');
$sheet->setCellValue('C1', 'Nama Barang');
$sheet->setCellValue('D1', 'Kategori');
$sheet->setCellValue('E1', 'No Asset');
$sheet->setCellValue('F1', 'Deskripsi');
$sheet->setCellValue('G1', 'Dibuat Oleh');
$sheet->setCellValue('H1', 'Waktu Dibuat');

// Atur lebar kolom agar lebih rapi
$sheet->getColumnDimension('A')->setWidth(5);
$sheet->getColumnDimension('B')->setWidth(20); // Lebar kolom gambar diperbesar
$sheet->getColumnDimension('C')->setWidth(25);
$sheet->getColumnDimension('D')->setWidth(15);
$sheet->getColumnDimension('E')->setWidth(15);
$sheet->getColumnDimension('F')->setWidth(30);
$sheet->getColumnDimension('G')->setWidth(15);
$sheet->getColumnDimension('H')->setWidth(20);

// Ambil data dari database
$result = $conn->query("SELECT * FROM items ORDER BY created_at DESC");
$rowNum = 2;
$no = 1;
$imageFolder = 'uploads/'; // Sesuaikan dengan lokasi penyimpanan gambar

while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNum, $no++);
    $sheet->setCellValue('C' . $rowNum, $row['nama_barang']);
    $sheet->setCellValue('D' . $rowNum, $row['kategori_barang']);
    $sheet->setCellValue('E' . $rowNum, $row['no_asset']);
    $sheet->setCellValue('F' . $rowNum, $row['deskripsi']);
    $sheet->setCellValue('G' . $rowNum, $row['created_by']);
    $sheet->setCellValue('H' . $rowNum, $row['created_at']);

    // Cek apakah file gambar ada
    $imagePath = $imageFolder . $row['foto_barang'];
    if (file_exists($imagePath) && !empty($row['foto_barang'])) {
        $drawing = new Drawing();
        $drawing->setName('Foto Barang');
        $drawing->setDescription('Foto Barang');
        $drawing->setPath($imagePath); // Path gambar
        $drawing->setHeight(80); // Ukuran gambar (disesuaikan)
        $drawing->setCoordinates('B' . $rowNum); // Posisi gambar

        // Posisikan gambar agar tidak bertabrakan dengan teks
        $drawing->setOffsetX(10); // Geser ke kanan
        $drawing->setOffsetY(10); // Geser ke bawah
        $drawing->setWorksheet($sheet);

        // Atur tinggi baris agar gambar tidak bertabrakan
        $sheet->getRowDimension($rowNum)->setRowHeight(80);
    } else {
        $sheet->setCellValue('B' . $rowNum, 'No Image');
    }

    $rowNum++;
}

// Set lebar kolom agar gambar terlihat dengan baik
$sheet->getColumnDimension('B')->setWidth(15);
$sheet->getRowDimension($rowNum)->setRowHeight(50);

// Simpan file dan unduh
$filename = "data_asset.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>