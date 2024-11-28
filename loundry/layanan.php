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

// Ambil data layanan
$query = "SELECT * FROM layanan";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan - Laundry</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link ke Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- nav.php -->
    <link rel="stylesheet" href="nav.css">
</head>
<body>
    
    <?php include 'nav.php'; ?>

    <div class="container">
        <h1 class="text-center mb-4">Daftar Layanan</h1>

        <!-- Button untuk tambah layanan -->
        <div class="text-end mb-3">
            <a href="tambah_layanan.php" class="btn btn-success">Tambah Layanan</a>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama Layanan</th>
                        <th>Harga per Kg</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td><?php echo $row['nama_layanan']; ?></td>
                            <td class="text-end">Rp <?php echo number_format($row['harga_per_kg'], 2); ?></td>
                            <td class="text-center">
                                <a href="edit_layanan.php?id=<?php echo $row['id_layanan']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="hapus_layanan.php?id=<?php echo $row['id_layanan']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus layanan ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
