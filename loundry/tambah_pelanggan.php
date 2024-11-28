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

// Variabel untuk menyimpan pesan error atau sukses
$error = '';
$sukses = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $no_telepon = $_POST['no_telepon'];
    $alamat = $_POST['alamat'];

    // Validasi input
    if (empty($nama_pelanggan) || empty($no_telepon) || empty($alamat)) {
        $error = "Nama pelanggan, no telepon, dan alamat tidak boleh kosong!";
    } else {
        // Query untuk memasukkan data pelanggan baru
        $insert_query = "INSERT INTO pelanggan (nama_pelanggan, no_telepon, alamat) 
                         VALUES ('$nama_pelanggan', '$no_telepon', '$alamat')";

        if (mysqli_query($conn, $insert_query)) {
            // Jika berhasil, set pesan sukses dan redirect ke pelanggan.php
            $sukses = "Pelanggan berhasil ditambahkan!";
            header("Location: pelanggan.php"); // Redirect ke halaman daftar pelanggan
            exit(); // Pastikan script berhenti setelah redirect
        } else {
            $error = "Terjadi kesalahan saat menambahkan pelanggan: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pelanggan - Laundry</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- nav.php -->
    <link rel="stylesheet" href="nav.css">
</head>
<body>
    
    <?php include 'nav.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Tambah Pelanggan</h1>

        <!-- Tampilkan pesan error atau sukses -->
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif ($sukses): ?>
            <div class="alert alert-success"><?php echo $sukses; ?></div>
        <?php endif; ?>

        <!-- Form untuk tambah pelanggan -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-container">
                    <form action="tambah_pelanggan.php" method="POST">
                        <div class="mb-3">
                            <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
                            <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan" required>
                        </div>

                        <div class="mb-3">
                            <label for="no_telepon" class="form-label">No Telepon</label>
                            <input type="text" class="form-control" id="no_telepon" name="no_telepon" required>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Tambah Pelanggan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
