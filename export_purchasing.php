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
$sheet->setCellValue('B1', 'Nama Barang');
$sheet->setCellValue('C1', 'Total Quantity');
$sheet->setCellValue('D1', 'Deskripsi');
$sheet->setCellValue('E1', 'Dibuat Oleh');
$sheet->setCellValue('F1', 'Waktu Dibuat');

// Ambil data dari database
$result = $conn->query("
    SELECT foto_barang, nama_barang, 
           SUM(quantity) AS total_quantity, deskripsi, 
           created_by, MAX(created_at) AS created_at 
    FROM purchasing 
    GROUP BY foto_barang, nama_barang, deskripsi, created_by
    ORDER BY created_at DESC
");

$rowNum = 2;
$no = 1;
$imageFolder = 'uploads/'; // Sesuaikan dengan lokasi penyimpanan gambar

while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNum, $no++);
    $sheet->setCellValue('B' . $rowNum, $row['nama_barang']);
    $sheet->setCellValue('C' . $rowNum, $row['total_quantity']);
    $sheet->setCellValue('D' . $rowNum, $row['deskripsi']);
    $sheet->setCellValue('E' . $rowNum, $row['created_by']);
    $sheet->setCellValue('F' . $rowNum, $row['created_at']);

    // /// Cek apakah file gambar ada
    // $imagePath = $imageFolder . $row['foto_barang'];
    // if (file_exists($imagePath) && !empty($row['foto_barang'])) {
    //     $drawing = new Drawing();
    //     $drawing->setName('Foto Barang');
    //     $drawing->setDescription('Foto Barang');
    //     $drawing->setPath($imagePath); // Path gambar
    //     $drawing->setHeight(80); // Ukuran gambar (disesuaikan)
    //     $drawing->setCoordinates('B' . $rowNum); // Posisi gambar

    //     // Posisikan gambar agar tidak bertabrakan dengan teks
    //     $drawing->setOffsetX(10); // Geser ke kanan
    //     $drawing->setOffsetY(10); // Geser ke bawah
    //     $drawing->setWorksheet($sheet);

    //     // Atur tinggi baris agar gambar tidak bertabrakan
    //     $sheet->getRowDimension($rowNum)->setRowHeight(80);
    // } else {
    //     $sheet->setCellValue('B' . $rowNum, 'No Image');
    // }

    $rowNum++;
}

// Set lebar kolom agar gambar terlihat dengan baik
$sheet->getColumnDimension('B')->setWidth(15);
$sheet->getRowDimension($rowNum)->setRowHeight(50);

// Simpan file dan unduh
$filename = "data_purchasing.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>