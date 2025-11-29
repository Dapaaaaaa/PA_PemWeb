<?php
include '../koneksi.php';
include 'includes/session.php';
include '../includes/settings_helper.php';

// Cek login, jika belum login redirect ke login.php
requireAdminLogin();

$query_total_menu = "SELECT COUNT(*) as total FROM produk WHERE aktif = 1";
$result_total_menu = mysqli_query($conn, $query_total_menu);
$total_menu = mysqli_fetch_assoc($result_total_menu)['total'];

$query_total_kategori = "SELECT COUNT(*) as total FROM kategori";
$result_total_kategori = mysqli_query($conn, $query_total_kategori);
$total_kategori = mysqli_fetch_assoc($result_total_kategori)['total'];

$query_total_pesanan = "SELECT COUNT(*) as total FROM pesanan";
$result_total_pesanan = mysqli_query($conn, $query_total_pesanan);
$total_pesanan = mysqli_num_rows($result_total_pesanan) > 0 ? mysqli_fetch_assoc($result_total_pesanan)['total'] : 0;

$query_total_pendapatan = "SELECT SUM(total) as pendapatan FROM pesanan WHERE status = 'completed'";
$result_pendapatan = mysqli_query($conn, $query_total_pendapatan);
$total_pendapatan = 0;
if (mysqli_num_rows($result_pendapatan) > 0) {
    $row_pendapatan = mysqli_fetch_assoc($result_pendapatan);
    $total_pendapatan = $row_pendapatan['pendapatan'] ?? 0;
}

// Ambil pesanan terbaru
$query_pesanan = "SELECT p.*, pl.nama as nama_pelanggan 
                  FROM pesanan p 
                  JOIN pelanggan pl ON p.pelanggan_id = pl.id 
                  ORDER BY p.dibuat_pada DESC LIMIT 4";
$result_pesanan = mysqli_query($conn, $query_pesanan);

// Ambil menu populer
$query_menu_populer = "SELECT p.*, k.nama as kategori_nama 
                       FROM produk p 
                       LEFT JOIN kategori k ON p.kategori_id = k.id 
                       WHERE p.aktif = 1
                       ORDER BY p.id DESC LIMIT 3";
$result_menu_populer = mysqli_query($conn, $query_menu_populer);
?>

<?php include 'includes/admin_header.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="admin-content">
        <!-- Topbar -->
        <div class="admin-topbar">
            <div class="topbar-left">
                <h1>Dashboard</h1>
            </div>
            <div class="topbar-right">
                <div class="admin-user">
                    <div class="admin-user-avatar">
                        <?php echo strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)); ?>
                    </div>
                    <div class="admin-user-info">
                        <h4><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin User'); ?></h4>
                        <p>Administrator</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Area -->
        <div class="admin-main">
            <!-- Dashboard Stats -->
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon green">
                        
                    </div>
                    <div class="stat-info">
                        <h3>Total Menu</h3>
                        <div class="stat-number"><?php echo $total_menu; ?></div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon blue">
                        
                    </div>
                    <div class="stat-info">
                        <h3>Kategori</h3>
                        <div class="stat-number"><?php echo $total_kategori; ?></div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        
                    </div>
                    <div class="stat-info">
                        <h3>Total Pesanan</h3>
                        <div class="stat-number"><?php echo $total_pesanan; ?></div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon red">
                        
                    </div>
                    <div class="stat-info">
                        <h3>Pendapatan</h3>
                        <div class="stat-number">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Pesanan Baru</h2>
                    <a href="orders.php" class="btn btn-primary btn-sm">Lihat Semua</a>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Customer</th>
                            <th>Item</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_pesanan = "SELECT p.*, pl.nama as nama_pelanggan 
                                         FROM pesanan p 
                                         JOIN pelanggan pl ON p.pelanggan_id = pl.id 
                                         ORDER BY p.dibuat_pada DESC LIMIT 10";
                        $result_pesanan = mysqli_query($conn, $query_pesanan);
                        
                        while ($pesanan = mysqli_fetch_assoc($result_pesanan)) {
                            $status_badge = 'info';
                            if ($pesanan['status'] == 'completed') $status_badge = 'success';
                            elseif ($pesanan['status'] == 'processing') $status_badge = 'warning';
                            elseif ($pesanan['status'] == 'cancelled') $status_badge = 'danger';
                        ?>
                        <tr>
                            <td><?php echo $pesanan['nomor_pesanan']; ?></td>
                            <td><?php echo htmlspecialchars($pesanan['nama_pelanggan']); ?></td>
                            <td>Rp <?php echo number_format($pesanan['total'], 0, ',', '.'); ?></td>
                            <td><span class="badge badge-<?php echo $status_badge; ?>"><?php echo ucfirst($pesanan['status']); ?></span></td>
                            <td><?php echo date('d M Y', strtotime($pesanan['dibuat_pada'])); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Popular Menu -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Item Menu Populer</h2>
                    <a href="menus.php" class="btn btn-primary btn-sm">Atur Menu</a>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Pesanan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result_menu_populer) > 0) {
                            while ($menu = mysqli_fetch_assoc($result_menu_populer)) {
                                // Hitung jumlah pesanan (dummy untuk sekarang)
                                $jumlah_pesanan = rand(20, 50);
                        ?>
                        <tr>
                            <td><img src="../<?php echo $menu['url_gambar'] ?: 'assets/img/hero-burger.png'; ?>" alt="<?php echo $menu['nama']; ?>" class="table-image"></td>
                            <td><?php echo htmlspecialchars($menu['nama']); ?></td>
                            <td><?php echo htmlspecialchars($menu['kategori_nama'] ?? '-'); ?></td>
                            <td>Rp <?php echo number_format($menu['harga'], 0, ',', '.'); ?></td>
                            <td><?php echo $jumlah_pesanan; ?> Pesanan</td>
                            <td><span class="badge badge-success">Aktif</span></td>
                        </tr>
                        <?php 
                            }
                        }
                    ?>
                    </tbody>
                </table>
            </div>

            <!-- Store Information -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Informasi Toko</h2>
                    <a href="settings.php" class="btn btn-secondary btn-sm">Edit Pengaturan</a>
                </div>

                <div class="info-box">
                    <p><strong>📍 Alamat:</strong> <?php echo getSetting('store_address', 'Blk. A-B No.53b, Gn. Kelua, Kec. Samarinda Ulu, Kota Samarinda, Kalimantan Timur 75243'); ?></p>
                </div>

                <div class="info-box">
                    <p><strong>📞 WhatsApp:</strong> <?php echo getSetting('store_phone', '+62 859-7490-6945'); ?></p>
                </div>

                <div class="info-box">
                    <p><strong>🕐 Jam Operasional:</strong> Senin - Minggu: <?php echo getSetting('opening_time', '10:00'); ?> - <?php echo getSetting('closing_time', '22:00'); ?></p>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
// Set active menu
document.querySelectorAll('.menu-item').forEach(item => {
    if (item.href === window.location.href) {
        item.classList.add('active');
    }
});
</script>

</body>
</html>