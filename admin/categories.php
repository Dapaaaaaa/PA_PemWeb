<?php
include '../koneksi.php';
include 'includes/session.php';
requireAdminLogin();

// Handle DELETE
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query_delete = "DELETE FROM kategori WHERE id = $id";
    if (mysqli_query($conn, $query_delete)) {
        $_SESSION['success'] = "Kategori berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus kategori!";
    }
    header("Location: categories.php");
    exit();
}

// Handle ADD CATEGORY (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $nama = mysqli_real_escape_string($conn, $_POST['category_name']);
    $slug = mysqli_real_escape_string($conn, $_POST['slug']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['description']);
    
    $query = "INSERT INTO kategori (nama, slug, deskripsi) VALUES ('$nama', '$slug', '$deskripsi')";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Kategori berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan kategori: " . mysqli_error($conn);
    }
    header("Location: categories.php");
    exit();
}

// Handle UPDATE CATEGORY
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = intval($_POST['category_id']);
    $nama = mysqli_real_escape_string($conn, $_POST['category_name']);
    $slug = mysqli_real_escape_string($conn, $_POST['slug']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['description']);
    
    $query = "UPDATE kategori SET nama = '$nama', slug = '$slug', deskripsi = '$deskripsi' WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Kategori berhasil diupdate!";
    } else {
        $_SESSION['error'] = "Gagal mengupdate kategori!";
    }
    header("Location: categories.php");
    exit();
}

// READ: Ambil semua kategori
$query_categories = "SELECT k.*, COUNT(p.id) as total_produk 
                     FROM kategori k 
                     LEFT JOIN produk p ON k.id = p.kategori_id 
                     GROUP BY k.id 
                     ORDER BY k.id ASC";
$result_categories = mysqli_query($conn, $query_categories);
?>
<?php include 'includes/admin_header.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="admin-content">
        <!-- Topbar -->
        <div class="admin-topbar">
            <div class="topbar-left">
                <h1>Manajemen Kategori</h1>
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

            <!-- Add Category Section -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Tambah Kategori Baru</h2>
                </div>

                <form class="admin-form" method="POST">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="category-name">Nama Kategori *</label>
                            <input type="text" id="category-name" name="category_name" required 
                                   placeholder="e.g., Burger, Minuman, Snack">
                        </div>

                        <div class="form-group">
                            <label for="category-slug">Selogan *</label>
                            <input type="text" id="category-slug" name="slug" required 
                                   placeholder="e.g., burger, minuman, snack">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="category-description">Deskripsi</label>
                        <textarea id="category-description" name="description" 
                                  placeholder="Describe this category..." rows="4"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Tambah Kategori</button>
                </form>
            </div>

            <!-- Category List Section -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Semua Kategori</h2>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Kategori</th>
                            <th>Selogan</th>
                            <th>Deskripsi</th>
                            <th>Total Item</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($kategori = mysqli_fetch_assoc($result_categories)) { ?>
                        <tr>
                            <td><?php echo $kategori['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($kategori['nama']); ?></strong></td>
                            <td><code><?php echo htmlspecialchars($kategori['slug']); ?></code></td>
                            <td><?php echo htmlspecialchars($kategori['deskripsi']); ?></td>
                            <td><span class="badge badge-info"><?php echo $kategori['total_produk']; ?> item</span></td>
                            <td><?php echo date('Y-m-d', strtotime($kategori['dibuat_pada'])); ?></td>
                            <td class="table-actions">
                                <a href="edit_category.php?id=<?php echo $kategori['id']; ?>" class="btn btn-sm btn-success">Edit</a>
                                <button onclick="confirmDelete(<?php echo $kategori['id']; ?>, '<?php echo htmlspecialchars($kategori['nama']); ?>')" 
                                        class="btn btn-sm btn-danger">Hapus</button>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- <div class="info-box">
                <p>💡 <strong>Tip:</strong> Kategori membantu mengatur item menu Anda. Pastikan setiap kategori memiliki slug unik untuk penamaan yang ramah URL.</p>
            </div> -->
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

// Auto-generate slug from category name
document.getElementById('category-name').addEventListener('input', function(e) {
    const slug = e.target.value
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    document.getElementById('category-slug').value = slug;
});

// Show toast notifications
<?php if (isset($_SESSION['success'])): ?>
showToast(<?php echo json_encode($_SESSION['success']); ?>, 'success', 3000);
<?php unset($_SESSION['success']); endif; ?>

<?php if (isset($_SESSION['error'])): ?>
showToast(<?php echo json_encode($_SESSION['error']); ?>, 'error', 3000);
<?php unset($_SESSION['error']); endif; ?>

// Delete confirmation function
function confirmDelete(id, name) {
    if (confirm('Yakin ingin menghapus kategori "' + name + '"?\n\nSemua produk dalam kategori ini akan terpengaruh!')) {
        window.location.href = 'categories.php?action=delete&id=' + id;
    }
}
</script>

</body>
</html>