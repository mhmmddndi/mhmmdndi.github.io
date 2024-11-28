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

    // Ambil data layanan berdasarkan id_layanan
    $query = "SELECT * FROM layanan WHERE id_layanan = '$id_layanan'";
    $result = mysqli_query($conn, $query);

    // Jika layanan ditemukan
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "Layanan tidak ditemukan.";
        exit();
    }
} else {
    // Jika id_layanan tidak ada di URL
    echo "ID Layanan tidak ditemukan.";
    exit();
}

// Proses form ketika data di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_layanan = $_POST['nama_layanan'];
    $harga_per_kg = $_POST['harga_per_kg'];

    // Update data layanan di database
    $update_query = "UPDATE layanan SET nama_layanan = '$nama_layanan', harga_per_kg = '$harga_per_kg' WHERE id_layanan = '$id_layanan'";

    if (mysqli_query($conn, $update_query)) {
        // Redirect ke halaman daftar layanan
        header("Location: layanan.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Layanan - Laundry</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link ke Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    
    <?php include 'nav.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Edit Layanan</h1>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="edit_layanan.php?id=<?php echo $row['id_layanan']; ?>" method="POST">
                    <div class="mb-3">
                        <label for="nama_layanan" class="form-label">Nama Layanan</label>
                        <input type="text" class="form-control" id="nama_layanan" name="nama_layanan" value="<?php echo $row['nama_layanan']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="harga_per_kg" class="form-label">Harga per Kg</label>
                        <input type="number" class="form-control" id="harga_per_kg" name="harga_per_kg" value="<?php echo $row['harga_per_kg']; ?>" min="0" step="0.01" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Update Layanan</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
