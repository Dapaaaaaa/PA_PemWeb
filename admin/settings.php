<?php 
include '../koneksi.php';
include 'includes/session.php';
requireAdminLogin();

// Handle SAVE SETTINGS
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $updates = [];
    
    foreach ($_POST as $key => $value) {
        if ($key != 'action') {
            $key_escaped = mysqli_real_escape_string($conn, $key);
            
            // Handle array values (untuk checkbox)
            if (is_array($value)) {
                $value_escaped = mysqli_real_escape_string($conn, implode(',', $value));
            } else {
                $value_escaped = mysqli_real_escape_string($conn, $value);
            }
            
            $query = "INSERT INTO settings (setting_key, setting_value) 
                      VALUES ('$key_escaped', '$value_escaped')
                      ON DUPLICATE KEY UPDATE setting_value = '$value_escaped'";
            
            if (mysqli_query($conn, $query)) {
                $updates[] = $key;
            }
        }
    }
    
    if (count($updates) > 0) {
        $_SESSION['success'] = "Settings berhasil disimpan! (" . count($updates) . " item updated)";
    } else {
        $_SESSION['error'] = "Tidak ada perubahan yang disimpan.";
    }
    
    header("Location: settings.php");
    exit();
}

// Load current settings
$settings = [];
$query_settings = "SELECT setting_key, setting_value FROM settings";
$result_settings = mysqli_query($conn, $query_settings);

while ($row = mysqli_fetch_assoc($result_settings)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Helper function
function getSetting($key, $default = '') {
    global $settings;
    return $settings[$key] ?? $default;
}
?>
<?php include 'includes/admin_header.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="admin-content">
        <!-- Topbar -->
        <div class="admin-topbar">
            <div class="topbar-left">
                <h1>Pengaturan Toko</h1>
            </div>
            <div class="topbar-right">
                <div class="admin-user">
                    <div class="admin-user-avatar">A</div>
                    <div class="admin-user-info">
                        <h4>Pengguna Admin</h4>
                        <p>Administrator</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Area -->
        <div class="admin-main">
            <?php
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']);
            }
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            ?>

            <!-- Store Information -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Informasi Toko</h2>
                </div>

                <form class="admin-form" method="POST" action="settings.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="store-name">Nama Toko *</label>
                            <input type="text" id="store-name" name="store_name" required 
                                   value="<?php echo htmlspecialchars(getSetting('store_name', 'OurStuffies')); ?>" placeholder="Nama toko Anda">
                        </div>

                        <div class="form-group">
                            <label for="store-email">Alamat Email *</label>
                            <input type="email" id="store-email" name="store_email" required 
                                   value="<?php echo htmlspecialchars(getSetting('store_email', 'info@ourstuffies.com')); ?>" placeholder="toko@contoh.com">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="store-phone">Nomor Telepon *</label>
                            <input type="tel" id="store-phone" name="store_phone" required 
                                   value="<?php echo htmlspecialchars(getSetting('store_phone', '+62 859-7490-6945')); ?>" placeholder="+62 xxx-xxxx-xxxx">
                        </div>

                        <div class="form-group">
                            <label for="store-whatsapp">Nomor WhatsApp *</label>
                            <input type="tel" id="store-whatsapp" name="store_whatsapp" required 
                                   value="<?php echo htmlspecialchars(getSetting('store_whatsapp', '6285974906945')); ?>" placeholder="628xxxxxxxxxx">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="store-address">Alamat Toko *</label>
                        <textarea id="store-address" name="store_address" required rows="3"
                                  placeholder="Alamat lengkap toko"><?php echo htmlspecialchars(getSetting('store_address', '')); ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="store-city">Kota *</label>
                            <input type="text" id="store-city" name="store_city" required 
                                   value="<?php echo htmlspecialchars(getSetting('store_city', 'Samarinda')); ?>" placeholder="Nama kota">
                        </div>

                        <div class="form-group">
                            <label for="store-postal">Kode Pos *</label>
                            <input type="text" id="store-postal" name="store_postal" required 
                                   value="<?php echo htmlspecialchars(getSetting('store_postal', '75243')); ?>" placeholder="Kode pos">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Informasi Toko</button>
                </form>
            </div>

            <!-- Business Hours -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Jam Operasional</h2>
                </div>

                <form class="admin-form" method="POST" action="settings.php">
                    <div class="info-box">
                        <p>Atur jam operasional toko Anda. Informasi ini akan ditampilkan di situs web Anda dan digunakan untuk memberi tahu pelanggan.</p>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="opening-time">Jam Buka *</label>
                            <input type="time" id="opening-time" name="opening_time" required 
                                   value="<?php echo htmlspecialchars(getSetting('opening_time', '10:00')); ?>">
                        </div>

                        <div class="form-group">
                            <label for="closing-time">Jam Tutup *</label>
                            <input type="time" id="closing-time" name="closing_time" required 
                                   value="<?php echo htmlspecialchars(getSetting('closing_time', '22:00')); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="holiday-note">Catatan Hari Libur</label>
                        <textarea id="holiday-note" name="holiday_note" rows="2"
                                  placeholder="e.g., Closed on national holidays"><?php echo htmlspecialchars(getSetting('holiday_note', '')); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Jam Operasional</button>
                </form>
            </div>

            <!-- Google Maps -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Lokasi & Peta</h2>
                </div>

                <form class="admin-form" id="locationForm" method="POST" action="settings.php">
                    <div class="form-group">
                        <label for="maps-embed">Google Maps Embed URL atau iframe *</label>
                        <textarea id="maps-embed" name="map_embed_url" rows="3" required
                                  placeholder='Either paste the embed <iframe> HTML or only the map "src" URL'><?php echo htmlspecialchars(getSetting('map_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.687140235874!2d117.14484747589199!3d-0.46461823528196516!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df67900560a5033%3A0xd14b9dfd79c14c60!2sOurStuffies!5e0!3m2!1sid!2sid!4v1762506331525!5m2!1sid!2sid')); ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="maps-latitude">Latitude</label>
                            <input type="text" id="maps-latitude" name="map_latitude" 
                                   value="<?php echo htmlspecialchars(getSetting('map_latitude', '-0.464618')); ?>" placeholder="e.g., -0.464618">
                        </div>

                        <div class="form-group">
                            <label for="maps-longitude">Longitude</label>
                            <input type="text" id="maps-longitude" name="map_longitude" 
                                   value="<?php echo htmlspecialchars(getSetting('map_longitude', '117.147623')); ?>" placeholder="e.g., 117.147623">
                        </div>
                    </div>

                    <div class="info-box">
                        <p><strong>Cara mendapatkan kode embed Google Maps:</strong><br>
                        1. Buka Google Maps dan temukan lokasi toko Anda<br>
                        2. Klik "Bagikan" → "Sematkan peta"<br>
                        3. Salin kode iframe dan tempelkan di atas (atau tempel hanya URL src)</p>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Pengaturan Lokasi</button>
                </form>
            </div>

            <!-- Social Media -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Link Media Sosial</h2>
                </div>

                <form class="admin-form" method="POST" action="settings.php">
                    <div class="form-group">
                        <label for="instagram">URL Instagram</label>
                        <input type="url" id="instagram" name="social_instagram" 
                               value="<?php echo htmlspecialchars(getSetting('social_instagram', '')); ?>"
                               placeholder="https://instagram.com/ourstuffies">
                    </div>

                    <div class="form-group">
                        <label for="facebook">URL Facebook</label>
                        <input type="url" id="facebook" name="social_facebook" 
                               value="<?php echo htmlspecialchars(getSetting('social_facebook', '')); ?>"
                               placeholder="https://facebook.com/ourstuffies">
                    </div>

                    <div class="form-group">
                        <label for="twitter">URL Twitter / X</label>
                        <input type="url" id="twitter" name="social_twitter" 
                               value="<?php echo htmlspecialchars(getSetting('social_twitter', '')); ?>"
                               placeholder="https://twitter.com/ourstuffies">
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Link Media Sosial</button>
                </form>
            </div>

            <!-- Delivery Settings -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Pengaturan Pengiriman</h2>
                </div>

                <form class="admin-form" method="POST" action="settings.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="delivery-fee">Biaya Pengiriman Standar (Rp)</label>
                            <input type="number" id="delivery-fee" name="delivery_fee" 
                                   value="<?php echo htmlspecialchars(getSetting('delivery_fee', '10000')); ?>" 
                                   placeholder="10000" min="0">
                        </div>

                        <div class="form-group">
                            <label for="free-delivery">Gratis Pengiriman Di Atas (Rp)</label>
                            <input type="number" id="free-delivery" name="free_delivery_min" 
                                   value="<?php echo htmlspecialchars(getSetting('free_delivery_min', '100000')); ?>" 
                                   placeholder="100000" min="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="delivery-note">Catatan Pengiriman</label>
                        <textarea id="delivery-note" name="delivery_note" rows="3"
                                  placeholder="Instruksi pengiriman khusus..."><?php echo htmlspecialchars(getSetting('delivery_note', '')); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Pengaturan Pengiriman</button>
                </form>
            </div>

            <!-- Sauce Options Settings -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Pilihan Saus Produk</h2>
                </div>

                <form class="admin-form" method="POST" action="settings.php">
                    <div class="info-box">
                        <p>Atur pilihan saus yang tersedia untuk produk. Pelanggan dapat memilih beberapa pilihan saus sekaligus.</p>
                    </div>

                    <div class="form-group">
                        <label>Pilihan Saus yang Tersedia</label>
                        <div style="display: grid; gap: 10px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" id="sauce_no_sauce" name="sauce_options[]" value="tidak-bersaus" 
                                       <?php echo (strpos(getSetting('sauce_options', 'tidak-bersaus,pedas,manis'), 'tidak-bersaus') !== false) ? 'checked' : ''; ?>>
                                <label for="sauce_no_sauce" style="margin: 0; font-weight: normal;">Tidak Bersaus</label>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" id="sauce_spicy" name="sauce_options[]" value="pedas" 
                                       <?php echo (strpos(getSetting('sauce_options', 'tidak-bersaus,pedas,manis'), 'pedas') !== false) ? 'checked' : ''; ?>>
                                <label for="sauce_spicy" style="margin: 0; font-weight: normal;">Pedas</label>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" id="sauce_sweet" name="sauce_options[]" value="manis" 
                                       <?php echo (strpos(getSetting('sauce_options', 'tidak-bersaus,pedas,manis'), 'manis') !== false) ? 'checked' : ''; ?>>
                                <label for="sauce_sweet" style="margin: 0; font-weight: normal;">Manis</label>
                            </div>
                        </div>
                        <small style="color: #666; display: block; margin-top: 8px;">Centang pilihan yang ingin Anda tampilkan ke pelanggan</small>
                    </div>

                    <div class="form-group">
                        <label for="sauce_label">Label untuk Pilihan Saus</label>
                        <input type="text" id="sauce_label" name="sauce_label" 
                               value="<?php echo htmlspecialchars(getSetting('sauce_label', 'Pilih Saus')); ?>" 
                               placeholder="Pilih Saus">
                        <small style="color: #666; display: block; margin-top: 5px;">Label yang akan ditampilkan di halaman keranjang</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Pengaturan Saus</button>
                </form>
            </div>

            <!-- Danger Zone -->
            <div class="content-section" style="border: 2px solid #e74c3c;">
                <div class="section-header">
                    <h2 style="color: #e74c3c;"> Zona Bahaya</h2>
                </div>

                <div class="warning-box">
                    <p><strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan. Harap pastikan sebelum melanjutkan.</p>
                </div>

                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <button class="btn btn-danger" onclick="clearOrders()">Hapus Semua Pesanan</button>
                    <button class="btn btn-danger" onclick="resetDatabase()">Reset Database</button>
                    <button class="btn btn-danger" onclick="deleteAccount()">Hapus Akun Admin</button>
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
</script>

</body>
</html>