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

// Cek apakah parameter 'id' ada dalam URL
if (isset($_GET['id'])) {
    $id_detail = $_GET['id'];

    // Hapus data detail pesanan berdasarkan id
    $query_delete = "DELETE FROM detail_pesanan WHERE id_detail = $id_detail";
    if (mysqli_query($conn, $query_delete)) {
        // Setelah penghapusan, atur ulang ID agar rapih
        $query_reset = "
            SET @count = 0;
            UPDATE detail_pesanan SET id_detail = (@count := @count + 1);
        ";

        // Eksekusi query untuk reset ID
        if (mysqli_multi_query($conn, $query_reset)) {
            // Redirect kembali ke halaman detail pesanan setelah berhasil
            header("Location: detail_pesanan.php");
            exit();
        } else {
            // Jika gagal mereset ID
            echo "Gagal mengatur ulang ID: " . mysqli_error($conn);
        }
    } else {
        // Jika gagal menghapus data
        echo "Gagal menghapus data: " . mysqli_error($conn);
    }
} else {
    // Jika parameter 'id' tidak ditemukan
    echo "ID tidak ditemukan!";
}
?>
