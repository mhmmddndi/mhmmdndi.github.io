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
    $nama_layanan = $_POST['nama_layanan'];
    $harga_per_kg = $_POST['harga_per_kg'];

    // Validasi input
    if (empty($nama_layanan) || empty($harga_per_kg)) {
        $error = "Nama layanan dan harga per kg tidak boleh kosong!";
    } else {
        // Query untuk memasukkan data layanan baru
        $insert_query = "INSERT INTO layanan (nama_layanan, harga_per_kg) 
                         VALUES ('$nama_layanan', '$harga_per_kg')";

        if (mysqli_query($conn, $insert_query)) {
            // Jika berhasil, set pesan sukses dan redirect ke layanan.php
            $sukses = "Layanan berhasil ditambahkan!";
            header("Location: layanan.php"); // Redirect ke halaman daftar layanan
            exit(); // Pastikan script berhenti setelah redirect
        } else {
            $error = "Terjadi kesalahan saat menambahkan layanan: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Layanan - Laundry</title>
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
        <h1 class="text-center mb-4">Tambah Layanan</h1>

        <!-- Tampilkan pesan error atau sukses -->
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif ($sukses): ?>
            <div class="alert alert-success"><?php echo $sukses; ?></div>
        <?php endif; ?>

        <!-- Form untuk tambah layanan -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-container">
                    <form action="tambah_layanan.php" method="POST">
                        <div class="mb-3">
                            <label for="nama_layanan" class="form-label">Nama Layanan</label>
                            <input type="text" class="form-control" id="nama_layanan" name="nama_layanan" required>
                        </div>

                        <div class="mb-3">
                            <label for="harga_per_kg" class="form-label">Harga per Kg</label>
                            <input type="number" class="form-control" id="harga_per_kg" name="harga_per_kg" min="0" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Tambah Layanan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
