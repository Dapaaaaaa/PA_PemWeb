<?php 
include '../koneksi.php';
include 'includes/session.php';
requireAdminLogin();  // ✅ camelCase sesuai function Anda

// Handle DELETE
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query_delete = "DELETE FROM produk WHERE id = $id";
    if (mysqli_query($conn, $query_delete)) {
        $_SESSION['success'] = "Menu berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus menu!";
    }
    header("Location: menus.php");
    exit();
}

// Handle ADD MENU (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $nama = mysqli_real_escape_string($conn, $_POST['menu_name']);
    $kategori_id = intval($_POST['category']);
    $harga = floatval($_POST['price']);
    $stok = intval($_POST['stock']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Handle upload gambar
    $url_gambar = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/img/products/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $url_gambar = 'assets/img/products/' . $file_name;
        }
    }
    
    $query = "INSERT INTO produk (kategori_id, nama, deskripsi, harga, stok, url_gambar, aktif) 
              VALUES ($kategori_id, '$nama', '$deskripsi', $harga, $stok, '$url_gambar', 1)";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Menu berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan menu: " . mysqli_error($conn);
    }
    header("Location: menus.php");
    exit();
}

// Handle UPDATE MENU
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = intval($_POST['menu_id']);
    $nama = mysqli_real_escape_string($conn, $_POST['menu_name']);
    $kategori_id = intval($_POST['category']);
    $harga = floatval($_POST['price']);
    $stok = intval($_POST['stock']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Handle upload gambar baru
    $url_gambar_update = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/img/products/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $url_gambar_update = ", url_gambar = 'assets/img/products/$file_name'";
        }
    }
    
    $query = "UPDATE produk SET 
              nama = '$nama', 
              kategori_id = $kategori_id, 
              harga = $harga, 
              stok = $stok, 
              deskripsi = '$deskripsi' 
              $url_gambar_update 
              WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Menu berhasil diupdate!";
    } else {
        $_SESSION['error'] = "Gagal mengupdate menu!";
    }
    header("Location: menus.php");
    exit();
}

// READ: Ambil semua menu
$query_menus = "SELECT p.*, k.nama as kategori_nama 
                FROM produk p 
                LEFT JOIN kategori k ON p.kategori_id = k.id 
                ORDER BY p.id DESC";
$result_menus = mysqli_query($conn, $query_menus);

// Ambil kategori untuk dropdown
$query_kategori = "SELECT * FROM kategori ORDER BY nama ASC";
$result_kategori = mysqli_query($conn, $query_kategori);
?>
<?php include 'includes/admin_header.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="admin-content">
        <!-- Topbar -->
        <div class="admin-topbar">
            <div class="topbar-left">
                <h1>Manajemen Menu</h1>
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

            <!-- Add Menu Section -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Tambah Item Menu Baru</h2>
                </div>

                <!-- PERBAIKAN: Tambah method, action, enctype -->
                <form class="admin-form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="menu-name">Nama Menu *</label>
                            <input type="text" id="menu-name" name="menu_name" required 
                                   placeholder="e.g., Premium Cheeseburger">
                        </div>

                        <div class="form-group">
                            <label for="menu-category">Kategori *</label>
                            <select id="menu-category" name="category" required>
                                <option value="">Pilih Kategori</option>
                                <?php
                                mysqli_data_seek($result_kategori, 0);
                                while ($kat = mysqli_fetch_assoc($result_kategori)) {
                                    echo "<option value='{$kat['id']}'>{$kat['nama']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="menu-price">Harga (Rp) *</label>
                            <input type="number" id="menu-price" name="price" required 
                                   placeholder="25000" min="0">
                        </div>

                        <div class="form-group">
                            <label for="menu-stock">Stok</label>
                            <input type="number" id="menu-stock" name="stock" 
                                   placeholder="50" min="0" value="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="menu-description">Deskripsi</label>
                        <textarea id="menu-description" name="description" 
                                  placeholder="Describe your menu item..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Upload Foto</label>
                        <input type="file" name="image" accept="image/*">
                    </div>

                    <button type="submit" class="btn btn-primary">Tambah Menu Item</button>
                </form>
            </div>

            <!-- Menu List Section -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Semua Menu Item</h2>
                    <div>
                        <select id="categoryFilter" style="padding: 8px 16px; border: 2px solid #ddd; border-radius: 8px;">
                            <option value="">Semua Kategori</option>
                            <?php
                            mysqli_data_seek($result_kategori, 0);
                            while ($kat = mysqli_fetch_assoc($result_kategori)) {
                                echo "<option value='{$kat['nama']}'>{$kat['nama']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="menuTableBody">
                        <?php while ($menu = mysqli_fetch_assoc($result_menus)) { ?>
                        <tr data-kategori="<?php echo htmlspecialchars($menu['kategori_nama']); ?>">
                            <td>
                                <img src="../<?php echo $menu['url_gambar'] ?: 'assets/img/no-image.png'; ?>" 
                                     class="table-image" alt="<?php echo $menu['nama']; ?>">
                            </td>
                            <td><?php echo htmlspecialchars($menu['nama']); ?></td>
                            <td><span class="badge badge-info"><?php echo htmlspecialchars($menu['kategori_nama']); ?></span></td>
                            <td>Rp <?php echo number_format($menu['harga'], 0, ',', '.'); ?></td>
                            <td><?php echo $menu['stok']; ?></td>
                            <!-- PERBAIKAN: Tambah kolom Status -->
                            <td>
                                <?php if ($menu['aktif'] == 1) { ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php } else { ?>
                                    <span class="badge badge-danger">Nonaktif</span>
                                <?php } ?>
                            </td>
                            <td class="table-actions">
                                <a href="edit_menu.php?id=<?php echo $menu['id']; ?>" class="btn btn-sm btn-success">Edit</a>
                                <a href="menus.php?action=delete&id=<?php echo $menu['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                            </td>
                        </tr>
                        <?php } ?>
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

// Filter kategori
document.getElementById('categoryFilter').addEventListener('change', function() {
    const selectedCategory = this.value.toLowerCase();
    const rows = document.querySelectorAll('#menuTableBody tr');
    
    rows.forEach(row => {
        const kategori = row.getAttribute('data-kategori').toLowerCase();
        
        if (selectedCategory === '' || kategori === selectedCategory) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>

</body>
</html>