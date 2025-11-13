<?php include 'includes/admin_header.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="admin-content">
        <!-- Topbar -->
        <div class="admin-topbar">
            <div class="topbar-left">
                <h1>📂 Manajemen Kategori</h1>
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
            <!-- Add Category Section -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Tambah Kategori Baru</h2>
                </div>

                <form class="admin-form" id="addCategoryForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="category-name">Nama Kategori *</label>
                            <input type="text" id="category-name" name="category_name" required 
                                   placeholder="e.g., Burger, Minuman, Snack">
                        </div>

                        <div class="form-group">
                            <label for="category-slug">Slug *</label>
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
                    <input type="text" placeholder="Search categories..." 
                           style="padding: 8px 16px; border: 2px solid #ddd; border-radius: 8px;">
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Kategori</th>
                            <th>Slug</th>
                            <th>Deskripsi</th>
                            <th>Total Item</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><strong>Kebab</strong></td>
                            <td><code>kebab</code></td>
                            <td>Aneka kebab dengan isian pilihan</td>
                            <td><span class="badge badge-info">2 item</span></td>
                            <td>2025-01-10</td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-success" onclick="editCategory(1)">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCategory(1)">Hapus</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td><strong>Burger</strong></td>
                            <td><code>burger</code></td>
                            <td>Burger segar dengan daging berkualitas</td>
                            <td><span class="badge badge-info">2 item</span></td>
                            <td>2025-01-10</td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-success" onclick="editCategory(2)">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCategory(2)">Hapus</button>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td><strong>Minuman</strong></td>
                            <td><code>minuman</code></td>
                            <td>Berbagai minuman segar</td>
                            <td><span class="badge badge-info">2 items</span></td>
                            <td>2025-01-10</td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-success" onclick="editCategory(3)">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCategory(3)">Hapus</button>
                            </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td><strong>Snack</strong></td>
                            <td><code>snack</code></td>
                            <td>Camilan pelengkap</td>
                            <td><span class="badge badge-info">1 item</span></td>
                            <td>2025-01-10</td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-success" onclick="editCategory(4)">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCategory(4)">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Category Statistics -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Statistik Kategori</h2>
                </div>

                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-icon green">
                            🍔
                        </div>
                        <div class="stat-info">
                            <h3>Burger</h3>
                            <div class="stat-number">2</div>
                            <p style="font-size: 12px; color: #999; margin-top: 5px;">Paling Populer</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon orange">
                            🌯
                        </div>
                        <div class="stat-info">
                            <h3>Kebab</h3>
                            <div class="stat-number">2</div>
                            <p style="font-size: 12px; color: #999; margin-top: 5px;">Terlaris</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon blue">
                            🥤
                        </div>
                        <div class="stat-info">
                            <h3>Minuman</h3>
                            <div class="stat-number">2</div>
                            <p style="font-size: 12px; color: #999; margin-top: 5px;">Refreshing</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon red">
                            🍟
                        </div>
                        <div class="stat-info">
                            <h3>Snack</h3>
                            <div class="stat-number">1</div>
                            <p style="font-size: 12px; color: #999; margin-top: 5px;">Side Dishes</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-box">
                <p>💡 <strong>Tip:</strong> Kategori membantu mengatur item menu Anda. Pastikan setiap kategori memiliki slug unik untuk penamaan yang ramah URL.</p>
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

// Auto-generate slug from category name
document.getElementById('category-name').addEventListener('input', function(e) {
    const slug = e.target.value
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    document.getElementById('category-slug').value = slug;
});

// Form submission
document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const categoryName = document.getElementById('category-name').value;
    alert('Category "' + categoryName + '" added successfully! (Frontend demo only)');
    this.reset();
});

// Edit category function
function editCategory(id) {
    alert('Edit category #' + id + '\n\nThis would open an edit form to modify category details.\n\n(Frontend demo only)');
}

// Delete category function
function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this category?\n\nNote: This will also affect all menu items in this category.')) {
        alert('Category #' + id + ' deleted! (Frontend demo only)');
    }
}
</script>

</body>
</html>