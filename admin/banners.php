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

<div class="dashboard-content">
    <div class="content-header">
        <h2>Kelola Banner Slider</h2>
        <button class="btn-add" onclick="openModal()">
             Tambah Banner Baru
        </button>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof showToast === 'function') {
                    showToast('<?php echo addslashes($_SESSION['success_message']); ?>', 'success', 2000);
                }
            });
        </script>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof showToast === 'function') {
                    showToast('<?php echo addslashes($_SESSION['error_message']); ?>', 'error', 3000);
                }
            });
        </script>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="info-box" style="margin-bottom: 20px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 8px;">
        <p style="margin: 0; color: #856404;">
            <strong>Catatan:</strong> Maksimal 5 banner aktif. Banner akan otomatis berganti setiap 5 detik. Gunakan urutan untuk mengatur posisi banner.
        </p>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Urutan</th>
                    <th>Preview</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($banners) > 0): ?>
                    <?php while($banner = mysqli_fetch_assoc($banners)): ?>
                    <tr>
                        <td><?php echo $banner['urutan']; ?></td>
                        <td>
                            <img src="../<?php echo htmlspecialchars($banner['image_url']); ?>" 
                                 alt="Banner" 
                                 style="width: 100px; height: 60px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td>
                            <label class="switch">
                                <input type="checkbox" 
                                       <?php echo $banner['aktif'] ? 'checked' : ''; ?>
                                       onchange="toggleStatus(<?php echo $banner['id']; ?>, this.checked ? 1 : 0)">
                                <span class="slider-switch"></span>
                            </label>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-edit" onclick='editBanner(<?php echo json_encode($banner); ?>)'>
                                    Edit
                                </button>
                                <button class="btn-delete" onclick="confirmDelete(<?php echo $banner['id']; ?>, <?php echo $banner['id']; ?>)">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px;">
                            Belum ada banner. Klik "Tambah Banner Baru" untuk memulai.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form -->
<div id="bannerModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3 id="modalTitle">Tambah Banner Baru</h3>
        
        <form method="POST" enctype="multipart/form-data" id="bannerForm">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="bannerId">
            <input type="hidden" name="existing_image" id="existingImage">
            
            <div class="form-group">
                <label>Urutan:</label>
                <input type="number" name="urutan" id="urutan" min="1" value="1" required>
                <small>Semakin kecil angka, semakin awal urutan tampil</small>
            </div>

            <div class="form-group">
                <label>Gambar Banner: <span style="color: red;">*</span></label>
                <input type="file" name="banner_image" id="banner_image" accept="image/*" onchange="previewImage(this)">
                <small>Format: JPG, PNG, GIF, WEBP (Recommended: 1200x400px)</small>
                <div id="imagePreview" style="margin-top: 10px;"></div>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="aktif" id="aktif" checked>
                    <span>Aktifkan Banner</span>
                </label>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-submit">Simpan Banner</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Delete -->
<div id="deleteModal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <h3 style="margin-top: 0; color: #dc3545;">⚠️ Konfirmasi Hapus</h3>
        <p id="deleteMessage" style="margin: 20px 0; color: #666;">Yakin ingin menghapus banner ini?</p>
        <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Batal</button>
            <button type="button" class="btn-delete" onclick="confirmDeleteAction()" style="background: #dc3545; color: white;">Hapus</button>
        </div>
    </div>
</div>

<style>
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.btn-add {
    background: linear-gradient(135deg, #537b2f 0%, #6a9d3a 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(83, 123, 47, 0.3);
}

.table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background: #537b2f;
    color: white;
    padding: 15px;
    text-align: left;
    font-weight: 600;
}

.data-table td {
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.data-table tr:hover {
    background: #f8f9fa;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-edit, .btn-delete {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-edit {
    background: #ffc107;
    color: #333;
}

.btn-edit:hover {
    background: #ffb300;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #c82333;
}

/* Toggle Switch */
.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider-switch {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
}

.slider-switch:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider-switch {
    background-color: #537b2f;
}

input:checked + .slider-switch:before {
    transform: translateX(26px);
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    overflow: auto;
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 30px;
    border-radius: 12px;
    max-width: 600px;
    position: relative;
}

.close {
    position: absolute;
    right: 20px;
    top: 20px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    color: #999;
}

.close:hover {
    color: #333;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.form-group input[type="text"],
.form-group input[type="number"],
.form-group input[type="file"],
.form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #537b2f;
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 12px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 30px;
}

.btn-cancel, .btn-submit {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-cancel {
    background: #6c757d;
    color: white;
}

.btn-cancel:hover {
    background: #5a6268;
}

.btn-submit {
    background: #537b2f;
    color: white;
}

.btn-submit:hover {
    background: #3d5b22;
}
</style>

<script>
function openModal() {
    document.getElementById('bannerModal').style.display = 'block';
    document.getElementById('modalTitle').textContent = 'Tambah Banner Baru';
    document.getElementById('formAction').value = 'add';
    document.getElementById('bannerForm').reset();
    document.getElementById('imagePreview').innerHTML = '';
    document.getElementById('bannerId').value = '';
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
    
    // Show existing image
    if (banner.image_url) {
        document.getElementById('imagePreview').innerHTML = 
            '<img src="../' + banner.image_url + '" style="max-width: 200px; border-radius: 8px;">';
    }
}

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" style="max-width: 200px; border-radius: 8px;">';
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
            showToast('Banner berhasil ' + statusText, 'success', 2000);
        }
    })
    .catch(error => {
        if (window.showToast) {
            showToast('Gagal mengubah status banner', 'error', 2000);
        }
    });
}

let deleteTargetId = null;

function confirmDelete(id, bannerId) {
    deleteTargetId = id;
    document.getElementById('deleteMessage').textContent = 'Yakin ingin menghapus banner #' + bannerId + '?';
    document.getElementById('deleteModal').style.display = 'block';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    deleteTargetId = null;
}

function confirmDeleteAction() {
    if (deleteTargetId) {
        deleteBanner(deleteTargetId);
        closeDeleteModal();
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
                showToast('Banner berhasil dihapus', 'success', 2000);
            }
            setTimeout(() => window.location.reload(), 500);
        } else {
            if (window.showToast) {
                showToast('Gagal menghapus banner', 'error', 2000);
            }
        }
    })
    .catch(error => {
        if (window.showToast) {
            showToast('Terjadi kesalahan saat menghapus', 'error', 2000);
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
</script>

</div>

<script>
// Set active menu
document.querySelectorAll('.menu-item').forEach(item => {
    if (item.href === window.location.href) {
        item.classList.add('active');
    }
});
</script>

</body>
</html>
