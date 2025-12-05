<?php
include '../koneksi.php';
include 'includes/session.php';
requireAdminLogin();

// Handle UPDATE STATUS
if (isset($_GET['action']) && $_GET['action'] == 'update_status' && isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    
    $query_update = "UPDATE pesanan SET status = '$status' WHERE id = $id";
    if (mysqli_query($conn, $query_update)) {
        $_SESSION['success'] = "Status pesanan berhasil diupdate!";
    } else {
        $_SESSION['error'] = "Gagal mengupdate status!";
    }
    header("Location: orders.php");
    exit();
}

// Statistik pesanan berdasarkan status
$query_pending = "SELECT COUNT(*) as total FROM pesanan WHERE status = 'pending'";
$result_pending = mysqli_query($conn, $query_pending);
$total_pending = mysqli_fetch_assoc($result_pending)['total'];

$query_waiting = "SELECT COUNT(*) as total FROM pesanan WHERE status = 'waiting_confirmation'";
$result_waiting = mysqli_query($conn, $query_waiting);
$total_waiting = mysqli_fetch_assoc($result_waiting)['total'];

$query_processing = "SELECT COUNT(*) as total FROM pesanan WHERE status = 'processing'";
$result_processing = mysqli_query($conn, $query_processing);
$total_processing = mysqli_fetch_assoc($result_processing)['total'];

$query_completed = "SELECT COUNT(*) as total FROM pesanan WHERE status = 'completed'";
$result_completed = mysqli_query($conn, $query_completed);
$total_completed = mysqli_fetch_assoc($result_completed)['total'];

$query_cancelled = "SELECT COUNT(*) as total FROM pesanan WHERE status = 'cancelled'";
$result_cancelled = mysqli_query($conn, $query_cancelled);
$total_cancelled = mysqli_fetch_assoc($result_cancelled)['total'];

// Ambil semua pesanan
$query_orders = "SELECT p.*, pl.nama as nama_pelanggan, pl.telepon 
                 FROM pesanan p 
                 JOIN pelanggan pl ON p.pelanggan_id = pl.id 
                 ORDER BY p.dibuat_pada DESC";
$result_orders = mysqli_query($conn, $query_orders);
?>
<?php include 'includes/admin_header.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="admin-content">
        <!-- Topbar -->
        <div class="admin-topbar">
            <div class="topbar-left">
                <h1>Manajemen Pesanan</h1>
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

            <!-- Order Stats -->
            <div class="dashboard-stats">
                <div class="stat-card">
                    <!-- <div class="stat-icon orange">
                        
                    </div> -->
                    <div class="stat-info">
                        <h3>Pending</h3>
                        <div class="stat-number"><?php echo $total_pending; ?></div>
                    </div>
                </div>

                <div class="stat-card">
                    <!-- <div class="stat-icon purple">
                        
                    </div> -->
                    <div class="stat-info">
                        <h3>Menunggu Konfirmasi</h3>
                        <div class="stat-number"><?php echo $total_waiting; ?></div>
                    </div>
                </div>

                <div class="stat-card">
                    <!-- <div class="stat-icon blue">
                        
                    </div> -->
                    <div class="stat-info">
                        <h3>Proses</h3>
                        <div class="stat-number"><?php echo $total_processing; ?></div>
                    </div>
                </div>

                <div class="stat-card">
                    <!-- <div class="stat-icon green">
                        
                    </div> -->
                    <div class="stat-info">
                        <h3>Beres</h3>
                        <div class="stat-number"><?php echo $total_completed; ?></div>
                    </div>
                </div>

                <div class="stat-card">
                    <!-- <div class="stat-icon red">
                        
                    </div> -->
                    <div class="stat-info">
                        <h3>Batal</h3>
                        <div class="stat-number"><?php echo $total_cancelled; ?></div>
                    </div>
                </div>
            </div>

            <!-- Orders List -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Semua Pesanan</h2>
                    <div>
                        <input type="text" placeholder="Search by order ID or customer..." 
                               style="padding: 8px 16px; border: 2px solid #ddd; border-radius: 8px; margin-right: 10px; width: 250px;">
                        <select style="padding: 8px 16px; border: 2px solid #ddd; border-radius: 8px;">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="waiting_confirmation">Menunggu Konfirmasi</option>
                            <option value="processing">Proses</option>
                            <option value="completed">Beres</option>
                            <option value="cancelled">Batal</option>
                        </select>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Pelanggan</th>
                            <th>No Kontak</th>
                            <th>Item</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (mysqli_num_rows($result_orders) > 0) {
                            while ($order = mysqli_fetch_assoc($result_orders)) {
                                // Ambil item pesanan
                                $query_items = "SELECT ip.*, p.nama 
                                               FROM item_pesanan ip 
                                               JOIN produk p ON ip.produk_id = p.id 
                                               WHERE ip.pesanan_id = " . $order['id'];
                                $result_items = mysqli_query($conn, $query_items);
                                
                                $items_text = '';
                                while ($item = mysqli_fetch_assoc($result_items)) {
                                    $items_text .= $item['qty'] . 'x ' . $item['nama'] . '<br>';
                                }
                                
                                // Badge status
                                $status_badge = 'info';
                                if ($order['status'] == 'completed') $status_badge = 'success';
                                elseif ($order['status'] == 'waiting_confirmation') $status_badge = 'purple';
                                elseif ($order['status'] == 'processing') $status_badge = 'warning';
                                elseif ($order['status'] == 'cancelled') $status_badge = 'danger';
                        ?>
                        <tr>
                            <td><strong><?php echo $order['nomor_pesanan']; ?></strong></td>
                            <td><?php echo htmlspecialchars($order['nama_pelanggan']); ?></td>
                            <td><?php echo $order['telepon']; ?></td>
                            <td><small><?php echo $items_text; ?></small></td>
                            <td><strong>Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></strong></td>
                            <td><span class="badge badge-<?php echo $status_badge; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($order['dibuat_pada'])); ?></td>
                            <td class="table-actions">
                                <a href="view_order.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-success">Lihat</a>
                                <?php if ($order['status'] != 'completed' && $order['status'] != 'cancelled') { ?>
                                    <select onchange="updateStatus(<?php echo $order['id']; ?>, this.value)" class="btn btn-sm btn-primary" style="padding: 4px 8px;">
                                        <option value="">Update Status</option>
                                        <option value="processing">Proses</option>
                                        <option value="completed">Selesai</option>
                                        <option value="cancelled">Dibatalkan</option>
                                    </select>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="8" style="text-align:center;">Belum ada pesanan</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
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

// Update status function
function updateStatus(orderId, status) {
    if (status && confirm('Update status pesanan ke: ' + status + '?')) {
        window.location.href = 'orders.php?action=update_status&id=' + orderId + '&status=' + status;
    }
}
</script>

</body>
</html>