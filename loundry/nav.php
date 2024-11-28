<?php
// Pastikan session dimulai di setiap halaman yang memanggil nav.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Mendapatkan nama file saat ini
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- nav.php -->
<link rel="stylesheet" href="nav.css">

<nav class="navbar navbar-expand-lg" >
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'pesanan.php' ? 'active' : ''; ?>" href="pesanan.php">Pesanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'pelanggan.php' ? 'active' : ''; ?>" href="pelanggan.php">Pelanggan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'layanan.php' ? 'active' : ''; ?>" href="layanan.php">Layanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'detail_pesanan.php' ? 'active' : ''; ?>" href="detail_pesanan.php">Detail Pesanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'peta.php' ? 'active' : ''; ?>" href="peta.php">Peta Cabang</a>
                </li>
            </ul>
            <span class="navbar-text me-3 text-white">
                Hello, <?php echo $_SESSION['username'] ?? 'Guest'; ?>!
            </span>
            <a href="logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </div>
</nav>
