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

// Cek apakah ada id_layanan yang dikirimkan
if (isset($_GET['id'])) {
    $id_layanan = $_GET['id'];

    // Hapus layanan berdasarkan id_layanan
    $delete_query = "DELETE FROM layanan WHERE id_layanan = '$id_layanan'";

    if (mysqli_query($conn, $delete_query)) {
        // Menyusun ulang ID setelah penghapusan
        $reset_id_query = "SET @num := 0; UPDATE layanan SET id_layanan = @num := (@num + 1)";
        mysqli_query($conn, $reset_id_query);

        // Redirect ke halaman daftar layanan
        header("Location: layanan.php");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    // Jika id_layanan tidak ada di URL
    echo "ID Layanan tidak ditemukan.";
    exit();
}
?>
