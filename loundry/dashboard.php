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

// Ambil data ringkasan untuk ditampilkan di card
$total_pelanggan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pelanggan"))['total'];
$total_pesanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan"))['total'];
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) AS total FROM pesanan"))['total'];

// Ambil 3 pesanan terbaru
$query = "
    SELECT 
        pesanan.id_pesanan, 
        pelanggan.nama_pelanggan, 
        pelanggan.alamat, 
        pesanan.tanggal_pesanan, 
        pesanan.total_harga
    FROM pesanan
    JOIN pelanggan ON pesanan.id_pelanggan = pelanggan.id_pelanggan
    ORDER BY pesanan.tanggal_pesanan DESC
    LIMIT 3
";
$result = mysqli_query($conn, $query);

// Ambil data pendapatan harian bulan ini
$pendapatan_harian_query = "
    SELECT 
        DATE(tanggal_pesanan) AS tanggal, 
        SUM(total_harga) AS pendapatan
    FROM pesanan
    WHERE MONTH(tanggal_pesanan) = MONTH(CURRENT_DATE) AND YEAR(tanggal_pesanan) = YEAR(CURRENT_DATE)
    GROUP BY DATE(tanggal_pesanan)
    ORDER BY tanggal
";
$pendapatan_harian_result = mysqli_query($conn, $pendapatan_harian_query);

$pendapatan_harian = [];
while ($row = mysqli_fetch_assoc($pendapatan_harian_result)) {
    $pendapatan_harian[] = $row;
}

// Ambil data pendapatan bulanan tahun ini
$pendapatan_bulanan_query = "
    SELECT 
        MONTH(tanggal_pesanan) AS bulan, 
        SUM(total_harga) AS pendapatan
    FROM pesanan
    WHERE YEAR(tanggal_pesanan) = YEAR(CURRENT_DATE)
    GROUP BY MONTH(tanggal_pesanan)
    ORDER BY bulan
";
$pendapatan_bulanan_result = mysqli_query($conn, $pendapatan_bulanan_query);

$pendapatan_bulanan = [];
while ($row = mysqli_fetch_assoc($pendapatan_bulanan_result)) {
    $pendapatan_bulanan[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Laundry</title>
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
        <h1 class="text-center mb-4">Dashboard</h1>

        <!-- Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h3 class="mb-2">Total Pelanggan</h3>
                    <p class="display-6"><?php echo $total_pelanggan; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h3 class="mb-2">Total Pesanan</h3>
                    <p class="display-6"><?php echo $total_pesanan; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h3 class="mb-2">Total Pendapatan</h3>
                    <p class="display-6">Rp <?php echo number_format($total_pendapatan, 2); ?></p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <h2 class="mt-5 mb-3 text-center">Pesanan Terbaru</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Alamat</th>
                        <th>Tanggal Pesanan</th>
                        <th>Total Harga</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td><?php echo $row['nama_pelanggan']; ?></td>
                            <td><?php echo $row['alamat']; ?></td>
                            <td class="text-center"><?php echo $row['tanggal_pesanan']; ?></td>
                            <td class="text-end">Rp <?php echo number_format($row['total_harga'], 2); ?></td>
                            <td class="text-center">
                                <a href="detail_pesanan.php?id=<?php echo $row['id_pesanan']; ?>" class="btn btn-info btn-sm">Lihat Detail</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Charts -->
        <div class="row mt-5">
            <!-- Grafik Pendapatan Harian -->
            <div class="col-md-6">
                <div class="chart-container">
                    <h4 class="text-center">Pendapatan Harian</h4>
                    <canvas id="pendapatanHarianChart"></canvas>
                </div>
            </div>

            <!-- Grafik Pendapatan Bulanan -->
            <div class="col-md-6">
                <div class="chart-container">
                    <h4 class="text-center">Pendapatan Bulanan</h4>
                    <canvas id="pendapatanBulananChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const pendapatanHarianCtx = document.getElementById('pendapatanHarianChart').getContext('2d');
        const pendapatanBulananCtx = document.getElementById('pendapatanBulananChart').getContext('2d');

        // Data Pendapatan Harian
        const pendapatanHarianData = {
            labels: <?php echo json_encode(array_column($pendapatan_harian, 'tanggal')); ?>,
            datasets: [{
                label: 'Pendapatan Harian (Rp)',
                data: <?php echo json_encode(array_column($pendapatan_harian, 'pendapatan')); ?>,
                backgroundColor: 'rgba(0, 123, 255, 0.5)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 2,
                fill: true,
            }]
        };

        // Data Pendapatan Bulanan
        const pendapatanBulananData = {
            labels: <?php echo json_encode(array_map(function($row) {
                return date('F', mktime(0, 0, 0, $row['bulan'], 10));
            }, $pendapatan_bulanan)); ?>,
            datasets: [{
                label: 'Pendapatan Bulanan (Rp)',
                data: <?php echo json_encode(array_column($pendapatan_bulanan, 'pendapatan')); ?>,
                backgroundColor: 'rgba(40, 167, 69, 0.5)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 2,
                fill: true,
            }]
        };

        // Render Charts
        new Chart(pendapatanHarianCtx, {
            type: 'line',
            data: pendapatanHarianData,
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true }, x: { beginAtZero: true } }
            }
        });

        new Chart(pendapatanBulananCtx, {
            type: 'bar',
            data: pendapatanBulananData,
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true }, x: { beginAtZero: true } }
            }
        });
    </script>
</body>
</html>
