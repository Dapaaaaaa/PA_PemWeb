<?php
include '../koneksi.php';
include 'includes/session.php';
requireAdminLogin();

// Get order ID
if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = intval($_GET['id']);

// Handle UPDATE STATUS
if (isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $query_update = "UPDATE pesanan SET status = '$new_status' WHERE id = $order_id";
    if (mysqli_query($conn, $query_update)) {
        $_SESSION['success'] = "Status pesanan berhasil diupdate!";
    } else {
        $_SESSION['error'] = "Gagal mengupdate status!";
    }
    header("Location: view_order.php?id=" . $order_id);
    exit();
}

// Get order details
$query_order = "SELECT p.*, 
                pl.nama as nama_pelanggan, 
                pl.email as email_pelanggan, 
                pl.telepon as telepon_pelanggan,
                a.jalan, a.kota, a.kode_pos, a.label as label_alamat, a.catatan as catatan_alamat
                FROM pesanan p 
                JOIN pelanggan pl ON p.pelanggan_id = pl.id 
                LEFT JOIN alamat a ON p.alamat_id = a.id
                WHERE p.id = $order_id";
$result_order = mysqli_query($conn, $query_order);

if (mysqli_num_rows($result_order) == 0) {
    header("Location: orders.php");
    exit();
}

$order = mysqli_fetch_assoc($result_order);

// Get order items
$query_items = "SELECT ip.*, p.nama, p.url_gambar 
                FROM item_pesanan ip 
                JOIN produk p ON ip.produk_id = p.id 
                WHERE ip.pesanan_id = $order_id";
$result_items = mysqli_query($conn, $query_items);

// Status badge color
$status_colors = [
    'pending' => 'info',
    'waiting_confirmation' => 'purple',
    'processing' => 'warning',
    'completed' => 'success',
    'cancelled' => 'danger'
];

$status_labels = [
    'pending' => 'Pending',
    'waiting_confirmation' => 'Menunggu Konfirmasi',
    'processing' => 'Proses',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan'
];

$status_badge = $status_colors[$order['status']] ?? 'info';
$status_label = $status_labels[$order['status']] ?? ucfirst($order['status']);
?>
<?php include 'includes/admin_header.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="admin-content">
        <!-- Topbar -->
        <div class="admin-topbar">
            <div class="topbar-left">
                <h1>Detail Pesanan #<?php echo htmlspecialchars($order['nomor_pesanan']); ?></h1>
            </div>
            <div class="topbar-right">
                <div class="admin-user">
                    <div class="admin-user-avatar"><?php echo strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)); ?></div>
                    <div class="admin-user-info">
                        <h4><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin User'); ?></h4>
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

            <!-- Back Button -->
            <div style="margin-bottom: 20px;">
                <a href="orders.php" class="btn btn-secondary">← Kembali ke Daftar Pesanan</a>
            </div>

            <!-- Order Details Grid -->
            <div class="order-details-grid">
                <!-- Left Column: Order Info -->
                <div class="order-info-section">
                    <!-- Order Status Card -->
                    <div class="content-section">
                        <div class="section-header">
                            <h2>Status Pesanan</h2>
                        </div>
                        <div class="order-status-content">
                            <div class="current-status">
                                <span class="badge badge-<?php echo $status_badge; ?>" style="font-size: 18px; padding: 10px 20px;">
                                    <?php echo $status_label; ?>
                                </span>
                            </div>
                            
                            <?php if ($order['status'] != 'completed' && $order['status'] != 'cancelled') { ?>
                            <form method="POST" class="status-update-form">
                                <input type="hidden" name="action" value="update_status">
                                <div class="form-group">
                                    <label style="font-weight: 600; margin-bottom: 12px; display: block;">Update Status Pesanan</label>
                                    <div class="status-options">
                                        <label class="status-option-card">
                                            <input type="radio" name="status" value="processing" <?php echo ($order['status'] == 'processing') ? 'checked' : ''; ?> required>
                                            <div class="status-option-content">
                                                <!-- <span class="status-icon">🔄</span> -->
                                                <span class="status-text">Proses</span>
                                            </div>
                                        </label>
                                        <label class="status-option-card">
                                            <input type="radio" name="status" value="completed" <?php echo ($order['status'] == 'completed') ? 'checked' : ''; ?> required>
                                            <div class="status-option-content">
                                                <!-- <span class="status-icon">✅</span> -->
                                                <span class="status-text">Selesai</span>
                                            </div>
                                        </label>
                                        <label class="status-option-card">
                                            <input type="radio" name="status" value="cancelled" <?php echo ($order['status'] == 'cancelled') ? 'checked' : ''; ?> required>
                                            <div class="status-option-content">
                                                <!-- <span class="status-icon">❌</span> -->
                                                <span class="status-text">Dibatalkan</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block" style="width: 100%; padding: 12px; font-weight: 600; margin-top: 16px;">Simpan Perubahan Status</button>
                            </form>
                            <?php } else { ?>
                            <div class="status-final-message">
                                <p style="text-align: center; color: #666; margin-top: 15px; font-style: italic;">
                                    <?php echo ($order['status'] == 'completed') ? 'Pesanan telah selesai' : 'Pesanan telah dibatalkan'; ?>
                                </p>
                            </div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Customer Info Card -->
                    <div class="content-section">
                        <div class="section-header">
                            <h2>Informasi Pelanggan</h2>
                        </div>
                        <div class="customer-info">
                            <div class="info-row">
                                <strong>Nama:</strong>
                                <span><?php echo htmlspecialchars($order['nama_pelanggan'] ?? ''); ?></span>
                            </div>
                            <div class="info-row">
                                <strong>Email:</strong>
                                <span><?php echo htmlspecialchars($order['email_pelanggan'] ?? '-'); ?></span>
                            </div>
                            <div class="info-row">
                                <strong>Telepon:</strong>
                                <span><?php echo htmlspecialchars($order['telepon_pelanggan'] ?? ''); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Address Card -->
                    <div class="content-section">
                        <div class="section-header">
                            <h2>Alamat Pengiriman</h2>
                        </div>
                        <div class="address-info">
                            <?php if ($order['alamat_id']) { ?>
                                <div class="info-row">
                                    <strong>Label:</strong>
                                    <span><?php echo htmlspecialchars($order['label_alamat'] ?? '-'); ?></span>
                                </div>
                                <div class="info-row">
                                    <strong>Alamat:</strong>
                                    <span><?php echo nl2br(htmlspecialchars($order['jalan'])); ?></span>
                                </div>
                                <div class="info-row">
                                    <strong>Kota:</strong>
                                    <span><?php echo htmlspecialchars($order['kota'] ?? '-'); ?></span>
                                </div>
                                <?php if ($order['kode_pos']) { ?>
                                <div class="info-row">
                                    <strong>Kode Pos:</strong>
                                    <span><?php echo htmlspecialchars($order['kode_pos']); ?></span>
                                </div>
                                <?php } ?>
                                <?php if ($order['catatan_alamat']) { ?>
                                <div class="info-row">
                                    <strong>Catatan Alamat:</strong>
                                    <span><?php echo nl2br(htmlspecialchars($order['catatan_alamat'])); ?></span>
                                </div>
                                <?php } ?>
                            <?php } else { ?>
                                <p style="color: #666;">Alamat tidak tersedia</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Order Items -->
                <div class="order-items-section">
                    <!-- Order Items Card -->
                    <div class="content-section">
                        <div class="section-header">
                            <h2>Item Pesanan</h2>
                        </div>
                        <div class="order-items-list">
                            <?php 
                            $subtotal = 0;
                            while ($item = mysqli_fetch_assoc($result_items)) { 
                                $subtotal += $item['subtotal'];
                            ?>
                            <div class="order-item">
                                <div class="item-image">
                                    <?php if ($item['url_gambar']) { ?>
                                        <img src="../<?php echo htmlspecialchars($item['url_gambar']); ?>" alt="<?php echo htmlspecialchars($item['nama']); ?>">
                                    <?php } else { ?>
                                        <div class="no-image">No Image</div>
                                    <?php } ?>
                                </div>
                                <div class="item-details">
                                    <h4><?php echo htmlspecialchars($item['nama']); ?></h4>
                                    <p class="item-quantity"><?php echo $item['qty']; ?>x @ Rp <?php echo number_format($item['harga_satuan'], 0, ',', '.'); ?></p>
                                    <?php if ($item['catatan']) { ?>
                                        <p class="item-notes"><small><strong>Catatan:</strong> <?php echo htmlspecialchars($item['catatan']); ?></small></p>
                                    <?php } ?>
                                </div>
                                <div class="item-subtotal">
                                    Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Order Summary Card -->
                    <div class="content-section">
                        <div class="section-header">
                            <h2>Ringkasan Pesanan</h2>
                        </div>
                        <div class="order-summary">
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Ongkos Kirim:</span>
                                <span>Rp <?php echo number_format($order['ongkir'], 0, ',', '.'); ?></span>
                            </div>
                            <div class="summary-row total">
                                <strong>Total:</strong>
                                <strong>Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></strong>
                            </div>
                            
                            <?php if ($order['catatan']) { ?>
                            <div class="order-notes">
                                <strong>Catatan Pesanan:</strong>
                                <p><?php echo nl2br(htmlspecialchars($order['catatan'])); ?></p>
                            </div>
                            <?php } ?>
                            
                            <div class="order-meta">
                                <p><strong>Dibuat pada:</strong> <?php echo date('d M Y, H:i', strtotime($order['dibuat_pada'])); ?></p>
                                <p><strong>Terakhir diupdate:</strong> <?php echo date('d M Y, H:i', strtotime($order['diperbarui_pada'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<style>
.order-details-grid {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 20px;
    margin-top: 20px;
}

.order-info-section,
.order-items-section {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.order-status-content {
    padding: 20px;
}

.current-status {
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.customer-info,
.address-info {
    padding: 20px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.info-row:last-child {
    border-bottom: none;
}

.info-row strong {
    color: #333;
    min-width: 150px;
}

.info-row span {
    color: #666;
    text-align: right;
    flex: 1;
}

.order-items-list {
    padding: 20px;
}

.order-item {
    display: flex;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 15px;
}

.order-item:last-child {
    margin-bottom: 0;
}

.item-image {
    width: 80px;
    height: 80px;
    flex-shrink: 0;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 6px;
}

.no-image {
    width: 100%;
    height: 100%;
    background: #ddd;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    color: #999;
    font-size: 12px;
}

.item-details {
    flex: 1;
}

.item-details h4 {
    margin: 0 0 8px 0;
    font-size: 16px;
    color: #333;
}

.item-quantity {
    color: #666;
    font-size: 14px;
    margin: 0;
}

.item-notes {
    margin: 8px 0 0 0;
    color: #888;
}

.item-subtotal {
    font-weight: 600;
    color: #537b2f;
    font-size: 16px;
    align-self: center;
}

.order-summary {
    padding: 20px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.summary-row.total {
    border-top: 2px solid #537b2f;
    border-bottom: none;
    padding-top: 15px;
    margin-top: 10px;
    font-size: 18px;
    color: #537b2f;
}

.order-notes {
    margin-top: 20px;
    padding: 15px;
    background: #fff9e6;
    border-radius: 6px;
}

.order-notes strong {
    display: block;
    margin-bottom: 8px;
    color: #333;
}

.order-notes p {
    margin: 0;
    color: #666;
}

.order-meta {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.order-meta p {
    margin: 8px 0;
    color: #666;
    font-size: 14px;
}

.badge-purple {
    background: #7c3aed;
    color: white;
}

/* Status Update Form Styles */
.status-update-form {
    margin-top: 20px;
}

.status-options {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.status-option-card {
    display: flex;
    align-items: center;
    padding: 14px 18px;
    border: 2px solid #e1e4e8;
    border-radius: 8px;
    background: #f8f9fa;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.status-option-card:hover {
    background: #f1f3f5;
    border-color: #8da750;
    transform: translateX(4px);
}

.status-option-card input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.status-option-card input[type="radio"]:checked + .status-option-content {
    font-weight: 600;
}

.status-option-card input[type="radio"]:checked ~ .status-option-content::before {
    content: '✓';
    position: absolute;
    right: 18px;
    top: 50%;
    transform: translateY(-50%);
    width: 24px;
    height: 24px;
    background: #537b2f;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: bold;
}

.status-option-card input[type="radio"]:checked {
    & + .status-option-content {
        position: relative;
    }
}

.status-option-card:has(input[type="radio"]:checked) {
    background: #e8f5e9;
    border-color: #537b2f;
    box-shadow: 0 2px 8px rgba(83, 123, 47, 0.2);
}

.status-option-content {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
    position: relative;
}

.status-icon {
    font-size: 24px;
    line-height: 1;
}

.status-text {
    font-size: 15px;
    color: #333;
}

.status-final-message {
    margin-top: 15px;
    padding: 12px;
    background: #f1f3f5;
    border-radius: 6px;
}

@media (max-width: 992px) {
    .order-details-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Set active menu
document.querySelectorAll('.menu-item').forEach(item => {
    if (item.href.includes('orders.php')) {
        item.classList.add('active');
    }
});
</script>

</body>
</html>
