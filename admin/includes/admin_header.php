<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - OurStuffies</title>

    <!-- Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Admin CSS -->
    <link rel="stylesheet" href="css/admin.css">
    
    <!-- Toast Notification Script -->
    <script src="../assets/js/toast.js"></script>
    
    <!-- Modal Script -->
    <script src="js/modal.js"></script>
</head>
<body>

<!-- Custom Delete Modal -->
<div id="deleteModal" class="modal-overlay">
    <div class="modal-content">
        <h3 class="modal-title">⚠️ Konfirmasi Hapus</h3>
        <p id="deleteMessage" class="modal-message"></p>
        <div class="modal-actions">
            <button onclick="closeDeleteModal()" class="btn-modal-cancel">Batal</button>
            <button id="confirmDeleteBtn" class="btn-modal-delete">Hapus</button>
        </div>
    </div>
</div>

<!-- Custom Update Status Modal -->
<div id="statusModal" class="modal-overlay">
    <div class="modal-content">
        <h3 class="modal-title" style="color: #537b2f;">🔄 Konfirmasi Update Status</h3>
        <p id="statusMessage" class="modal-message"></p>
        <div class="modal-actions">
            <button onclick="closeStatusModal()" class="btn-modal-cancel">Batal</button>
            <button id="confirmStatusBtn" class="btn-modal-confirm">Update</button>
        </div>
    </div>
</div>

<div class="admin-wrapper">