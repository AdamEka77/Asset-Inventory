<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "crud_php";

date_default_timezone_set('Asia/Jakarta');

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>