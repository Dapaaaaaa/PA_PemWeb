<?php
include '../koneksi.php';
include 'includes/session.php';

requireAdminLogin();

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'add' || $action === 'edit') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $title = mysqli_real_escape_string($conn, $_POST['title']);
            $description = mysqli_real_escape_string($conn, $_POST['description']);
            $button_text = mysqli_real_escape_string($conn, $_POST['button_text']);
            $button_link = mysqli_real_escape_string($conn, $_POST['button_link']);
            $urutan = intval($_POST['urutan']);
            $aktif = isset($_POST['aktif']) ? 1 : 0;
            
            // Handle image upload
            $image_url = $_POST['existing_image'] ?? '';
            
            if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === 0) {
                $upload_dir = '../assets/img/banners/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_ext = strtolower(pathinfo($_FILES['banner_image']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($file_ext, $allowed)) {
                    $new_filename = 'banner_' . time() . '.' . $file_ext;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $upload_path)) {
                        $image_url = 'assets/img/banners/' . $new_filename;
                        
                        // Delete old image if editing
                        if ($action === 'edit' && !empty($_POST['existing_image'])) {
                            $old_image = '../' . $_POST['existing_image'];
                            if (file_exists($old_image)) {
                                unlink($old_image);
                            }
                        }
                    }
                }
            }
            
            if ($action === 'add') {
                $query = "INSERT INTO banners (title, description, image_url, button_text, button_link, urutan, aktif) 
                          VALUES ('$title', '$description', '$image_url', '$button_text', '$button_link', $urutan, $aktif)";
                $message = 'Banner berhasil ditambahkan!';
            } else {
                $query = "UPDATE banners SET 
                          title = '$title', 
                          description = '$description', 
                          image_url = '$image_url', 
                          button_text = '$button_text', 
                          button_link = '$button_link', 
                          urutan = $urutan, 
                          aktif = $aktif 
                          WHERE id = $id";
                $message = 'Banner berhasil diupdate!';
            }
            
            if (mysqli_query($conn, $query)) {
                $_SESSION['success_message'] = $message;
            } else {
                $_SESSION['error_message'] = 'Error: ' . mysqli_error($conn);
            }
            
            header('Location: banners.php');
            exit;
        }
        
        if ($action === 'delete') {
            $id = intval($_POST['id']);
            
            // Get image path to delete file
            $result = mysqli_query($conn, "SELECT image_url FROM banners WHERE id = $id");
            if ($row = mysqli_fetch_assoc($result)) {
                $image_path = '../' . $row['image_url'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            mysqli_query($conn, "DELETE FROM banners WHERE id = $id");
            $_SESSION['success_message'] = 'Banner berhasil dihapus!';
            header('Location: banners.php');
            exit;
        }
        
        if ($action === 'toggle_status') {
            $id = intval($_POST['id']);
            $status = intval($_POST['status']);
            mysqli_query($conn, "UPDATE banners SET aktif = $status WHERE id = $id");
            echo json_encode(['success' => true]);
            exit;
        }
    }
}

// Get all banners
$banners = mysqli_query($conn, "SELECT * FROM banners ORDER BY urutan ASC, id DESC");
?>

<?php include 'includes/admin_header.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

<!-- Main Content -->
<div class="admin-content">
    <!-- Topbar -->
    <div class="admin-topbar">
        <div class="topbar-left">
            <h1>Kelola Banner</h1>
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
        <?php if (isset($_SESSION['success_message'])): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof showToast === 'function') {
                        showToast('<?php echo addslashes($_SESSION['success_message']); ?>', 'success');
                    }
                });
            </script>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof showToast === 'function') {
                        showToast('<?php echo addslashes($_SESSION['error_message']); ?>', 'error');
                    }
                });
            </script>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <!-- Info Box -->
        <div style="margin-bottom: 25px; padding: 16px 20px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 8px;">
            <p style="margin: 0; color: #856404;">
                <strong>Catatan:</strong> Maksimal 5 banner aktif. Banner akan otomatis berganti setiap 5 detik. Gunakan urutan untuk mengatur posisi banner.
            </p>
        </div>

        <!-- Banners List -->
        <div class="content-section">
            <div class="section-header">
                <h2>Daftar Banner</h2>
                <button class="btn btn-primary" onclick="openModal()">+ Tambah Banner</button>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Urutan</th>
                        <th style="width: 150px;">Preview</th>
                        <th style="text-align: center; width: 120px;">Status</th>
                        <th style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($banners) > 0): ?>
                        <?php while($banner = mysqli_fetch_assoc($banners)): ?>
                        <tr>
                            <td><strong><?php echo $banner['urutan']; ?></strong></td>
                            <td>
                                <img src="../<?php echo htmlspecialchars($banner['image_url']); ?>" 
                                     alt="Banner" 
                                     class="table-image"
                                     style="width: 120px; height: 70px;">
                            </td>
                            <td style="text-align: center;">
                                <span class="badge badge-<?php echo $banner['aktif'] ? 'success' : 'danger'; ?>">
                                    <?php echo $banner['aktif'] ? 'Aktif' : 'Nonaktif'; ?>
                                </span>
                            </td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-warning" onclick='editBanner(<?php echo json_encode($banner); ?>)'>Edit</button>
                                <button class="btn btn-sm btn-<?php echo $banner['aktif'] ? 'secondary' : 'success'; ?>\" 
                                        onclick="toggleStatus(<?php echo $banner['id']; ?>, <?php echo $banner['aktif'] ? 0 : 1; ?>)">
                                    <?php echo $banner['aktif'] ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="confirmDeleteBanner(<?php echo $banner['id']; ?>, <?php echo $banner['id']; ?>)">Hapus</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px; color: #666;">
                                Belum ada banner. Klik "Tambah Banner" untuk memulai.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div id="bannerModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); overflow: auto;">
    <div style="background-color: white; margin: 5% auto; padding: 30px; border-radius: 12px; max-width: 600px; position: relative;">
        <span onclick="closeModal()" style="position: absolute; right: 20px; top: 20px; font-size: 28px; font-weight: bold; cursor: pointer; color: #999;">&times;</span>
        <h3 id="modalTitle" style="margin-top: 0; margin-bottom: 25px; color: #333;">Tambah Banner Baru</h3>
        
        <form method="POST" enctype="multipart/form-data" id="bannerForm" class="admin-form">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="bannerId">
            <input type="hidden" name="existing_image" id="existingImage">
            
            <div class="form-group">
                <label>Urutan</label>
                <input type="number" name="urutan" id="urutan" min="1" value="1" required class="form-control">
                <small style="color: #666; display: block; margin-top: 5px;">Semakin kecil angka, semakin awal urutan tampil</small>
            </div>

            <div class="form-group">
                <label>Gambar Banner <span style="color: red;">*</span></label>
                <div style="position: relative;">
                    <input type="file" name="banner_image" id="banner_image" accept="image/*" onchange="previewImage(this)" class="form-control" style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer;">
                    <button type="button" onclick="document.getElementById('banner_image').click()" style="padding: 8px 16px; border: 1px solid #ddd; background: white; border-radius: 6px; cursor: pointer; width: 100%; text-align: left; color: #666;">
                        <span id="fileLabel">Pilih file...</span>
                    </button>
                </div>
                <small style="color: #666; display: block; margin-top: 5px;">Format: JPG, PNG, GIF, WEBP (Recommended: 1200x400px)</small>
                <div id="imagePreview" style="margin-top: 15px;"></div>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="aktif" id="aktif" checked>
                    <span>Aktifkan Banner</span>
                </label>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 30px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Banner</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('bannerModal').style.display = 'block';
    document.getElementById('modalTitle').textContent = 'Tambah Banner Baru';
    document.getElementById('formAction').value = 'add';
    document.getElementById('bannerForm').reset();
    document.getElementById('imagePreview').innerHTML = '';
    document.getElementById('bannerId').value = '';
    document.getElementById('fileLabel').textContent = 'Pilih file...';
}

function closeModal() {
    document.getElementById('bannerModal').style.display = 'none';
}

function editBanner(banner) {
    document.getElementById('bannerModal').style.display = 'block';
    document.getElementById('modalTitle').textContent = 'Edit Banner';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('bannerId').value = banner.id;
    document.getElementById('urutan').value = banner.urutan;
    document.getElementById('aktif').checked = banner.aktif == 1;
    document.getElementById('existingImage').value = banner.image_url;
    
    if (banner.image_url) {
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const fileLabel = document.getElementById('fileLabel');
    
    if (input.files && input.files[0]) {
        const fileName = input.files[0].name;
        fileLabel.textContent = fileName;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        fileLabel.textContent = 'Pilih file...';
    }
}       reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function toggleStatus(id, status) {
    const formData = new FormData();
    formData.append('action', 'toggle_status');
    formData.append('id', id);
    formData.append('status', status);
    
    fetch('banners.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && window.showToast) {
            const statusText = status == 1 ? 'diaktifkan' : 'dinonaktifkan';
            showToast('Banner berhasil ' + statusText, 'success');
            setTimeout(() => window.location.reload(), 500);
        }
    })
    .catch(error => {
        if (window.showToast) {
            showToast('Gagal mengubah status banner', 'error');
        }
    });
}

function confirmDeleteBanner(id, bannerId) {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        document.getElementById('deleteMessage').innerHTML = 
            'Yakin ingin menghapus <strong>Banner #' + bannerId + '</strong>?<br><br>' +
            '<span style="color: #dc3545;">Banner akan dihapus permanen!</span>';
        
        modal.classList.add('show');
        
        // Override confirm button action
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        newConfirmBtn.onclick = function() {
            deleteBanner(id);
            modal.classList.remove('show');
        };
    } else if (confirm('Yakin ingin menghapus banner #' + bannerId + '?')) {
        deleteBanner(id);
    }
}

function deleteBanner(id) {
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);
    
    fetch('banners.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            if (window.showToast) {
                showToast('Banner berhasil dihapus', 'success');
            }
            setTimeout(() => window.location.reload(), 500);
        } else {
            if (window.showToast) {
                showToast('Gagal menghapus banner', 'error');
            }
        }
    })
    .catch(error => {
        if (window.showToast) {
            showToast('Terjadi kesalahan saat menghapus', 'error');
        }
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('bannerModal');
    if (event.target == modal) {
        closeModal();
    }
}

// Set active menu
document.querySelectorAll('.menu-item').forEach(item => {
    if (item.href && item.href.includes('banners.php')) {
        item.classList.add('active');
    }
});
</script>

</body>
</html>