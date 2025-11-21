<?php
include '../koneksi.php';
include 'includes/session.php';
requireAdminLogin();

// Cek apakah ada ID
if (!isset($_GET['id'])) {
    header("Location: categories.php");
    exit();
}

$id = intval($_GET['id']);

// Ambil data kategori berdasarkan ID
$query = "SELECT * FROM kategori WHERE id = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = "Kategori tidak ditemukan!";
    header("Location: categories.php");
    exit();
}

$kategori = mysqli_fetch_assoc($result);
?>
<?php include 'includes/admin_header.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="admin-content">
        <!-- Topbar -->
        <div class="admin-topbar">
            <div class="topbar-left">
                <h1> Edit Kategori</h1>
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
            <div class="content-section">
                <div class="section-header">
                    <h2>Edit Kategori: <?php echo htmlspecialchars($kategori['nama']); ?></h2>
                    <a href="categories.php" class="btn btn-secondary btn-sm">← Kembali</a>
                </div>

                <?php
                if (isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
                ?>

                <form class="admin-form" method="POST" action="categories.php">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="category_id" value="<?php echo $kategori['id']; ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="category-name">Nama Kategori *</label>
                            <input type="text" id="category-name" name="category_name" required 
                                   value="<?php echo htmlspecialchars($kategori['nama']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="category-slug">Slug *</label>
                            <input type="text" id="category-slug" name="slug" required 
                                   value="<?php echo htmlspecialchars($kategori['slug']); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="category-description">Deskripsi</label>
                        <textarea id="category-description" name="description" rows="4"><?php echo htmlspecialchars($kategori['deskripsi']); ?></textarea>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="categories.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
// Auto-generate slug from category name
document.getElementById('category-name').addEventListener('input', function(e) {
    const slug = e.target.value
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    document.getElementById('category-slug').value = slug;
});
</script>

</body>
</html>
