<?php
// Mulai sesi
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Koneksi ke database
include 'koneksi.php';

// Ambil data cabang dari database
$query = "SELECT * FROM cabang";
$result = mysqli_query($conn, $query);

// Menyiapkan array lokasi untuk peta
$locations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $locations[] = $row;
}

// Jika ada permintaan untuk edit
$cabang_edit = null;
if (isset($_GET['id'])) {
    $id_cabang = $_GET['id'];
    $query_edit = "SELECT * FROM cabang WHERE id_cabang = $id_cabang";
    $result_edit = mysqli_query($conn, $query_edit);
    $cabang_edit = mysqli_fetch_assoc($result_edit);
}

// Proses form jika ada POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_cabang = $_POST['nama_cabang'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $keterangan = $_POST['keterangan'];
    $foto = $_FILES['foto']['name'];
    $foto_tmp = $_FILES['foto']['tmp_name'];

    // Jika ada foto baru yang diupload
    if ($foto) {
        $foto_path = 'uploads/' . $foto;
        move_uploaded_file($foto_tmp, $foto_path);
    } else {
        $foto_path = $cabang_edit ? $cabang_edit['foto'] : null;
    }

    if ($cabang_edit) {
        // Update cabang
        $update_query = "UPDATE cabang SET
            nama_cabang = '$nama_cabang',
            latitude = '$latitude',
            longitude = '$longitude',
            keterangan = '$keterangan',
            foto = '$foto_path'
            WHERE id_cabang = " . $cabang_edit['id_cabang'];
        mysqli_query($conn, $update_query);
    } else {
        // Tambah cabang baru
        $insert_query = "INSERT INTO cabang (nama_cabang, latitude, longitude, keterangan, foto) 
        VALUES ('$nama_cabang', '$latitude', '$longitude', '$keterangan', '$foto_path')";
        mysqli_query($conn, $insert_query);
    }

    header("Location: peta.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Cabang</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link ke Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Include Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <style>
        /* Membuat layout dengan Flexbox */
        .container {
            display: flex;
            justify-content: space-between;
            padding-top: 10px;
        }

        .form-container {
            width: 30%;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-top: 10px;
            position: relative; /* Mengatur form agar bisa berada di atas peta */
            z-index: 2; /* Memastikan form tidak tertutup oleh peta */
        }

        #map { 
            height: 550px; 
            width: 70%; /* Membuat peta mengambil 70% dari lebar kontainer */
            margin-left: 0px;
            margin-top: 0px; /* Berikan sedikit margin kiri agar tidak menempel pada tepi halaman */
        }
    </style>
</head>
<body>
    <!-- Memanggil file navbar.php -->
    <?php include 'nav.php'; ?>

    <div class="container">
        <!-- Form Input Cabang -->
        <div class="form-container">
            <h4><?php echo $cabang_edit ? 'Edit Cabang' : 'Tambah Cabang'; ?></h4>
            <form action="peta.php" method="POST" enctype="multipart/form-data">
                <?php if ($cabang_edit): ?>
                    <input type="hidden" name="id_cabang" value="<?php echo $cabang_edit['id_cabang']; ?>">
                    <input type="hidden" name="foto_lama" value="<?php echo $cabang_edit['foto']; ?>"> <!-- Menyimpan foto lama -->
                <?php endif; ?>

                <div class="form-group">
                    <label for="nama_cabang">Nama Cabang:</label>
                    <input type="text" class="form-control" id="nama_cabang" name="nama_cabang" value="<?php echo $cabang_edit ? $cabang_edit['nama_cabang'] : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="latitude">Latitude:</label>
                    <input type="text" class="form-control" id="latitude" name="latitude" value="<?php echo $cabang_edit ? $cabang_edit['latitude'] : ''; ?>" required readonly>
                </div>

                <div class="form-group">
                    <label for="longitude">Longitude:</label>
                    <input type="text" class="form-control" id="longitude" name="longitude" value="<?php echo $cabang_edit ? $cabang_edit['longitude'] : ''; ?>" required readonly>
                </div>

                <div class="form-group">
                    <label for="keterangan">Keterangan:</label>
                    <textarea class="form-control" id="keterangan" name="keterangan"><?php echo $cabang_edit ? $cabang_edit['keterangan'] : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="foto">Upload Foto:</label>
                    <input type="file" class="form-control-file" id="foto" name="foto">
                    <?php if ($cabang_edit && $cabang_edit['foto']): ?>
                        <img src="<?php echo $cabang_edit['foto']; ?>" alt="Foto Cabang" style="width:100px;height:auto;">
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary"><?php echo $cabang_edit ? 'Update Cabang' : 'Simpan Data'; ?></button>
            </form>
        </div>

        <!-- Peta -->
        <div id="map"></div>
    </div>

    <!-- Include Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <script>
        var map = L.map('map').setView([-6.400431, 106.974306], 15);

        map.zoomControl.setPosition('bottomright');

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        // Ambil data lokasi dari PHP
        var locations = <?php echo json_encode($locations); ?>;

        // Menambahkan marker untuk setiap lokasi
        locations.forEach(function(location) {
            var marker = L.marker([location.latitude, location.longitude]).addTo(map);
            marker.bindPopup(
                "<b>" + location.nama_cabang + "</b><br>" +
                "Lokasi dengan ID: " + location.id_cabang + "<br>" +
                "<img src='" + location.foto + "' alt='Gambar Lokasi' style='width:100px;height:auto;'><br><br>" +
                "Keterangan: " + location.keterangan + "<br>" +
                "<a href='peta.php?id=" + location.id_cabang + "'>Edit</a> | " +
                "<a href='hapusmarker.php?id=" + location.id_cabang + "' onclick='return confirm(\"Apakah Anda yakin ingin menghapus lokasi ini?\");'>Hapus</a>"
            );
        });

        map.on('click', function(e) {
            document.getElementById('latitude').value = e.latlng.lat.toFixed(6);
            document.getElementById('longitude').value = e.latlng.lng.toFixed(6);
        });
    </script>
</body>
</html>
