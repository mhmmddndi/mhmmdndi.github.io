<?php
// Konfigurasi database
$host = "localhost";        // Host database, biasanya "localhost"
$user = "root";             // Username database
$password = "";             // Password database
$database = "Loundry";      // Nama database

// Membuat koneksi ke database
$conn = mysqli_connect($host, $user, $password, $database);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

?>
