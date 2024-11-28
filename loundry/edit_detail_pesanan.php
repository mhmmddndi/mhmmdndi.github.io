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

    // Ambil data detail pesanan berdasarkan id
    $query = "SELECT * FROM detail_pesanan WHERE id_detail = $id_detail";
    $result = mysqli_query($conn, $query);
    
    // Cek apakah data ditemukan
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "Data tidak ditemukan!";
        exit();
    }
}

// Ambil data pelanggan dan layanan untuk dropdown
$query_pelanggan = "SELECT * FROM pelanggan";
$query_layanan = "SELECT * FROM layanan";
$pelanggan_result = mysqli_query($conn, $query_pelanggan);
$layanan_result = mysqli_query($conn, $query_layanan);

// Ambil harga layanan untuk perhitungan
$harga_layanan = 0;
if ($row) {
    $layanan_id = $row['id_layanan'];
    $query_layanan_harga = "SELECT harga_per_kg FROM layanan WHERE id_layanan = $layanan_id";
    $result_harga = mysqli_query($conn, $query_layanan_harga);
    if ($result_harga) {
        $harga_data = mysqli_fetch_assoc($result_harga);
        $harga_layanan = $harga_data['harga_per_kg'];
    }
}

// Proses update data ketika form disubmit
if (isset($_POST['submit'])) {
    // Ambil data dari form
    $id_pelanggan = $_POST['id_pelanggan'];
    $id_layanan = $_POST['id_layanan'];
    $berat_kg = $_POST['berat_kg'];
    $sub_total = $_POST['sub_total'];

    // Query untuk update data detail pesanan
    $query_update = "
        UPDATE detail_pesanan 
        SET 
            id_pelanggan = '$id_pelanggan', 
            id_layanan = '$id_layanan', 
            berat_kg = '$berat_kg', 
            sub_total = '$sub_total'
        WHERE id_detail = $id_detail
    ";

    if (mysqli_query($conn, $query_update)) {
        // Redirect ke halaman detail pesanan setelah berhasil
        header("Location: detail_pesanan.php");
        exit();
    } else {
        // Jika gagal update
        echo "Gagal memperbarui data: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Detail Pesanan</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 30px;
        }
        h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <?php include 'nav.php'; ?>

    <div class="container">
        <h1 class="text-center mb-4">Edit Detail Pesanan</h1>
        
        <form action="" method="POST">
            <div class="mb-3">
                <label for="id_pelanggan" class="form-label">Pelanggan</label>
                <select class="form-select" id="id_pelanggan" name="id_pelanggan" required>
                    <?php while ($pelanggan = mysqli_fetch_assoc($pelanggan_result)): ?>
                        <option value="<?php echo $pelanggan['id_pelanggan']; ?>" <?php echo ($pelanggan['id_pelanggan'] == $row['id_pelanggan']) ? 'selected' : ''; ?>>
                            <?php echo $pelanggan['nama_pelanggan']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="id_layanan" class="form-label">Layanan</label>
                <select class="form-select" id="id_layanan" name="id_layanan" required onchange="updateSubTotal()">
                    <?php while ($layanan = mysqli_fetch_assoc($layanan_result)): ?>
                        <option value="<?php echo $layanan['id_layanan']; ?>" 
                            data-harga="<?php echo $layanan['harga_per_kg']; ?>"
                            <?php echo ($layanan['id_layanan'] == $row['id_layanan']) ? 'selected' : ''; ?>>
                            <?php echo $layanan['nama_layanan']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="berat_kg" class="form-label">Berat (Kg)</label>
                <input type="number" class="form-control" id="berat_kg" name="berat_kg" value="<?php echo $row['berat_kg']; ?>" required oninput="updateSubTotal()">
            </div>

            <div class="mb-3">
                <label for="sub_total" class="form-label">Subtotal (Rp)</label>
                <input type="number" class="form-control" id="sub_total" name="sub_total" value="<?php echo $row['sub_total']; ?>" readonly>
            </div>

            <button type="submit" class="btn btn-primary" name="submit">Update</button>
            <a href="detail_pesanan.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Update subtotal berdasarkan berat dan harga layanan
        function updateSubTotal() {
            var beratKg = parseFloat(document.getElementById("berat_kg").value);
            var layananSelect = document.getElementById("id_layanan");
            var hargaPerKg = layananSelect.options[layananSelect.selectedIndex].getAttribute("data-harga");
            var subTotal = beratKg * parseFloat(hargaPerKg);
            document.getElementById("sub_total").value = subTotal.toFixed(2);
        }
    </script>
</body>
</html>
