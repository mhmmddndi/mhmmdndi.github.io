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

    // Ambil data pelanggan berdasarkan id
    $query = "SELECT * FROM pelanggan WHERE id_pelanggan = '$id_pelanggan'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        // Ambil data pelanggan
        $row = mysqli_fetch_assoc($result);
    } else {
        // Jika pelanggan tidak ditemukan
        echo "Pelanggan tidak ditemukan.";
        exit();
    }
} else {
    // Jika id_pelanggan tidak ada
    echo "ID Pelanggan tidak ditemukan.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data yang di-submit dari form
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $no_telepon = $_POST['no_telepon'];
    $alamat = $_POST['alamat'];

    // Update data pelanggan
    $update_query = "UPDATE pelanggan 
                     SET nama_pelanggan = '$nama_pelanggan', no_telepon = '$no_telepon', alamat = '$alamat' 
                     WHERE id_pelanggan = '$id_pelanggan'";

    if (mysqli_query($conn, $update_query)) {
        // Redirect setelah update berhasil
        header("Location: pelanggan.php");
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
    <title>Edit Pelanggan - Laundry</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link ke Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    
    <?php include 'nav.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Edit Pelanggan</h1>

        <!-- Form Edit Pelanggan -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-container">
                    <form action="edit_pelanggan.php?id=<?php echo $id_pelanggan; ?>" method="POST">
                        <div class="mb-3">
                            <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
                            <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan" value="<?php echo $row['nama_pelanggan']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="no_telepon" class="form-label">No Telepon</label>
                            <input type="text" class="form-control" id="no_telepon" name="no_telepon" value="<?php echo $row['no_telepon']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?php echo $row['alamat']; ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
