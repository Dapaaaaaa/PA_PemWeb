<?php 
include '../koneksi.php';
include 'includes/session.php';
requireAdminLogin();

// Handle ADD to Menu Display
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $produk_id = intval($_POST['produk_id']);
    $urutan = intval($_POST['urutan']);
    
    // Check if product already exists in menu_display
    $check_product = mysqli_query($conn, "SELECT id FROM menu_display WHERE produk_id = $produk_id");
    if (mysqli_num_rows($check_product) > 0) {
        $_SESSION['error'] = "Produk sudah ada di Menu Kami!";
    } else {
        // Check if urutan already exists
        $check_urutan = mysqli_query($conn, "SELECT id FROM menu_display WHERE urutan = $urutan");
        if (mysqli_num_rows($check_urutan) > 0) {
            $_SESSION['error'] = "Urutan ke-$urutan sudah digunakan! Silakan pilih urutan yang lain.";
        } else {
            $query = "INSERT INTO menu_display (produk_id, urutan, aktif) VALUES ($produk_id, $urutan, 1)";
            if (mysqli_query($conn, $query)) {
                $_SESSION['success'] = "Produk berhasil ditambahkan ke Menu Kami!";
            } else {
                $_SESSION['error'] = "Gagal menambahkan produk!";
            }
        }
    }
    header("Location: featured_products.php");
    exit();
}

// Handle DELETE from Menu Display
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if (mysqli_query($conn, "DELETE FROM menu_display WHERE id = $id")) {
        $_SESSION['success'] = "Produk berhasil dihapus dari Menu Kami!";
    } else {
        $_SESSION['error'] = "Gagal menghapus produk!";
    }
    header("Location: featured_products.php");
    exit();
}

// Handle UPDATE Order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_order') {
    $id = intval($_POST['id']);
    $urutan = intval($_POST['urutan']);
    
    // Check if urutan already exists (excluding current item)
    $check_urutan = mysqli_query($conn, "SELECT id FROM menu_display WHERE urutan = $urutan AND id != $id");
    if (mysqli_num_rows($check_urutan) > 0) {
        $_SESSION['error'] = "Urutan ke-$urutan sudah digunakan oleh produk lain! Silakan pilih urutan yang berbeda.";
    } else {
        if (mysqli_query($conn, "UPDATE menu_display SET urutan = $urutan WHERE id = $id")) {
            $_SESSION['success'] = "Urutan berhasil diupdate!";
        } else {
            $_SESSION['error'] = "Gagal mengupdate urutan!";
        }
    }
    header("Location: featured_products.php");
    exit();
}

// Handle TOGGLE Active
if (isset($_GET['action']) && $_GET['action'] == 'toggle' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "UPDATE menu_display SET aktif = NOT aktif WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Status berhasil diupdate!";
    } else {
        $_SESSION['error'] = "Gagal mengupdate status!";
    }
    header("Location: featured_products.php");
    exit();
}

// Get Featured Products
$query_featured = "SELECT md.*, p.nama, p.deskripsi, p.harga, p.url_gambar, k.nama as kategori_nama
                   FROM menu_display md
                   JOIN produk p ON md.produk_id = p.id
                   LEFT JOIN kategori k ON p.kategori_id = k.id
                   ORDER BY md.urutan ASC";
$result_featured = mysqli_query($conn, $query_featured);

// Get All Active Products (for add dropdown)
$query_products = "SELECT p.*, k.nama as kategori_nama 
                   FROM produk p
                   LEFT JOIN kategori k ON p.kategori_id = k.id
                   WHERE p.aktif = 1
                   AND p.id NOT IN (SELECT produk_id FROM menu_display)
                   ORDER BY k.nama, p.nama";
$result_products = mysqli_query($conn, $query_products);
?>
<?php include 'includes/admin_header.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="admin-content">
        <!-- Topbar -->
        <div class="admin-topbar">
            <div class="topbar-left">
                <h1>Menu Kami (Beranda)</h1>
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
                echo '<script>showToast("' . $_SESSION['success'] . '", "success");</script>';
                unset($_SESSION['success']);
            }
            if (isset($_SESSION['error'])) {
                echo '<script>showToast("' . $_SESSION['error'] . '", "error");</script>';
                unset($_SESSION['error']);
            }
            ?>

            <!-- Add Product Form -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Tambah Produk ke Menu Kami</h2>
                </div>
                <form method="POST" class="admin-form" style="max-width: 600px;">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-group">
                        <label>Pilih Produk</label>
                        <select name="produk_id" required class="form-control">
                            <option value="">-- Pilih Produk --</option>
                            <?php 
                            mysqli_data_seek($result_products, 0);
                            while ($product = mysqli_fetch_assoc($result_products)) {
                                echo '<option value="'.$product['id'].'">'.$product['kategori_nama'].' - '.$product['nama'].'</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Urutan Tampil</label>
                        <input type="number" name="urutan" value="<?php echo mysqli_num_rows($result_featured) + 1; ?>" required class="form-control" min="1">
                        <small style="color: #666; display: block; margin-top: 5px;">Urutan tampilan produk di homepage (kecil ke besar)</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Tambahkan Produk</button>
                </form>
            </div>

            <!-- Featured Products List -->
            <div class="content-section" style="margin-top: 30px;">
                <div class="section-header">
                    <h2>Produk di Menu Kami</h2>
                    <small style="color: #666;">Produk yang ditampilkan di section "Menu Kami!" di homepage</small>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Urutan</th>
                            <th style="width: 100px;">Gambar</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th style="width: 130px;">Harga</th>
                            <th style="text-align: center; width: 100px;">Status</th>
                            <th style="text-align: center; width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (mysqli_num_rows($result_featured) > 0) {
                            while ($item = mysqli_fetch_assoc($result_featured)) {
                        ?>
                        <tr>
                            <td>
                                <form method="POST" style="display: flex; align-items: center; gap: 8px;">
                                    <input type="hidden" name="action" value="update_order">
                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                    <input type="number" name="urutan" value="<?php echo $item['urutan']; ?>" min="1" style="width: 60px; padding: 4px; text-align: center;" required>
                                </form>
                            </td>
                            <td>
                                <?php if ($item['url_gambar']): ?>
                                    <img src="../<?php echo $item['url_gambar']; ?>" alt="<?php echo htmlspecialchars($item['nama']); ?>" class="table-image">
                                <?php else: ?>
                                    <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 8px;"></div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($item['nama']); ?></strong></td>
                            <td><?php echo htmlspecialchars($item['kategori_nama']); ?></td>
                            <td><strong>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></strong></td>
                            <td style="text-align: center;">
                                <span class="badge badge-<?php echo $item['aktif'] ? 'success' : 'danger'; ?>">
                                    <?php echo $item['aktif'] ? 'Aktif' : 'Nonaktif'; ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                    <button onclick="confirmStatusToggle(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['nama']); ?>', <?php echo $item['aktif'] ? 'false' : 'true'; ?>)" 
                                            class="btn btn-sm btn-<?php echo $item['aktif'] ? 'warning' : 'success'; ?>"
                                            style="min-width: 95px; white-space: nowrap;">
                                        <?php echo $item['aktif'] ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                    </button>
                                    <button onclick="confirmRemoveFromFeatured(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['nama']); ?>')" 
                                            class="btn btn-sm btn-danger" style="min-width: 60px;">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="7" style="text-align:center;">Belum ada produk di Menu Kami</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

<script>
function confirmStatusToggle(id, productName, willActivate) {
    const action = willActivate ? 'mengaktifkan' : 'menonaktifkan';
    const statusText = willActivate ? 'Aktifkan' : 'Nonaktifkan';
    
    if (window.confirmStatusUpdate) {
        // Use existing status modal if available
        confirmStatusUpdate(id, action, statusText, function() {
            window.location.href = 'featured_products.php?action=toggle&id=' + id;
        });
    } else {
        // Fallback to custom confirmation
        if (confirm('Yakin ingin ' + action + ' produk "' + productName + '" di Menu Kami?')) {
            window.location.href = 'featured_products.php?action=toggle&id=' + id;
        }
    }
}

function confirmRemoveFromFeatured(id, productName) {
    // Custom confirmation for removing from featured list (not deleting product)
    const modal = document.getElementById('deleteModal');
    if (modal) {
        document.getElementById('deleteMessage').innerHTML = 
            'Yakin ingin menghapus <strong>"' + productName + '"</strong> dari Menu Kami?<br><br>' +
            '<span style="color: #666; font-size: 13px;">Produk tidak akan dihapus dari database, hanya dihapus dari tampilan Menu Kami di homepage.</span>';
        
        modal.classList.add('show');
        
        // Override confirm button action
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        newConfirmBtn.onclick = function() {
            window.location.href = 'featured_products.php?action=delete&id=' + id;
        };
    } else {
        // Fallback if modal not available
        if (confirm('Yakin ingin menghapus "' + productName + '" dari Menu Kami?\n\nProduk tidak akan dihapus dari database, hanya dari tampilan homepage.')) {
            window.location.href = 'featured_products.php?action=delete&id=' + id;
        }
    }
}
</script>

</body>
</html>
