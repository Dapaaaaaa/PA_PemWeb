<?php
include '../koneksi.php';
include 'includes/session.php';
requireAdminLogin();

// Get menu ID
if (!isset($_GET['id'])) {
    header("Location: menus.php");
    exit();
}

$menu_id = intval($_GET['id']);

// Handle UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $nama = mysqli_real_escape_string($conn, $_POST['menu_name']);
    $kategori_id = intval($_POST['category']);
    $harga = floatval($_POST['price']);
    $stok = intval($_POST['stock']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['description']);
    $aktif = isset($_POST['aktif']) ? 1 : 0;
    
    // Handle upload gambar baru (optional)
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
              deskripsi = '$deskripsi',
              aktif = $aktif
              $url_gambar_update 
              WHERE id = $menu_id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Menu berhasil diupdate!";
    } else {
        $_SESSION['error'] = "Gagal mengupdate menu!";
    }
    header("Location: menus.php");
    exit();
}

// Get menu details
$query_menu = "SELECT * FROM produk WHERE id = $menu_id";
$result_menu = mysqli_query($conn, $query_menu);

if (mysqli_num_rows($result_menu) == 0) {
    header("Location: menus.php");
    exit();
}

$menu = mysqli_fetch_assoc($result_menu);

// Get categories for dropdown
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
                <h1>Edit Menu: <?php echo htmlspecialchars($menu['nama']); ?></h1>
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
            <!-- Back Button -->
            <div style="margin-bottom: 20px;">
                <a href="menus.php" class="btn btn-secondary">← Kembali ke Daftar Menu</a>
            </div>

            <!-- Edit Form -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Form Edit Menu</h2>
                </div>

                <form class="admin-form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="menu-name">Nama Menu *</label>
                            <input type="text" id="menu-name" name="menu_name" required 
                                   value="<?php echo htmlspecialchars($menu['nama']); ?>"
                                   placeholder="Burger Kebab">
                        </div>

                        <div class="form-group">
                            <label for="menu-category">Kategori *</label>
                            <select id="menu-category" name="category" required>
                                <option value="">Pilih Kategori</option>
                                <?php 
                                mysqli_data_seek($result_kategori, 0);
                                while ($kat = mysqli_fetch_assoc($result_kategori)) { 
                                ?>
                                    <option value="<?php echo $kat['id']; ?>" 
                                            <?php echo ($menu['kategori_id'] == $kat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($kat['nama']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="menu-price">Harga *</label>
                            <input type="number" id="menu-price" name="price" required 
                                   value="<?php echo $menu['harga']; ?>"
                                   placeholder="25000" min="0" step="1000">
                        </div>

                        <div class="form-group">
                            <label for="menu-stock">Stok *</label>
                            <input type="number" id="menu-stock" name="stock" required 
                                   value="<?php echo $menu['stok']; ?>"
                                   placeholder="50" min="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="menu-description">Deskripsi</label>
                        <textarea id="menu-description" name="description" 
                                  placeholder="Deskripsi menu..." rows="5"><?php echo htmlspecialchars($menu['deskripsi']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="menu-image">Gambar Menu (Opsional - kosongkan jika tidak ingin mengubah)</label>
                        <?php if ($menu['url_gambar']) { ?>
                            <div style="margin-bottom: 10px;">
                                <img src="../<?php echo htmlspecialchars($menu['url_gambar']); ?>" 
                                     alt="Current Image" 
                                     style="max-width: 200px; border-radius: 8px; border: 2px solid #e1e4e8;">
                                <p style="color: #666; font-size: 14px; margin-top: 5px;">Gambar saat ini</p>
                            </div>
                        <?php } ?>
                        <input type="file" id="menu-image" name="image" accept="image/*">
                        <small style="color: #666;">Format: JPG, PNG, GIF (Max 2MB)</small>
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="aktif" <?php echo $menu['aktif'] ? 'checked' : ''; ?> 
                                   style="width: 20px; height: 20px; cursor: pointer;">
                            <span style="font-weight: 500;">Menu Aktif (tampilkan di website)</span>
                        </label>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="menus.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
// Set active menu
document.querySelectorAll('.menu-item').forEach(item => {
    if (item.href.includes('menus.php')) {
        item.classList.add('active');
    }
});

// Preview image before upload
document.getElementById('menu-image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.createElement('img');
            preview.src = e.target.result;
            preview.style.maxWidth = '200px';
            preview.style.borderRadius = '8px';
            preview.style.marginTop = '10px';
            preview.style.border = '2px solid #537b2f';
            
            const existingPreview = document.querySelector('.image-preview');
            if (existingPreview) {
                existingPreview.remove();
            }
            
            const container = document.createElement('div');
            container.className = 'image-preview';
            container.appendChild(preview);
            
            const label = document.createElement('p');
            label.textContent = 'Preview gambar baru';
            label.style.color = '#537b2f';
            label.style.fontSize = '14px';
            label.style.marginTop = '5px';
            label.style.fontWeight = '600';
            container.appendChild(label);
            
            e.target.parentElement.appendChild(container);
        };
        reader.readAsDataURL(file);
    }
});
</script>

</body>
</html>
