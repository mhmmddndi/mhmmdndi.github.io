<?php
// Start session
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Include koneksi database
include 'koneksi.php';

// Cek apakah ada id_pelanggan yang dikirimkan
if (isset($_GET['id'])) {
    $id_pelanggan = $_GET['id'];

    // Hapus data pelanggan berdasarkan id_pelanggan
    $delete_query = "DELETE FROM pelanggan WHERE id_pelanggan = '$id_pelanggan'";

    if (mysqli_query($conn, $delete_query)) {
        // Setelah berhasil menghapus, reset auto_increment agar ID teratur
        $reset_id_query = "ALTER TABLE pelanggan AUTO_INCREMENT = 1";
        mysqli_query($conn, $reset_id_query);

        // Redirect ke halaman daftar pelanggan
        header("Location: pelanggan.php");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    // Jika id_pelanggan tidak ada di URL
    echo "ID Pelanggan tidak ditemukan.";
    exit();
}
?>
