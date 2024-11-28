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

// Ambil data pelanggan untuk dropdown
$pelanggan_query = "SELECT id_pelanggan, nama_pelanggan FROM pelanggan";
$pelanggan_result = mysqli_query($conn, $pelanggan_query);

// Ambil data layanan untuk dropdown
$layanan_query = "SELECT id_layanan, nama_layanan, harga_per_kg FROM layanan";
$layanan_result = mysqli_query($conn, $layanan_query);

// Debug jika query gagal
if (!$pelanggan_result || !$layanan_result) {
    die("Error executing query: " . mysqli_error($conn));
}

// Ambil tanggal hari ini
$tanggal_hari_ini = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id_pelanggan = $_POST['id_pelanggan'];
    $id_layanan = $_POST['id_layanan'];
    $berat_kg = $_POST['berat_kg'];
    $tanggal_pesanan = $_POST['tanggal_pesanan'];

    // Ambil harga layanan
    $layanan_query = "SELECT harga_per_kg FROM layanan WHERE id_layanan = '$id_layanan'";
    $layanan_result = mysqli_query($conn, $layanan_query);
    $layanan = mysqli_fetch_assoc($layanan_result);

    $harga_per_kg = $layanan['harga_per_kg'];
    $sub_total = $berat_kg * $harga_per_kg;

    // Insert data ke tabel pesanan
    $insert_pesanan_query = "INSERT INTO pesanan (id_pelanggan, tanggal_pesanan, total_harga) 
                             VALUES ('$id_pelanggan', '$tanggal_pesanan', '$sub_total')";
    if (mysqli_query($conn, $insert_pesanan_query)) {
        // Dapatkan ID pesanan yang baru saja dibuat
        $id_pesanan = mysqli_insert_id($conn);

        // Insert data ke tabel detail_pesanan
        $insert_detail_query = "INSERT INTO detail_pesanan (id_pelanggan, id_pesanan, id_layanan, berat_kg, sub_total) 
                                VALUES ('$id_pelanggan', '$id_pesanan', '$id_layanan', '$berat_kg', '$sub_total')";
        if (mysqli_query($conn, $insert_detail_query)) {
            // Redirect ke halaman dashboard atau sukses
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Error inserting detail pesanan: " . mysqli_error($conn);
        }
    } else {
        echo "Error inserting pesanan: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Pesanan Baru</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

    <?php include 'nav.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Buat Pesanan Baru</h1>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-container">
                    <form action="pesanan.php" method="POST">
                        <div class="mb-3">
                            <label for="id_pelanggan" class="form-label">Pelanggan</label>
                            <select class="form-select" id="id_pelanggan" name="id_pelanggan" required>
                                <option value="" disabled selected>Pilih Pelanggan</option>
                                <?php while ($row = mysqli_fetch_assoc($pelanggan_result)): ?>
                                    <option value="<?php echo $row['id_pelanggan']; ?>"><?php echo $row['nama_pelanggan']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="id_layanan" class="form-label">Layanan</label>
                            <select class="form-select" id="id_layanan" name="id_layanan" required>
                                <option value="" disabled selected>Pilih Layanan</option>
                                <?php while ($row = mysqli_fetch_assoc($layanan_result)): ?>
                                    <option value="<?php echo $row['id_layanan']; ?>">
                                        <?php echo $row['nama_layanan']; ?> - Rp <?php echo number_format($row['harga_per_kg'], 2); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="berat_kg" class="form-label">Berat (Kg)</label>
                            <input type="number" class="form-control" id="berat_kg" name="berat_kg" min="1" step="0.1" required>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_pesanan" class="form-label">Tanggal Pesanan</label>
                            <input type="date" class="form-control" id="tanggal_pesanan" name="tanggal_pesanan" value="<?php echo $tanggal_hari_ini; ?>" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Buat Pesanan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
