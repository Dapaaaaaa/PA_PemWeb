<?php include 'includes/admin_header.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="admin-content">
        <!-- Topbar -->
        <div class="admin-topbar">
            <div class="topbar-left">
                <h1>🍔 Manajemen Menu</h1>
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
            <!-- Add Menu Section -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Tambah Item Menu Baru</h2>
                </div>

                <form class="admin-form" id="addMenuForm">
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
                                <option value="1">Kebab</option>
                                <option value="2">Burger</option>
                                <option value="3">Minuman</option>
                                <option value="4">Snack</option>
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
                                   placeholder="50" min="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="menu-description">Deskripsi</label>
                        <textarea id="menu-description" name="description" 
                                  placeholder="Describe your menu item..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Upload Foto</label>
                        <div class="file-upload-wrapper">
                            <input type="file" id="menu-image" name="image" accept="image/*">
                            <div class="upload-icon">📷</div>
                            <div class="upload-text">
                                <p><strong>Click untuk Upload</strong> or drag and drop</p>
                                <p>PNG, JPG or JPEG (MAX. 2MB)</p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Tambah Menu Item</button>
                </form>
            </div>

            <!-- Menu List Section -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Semua Menu Item</h2>
                    <div>
                        <input type="text" placeholder="Search menu..." 
                               style="padding: 8px 16px; border: 2px solid #ddd; border-radius: 8px; margin-right: 10px;">
                        <select style="padding: 8px 16px; border: 2px solid #ddd; border-radius: 8px;">
                            <option value="">Semua Kategori</option>
                            <option value="kebab">Kebab</option>
                            <option value="burger">Burger</option>
                            <option value="minuman">Minuman</option>
                            <option value="snack">Snack</option>
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
                    <tbody>
                        <tr>
                            <td><img src="../assets/img/hero-burger.png" alt="Kebab Ayam" class="table-image"></td>
                            <td>Kebab Ayam</td>
                            <td><span class="badge badge-info">Kebab</span></td>
                            <td>Rp 15,000</td>
                            <td>50</td>
                            <td><span class="badge badge-success">Active</span></td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-success" onclick="editMenu(1)">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteMenu(1)">Hapus</button>
                            </td>
                        </tr>
                        <tr>
                            <td><img src="../assets/img/hero-burger.png" alt="Kebab Sapi" class="table-image"></td>
                            <td>Kebab Sapi</td>
                            <td><span class="badge badge-info">Kebab</span></td>
                            <td>Rp 20,000</td>
                            <td>30</td>
                            <td><span class="badge badge-success">Aktif</span></td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-success" onclick="editMenu(2)">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteMenu(2)">Hapus</button>
                            </td>
                        </tr>
                        <tr>
                            <td><img src="../assets/img/hero-burger.png" alt="Burger Beef" class="table-image"></td>
                            <td>Burger Beef</td>
                            <td><span class="badge badge-info">Burger</span></td>
                            <td>Rp 25,000</td>
                            <td>40</td>
                            <td><span class="badge badge-success">Aktif</span></td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-success" onclick="editMenu(3)">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteMenu(3)">Hapus</button>
                            </td>
                        </tr>
                        <tr>
                            <td><img src="../assets/img/hero-burger.png" alt="Burger Chicken" class="table-image"></td>
                            <td>Burger Chicken</td>
                            <td><span class="badge badge-info">Burger</span></td>
                            <td>Rp 20,000</td>
                            <td>45</td>
                            <td><span class="badge badge-success">Aktif</span></td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-success" onclick="editMenu(4)">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteMenu(4)">Hapus</button>
                            </td>
                        </tr>
                        <tr>
                            <td><img src="../assets/img/hero-burger.png" alt="Es Teh Manis" class="table-image"></td>
                            <td>Es Teh Manis</td>
                            <td><span class="badge badge-info">Minuman</span></td>
                            <td>Rp 5,000</td>
                            <td>100</td>
                            <td><span class="badge badge-success">Aktif</span></td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-success" onclick="editMenu(5)">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteMenu(5)">Hapus</button>
                            </td>
                        </tr>
                        <tr>
                            <td><img src="../assets/img/hero-burger.png" alt="French Fries" class="table-image"></td>
                            <td>French Fries</td>
                            <td><span class="badge badge-info">Snack</span></td>
                            <td>Rp 12,000</td>
                            <td>60</td>
                            <td><span class="badge badge-success">Aktif</span></td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-success" onclick="editMenu(6)">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteMenu(6)">Hapus</button>
                            </td>
                        </tr>
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

// Form submission (Frontend only - no backend)
document.getElementById('addMenuForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Menu item added successfully! (Frontend demo only)');
    this.reset();
});

// Edit menu function
function editMenu(id) {
    alert('Edit menu item #' + id + ' (Frontend demo only)');
}

// Delete menu function
function deleteMenu(id) {
    if (confirm('Are you sure you want to delete this menu item?')) {
        alert('Menu item #' + id + ' deleted! (Frontend demo only)');
    }
}

// File upload preview
document.getElementById('menu-image').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(event) {
            console.log('Image uploaded:', event.target.result);
            alert('Image selected successfully!');
        };
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>

</body>
</html>