<?php
date_default_timezone_set('Asia/Makassar');

$host = "localhost";
$user = "root"; // ganti jika berbeda
$pass = ""; // ganti jika ada password
$db = "banksampah";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
