<?php include 'includes/admin_header.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="admin-content">
        <!-- Topbar -->
        <div class="admin-topbar">
            <div class="topbar-left">
                <h1>📊 Dashboard</h1>
            </div>
            <div class="topbar-right">
                <div class="admin-user">
                    <div class="admin-user-avatar">A</div>
                    <div class="admin-user-info">
                        <h4>Admin User</h4>
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
                        🍔
                    </div>
                    <div class="stat-info">
                        <h3>Total Menu</h3>
                        <div class="stat-number">24</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon blue">
                        📂
                    </div>
                    <div class="stat-info">
                        <h3>Kategori</h3>
                        <div class="stat-number">6</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        📦
                    </div>
                    <div class="stat-info">
                        <h3>Total Pesanan</h3>
                        <div class="stat-number">142</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon red">
                        💰
                    </div>
                    <div class="stat-info">
                        <h3>Pendapatan</h3>
                        <div class="stat-number">Rp 5.2Jt</div>
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
                        <tr>
                            <td>#ORD20250001</td>
                            <td>John Doe</td>
                            <td>2x Burger Beef, 1x French Fries</td>
                            <td>Rp 62,000</td>
                            <td><span class="badge badge-success">Beres</span></td>
                            <td>2025-01-15</td>
                        </tr>
                        <tr>
                            <td>#ORD20250002</td>
                            <td>Jane Smith</td>
                            <td>1x Kebab Sapi, 1x Jus Jeruk</td>
                            <td>Rp 30,000</td>
                            <td><span class="badge badge-warning">Lagi dimasak</span></td>
                            <td>2025-01-15</td>
                        </tr>
                        <tr>
                            <td>#ORD20250003</td>
                            <td>Ahmad Rahman</td>
                            <td>3x Burger Chicken</td>
                            <td>Rp 60,000</td>
                            <td><span class="badge badge-info">Pending</span></td>
                            <td>2025-01-14</td>
                        </tr>
                        <tr>
                            <td>#ORD20250004</td>
                            <td>Siti Nurhaliza</td>
                            <td>2x Kebab Ayam, 2x Es Teh Manis</td>
                            <td>Rp 40,000</td>
                            <td><span class="badge badge-success">Beres</span></td>
                            <td>2025-01-14</td>
                        </tr>
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
                        <tr>
                            <td><img src="../assets/img/hero-burger.png" alt="Burger" class="table-image"></td>
                            <td>Burger Beef</td>
                            <td>Burger</td>
                            <td>Rp 25,000</td>
                            <td>45 Pesanan</td>
                            <td><span class="badge badge-success">Aktif</span></td>
                        </tr>
                        <tr>
                            <td><img src="../assets/img/hero-burger.png" alt="Kebab" class="table-image"></td>
                            <td>Kebab Sapi</td>
                            <td>Kebab</td>
                            <td>Rp 20,000</td>
                            <td>38 Pesanan</td>
                            <td><span class="badge badge-success">Aktif</span></td>
                        </tr>
                        <tr>
                            <td><img src="../assets/img/hero-burger.png" alt="Burger" class="table-image"></td>
                            <td>Burger Chicken</td>
                            <td>Burger</td>
                            <td>Rp 20,000</td>
                            <td>32 Pesanan</td>
                            <td><span class="badge badge-success">Aktif</span></td>
                        </tr>
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
                    <p><strong>📍 Alamat:</strong> Blk. A-B No.53b, Gn. Kelua, Kec. Samarinda Ulu, Kota Samarinda, Kalimantan Timur 75243</p>
                </div>

                <div class="info-box">
                    <p><strong>📞 WhatsApp:</strong> +62 859-7490-6945</p>
                </div>

                <div class="info-box">
                    <p><strong>🕐 Jam Operasional:</strong> Senin - Minggu: 10:00 AM - 5:00 PM</p>
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