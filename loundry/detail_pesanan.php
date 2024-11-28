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

// Ambil data dari tabel detail_pesanan
$query = "
    SELECT 
        dp.id_detail, 
        p.nama_pelanggan, 
        ps.tanggal_pesanan, 
        l.nama_layanan, 
        dp.berat_kg, 
        dp.sub_total
    FROM 
        detail_pesanan dp
    INNER JOIN pelanggan p ON dp.id_pelanggan = p.id_pelanggan
    INNER JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
    INNER JOIN layanan l ON dp.id_layanan = l.id_layanan
";
$result = mysqli_query($conn, $query);

// Debugging jika query gagal
if (!$result) {
    die("Error executing query: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link ke Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        .container {
            margin-top: 30px;
        }
        h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
        table {
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
        }
    </style>
</head>
<body>
    
    <?php include 'nav.php'; ?>

    <div class="container">
        <h1 class="text-center mb-4">Detail Pesanan</h1>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID Detail</th>
                        <th>Nama Pelanggan</th>
                        <th>Tanggal Pesanan</th>
                        <th>Nama Layanan</th>
                        <th>Berat (Kg)</th>
                        <th>Subtotal (Rp)</th>
                        <th>Aksi</th> <!-- Kolom Aksi -->
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['id_detail']; ?></td>
                                <td><?php echo $row['nama_pelanggan']; ?></td>
                                <td><?php echo $row['tanggal_pesanan']; ?></td>
                                <td><?php echo $row['nama_layanan']; ?></td>
                                <td><?php echo number_format($row['berat_kg'], 2); ?></td>
                                <td><?php echo number_format($row['sub_total'], 2); ?></td>
                                <td class="text-center">
                                    <!-- Tombol Edit -->
                                    <a href="edit_detail_pesanan.php?id=<?php echo $row['id_detail']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <!-- Tombol Hapus -->
                                    <a href="hapus_detail_pesanan.php?id=<?php echo $row['id_detail']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus detail pesanan ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data detail pesanan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
