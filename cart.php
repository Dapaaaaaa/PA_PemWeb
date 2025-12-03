<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/settings_helper.php';
include 'includes/header.php'; 

// --- Logika Penanganan Aksi ---
// Update catatan pesanan
if (isset($_POST['update_notes'])) {
    $index = intval($_POST['item_index']);
    $notes = trim($_POST['notes'] ?? '');
    $sauce_selections = isset($_POST['sauce_options']) ? $_POST['sauce_options'] : [];
    
    if (isset($_SESSION['cart'][$index])) {
        $_SESSION['cart'][$index]['notes'] = $notes;
        $_SESSION['cart'][$index]['sauce_options'] = $sauce_selections;
    }
    header('Location: cart.php?updated=success');
    exit;
}

if (isset($_POST['batal_beli'])) {
    unset($_SESSION['cart']);
    $_SESSION['cart'] = [];
    header('Location: cart.php?status=cancelled'); 
    exit;
}
if (isset($_POST['lanjut_beli'])) {
    if (!empty($_SESSION['cart'])) {
        header('Location: checkout.php');
        exit;
    }
}

// Hitung Total Pembelian dan Detail
$total_price = 0;
$total_items = 0;
$total_qty = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'] * $item['qty'];
        $total_qty += $item['qty'];
        $total_items++;
    }
}
?>

<div class="cart-container">
    <h2>Keranjang Pesanan Anda</h2>

    <?php if (!empty($_SESSION['cart'])): ?>
        <div class="order-summary-box">
            <h3>Ringkasan Pesanan</h3>
            <div class="summary-details">
                <p><strong>Jumlah Jenis Item:</strong> <?php echo $total_items; ?> item</p>
                <p><strong>Total Kuantitas:</strong> <?php echo $total_qty; ?> pcs</p>
                <p><strong>Total Harga:</strong> Rp <?php echo number_format($total_price, 0, ',', '.'); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'cancelled'): ?>
        <p class="alert-message alert-error">Semua pesanan telah dibatalkan!</p>
    <?php endif; ?>

    <?php if (isset($_GET['added']) && $_GET['added'] == 'success'): ?>
        <p class="alert-message alert-success">Item berhasil ditambahkan ke keranjang!</p>
    <?php endif; ?>

    <?php if (isset($_GET['updated']) && $_GET['updated'] == 'success'): ?>
        <p class="alert-message alert-success">Catatan pesanan berhasil diperbarui!</p>
    <?php endif; ?>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="empty-cart">
            <p>Keranjang Anda masih kosong. Mari mulai berbelanja!</p>
            <a href="menu.php" class="btn-action btn-back">← Kembali ke Menu</a>
        </div>
    <?php else: ?>
        <div class="cart-items-container">
            <?php foreach ($_SESSION['cart'] as $index => $item): ?>
            <div class="cart-item-card">
                <div class="cart-item-main">
                    <div class="cart-item-image">
                        <img src="<?php echo htmlspecialchars($item['img']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    </div>
                    <div class="cart-item-info">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="price">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?> x <?php echo $item['qty']; ?></p>
                        <p class="subtotal">Subtotal: Rp <?php echo number_format($item['price'] * $item['qty'], 0, ',', '.'); ?></p>
                    </div>
                </div>
                
                <div class="cart-item-customization">
                    <form method="POST" action="cart.php" class="customization-form">
                        <input type="hidden" name="item_index" value="<?php echo $index; ?>">
                        
                        <div class="form-group">
                            <label><?php echo getSetting('sauce_label', 'Pilih Saus'); ?>:</label>
                            <div class="sauce-selector">
                                <?php 
                                // Ambil pilihan saus dari settings admin
                                $available_sauces_str = getSetting('sauce_options', 'tidak-bersaus,pedas,manis');
                                $available_sauces = explode(',', $available_sauces_str);
                                
                                $current_sauces = $item['sauce_options'] ?? [];
                                
                                $sauce_labels = [
                                    'tidak-bersaus' => 'Tidak Bersaus',
                                    'pedas' => 'Pedas',
                                    'manis' => 'Manis'
                                ];
                                
                                foreach ($available_sauces as $sauce_value):
                                    $sauce_value = trim($sauce_value);
                                    if (isset($sauce_labels[$sauce_value])):
                                        $is_checked = in_array($sauce_value, $current_sauces);
                                ?>
                                <label class="sauce-option <?php echo $is_checked ? 'active' : ''; ?>">
                                    <input type="checkbox" name="sauce_options[]" value="<?php echo $sauce_value; ?>" <?php echo $is_checked ? 'checked' : ''; ?>>
                                    <span><?php echo $sauce_labels[$sauce_value]; ?></span>
                                </label>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                            <small style="color: #666; font-size: 12px; margin-top: 5px; display: block;">Anda bisa memilih lebih dari satu</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes_<?php echo $index; ?>">Catatan Pesanan:</label>
                            <textarea 
                                id="notes_<?php echo $index; ?>" 
                                name="notes" 
                                rows="2" 
                                placeholder="Contoh: Tanpa sayur, ekstra sambal, minta dikemas terpisah, dll."
                            ><?php echo htmlspecialchars($item['notes'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" name="update_notes" class="btn-update-notes">Simpan Catatan</button>
                    </form>
                    
                    <?php if (!empty($item['notes']) || !empty($item['sauce_options'])): ?>
                    <div class="current-customization">
                        <strong>Customisasi Saat Ini:</strong>
                        <?php if (!empty($item['sauce_options'])): ?>
                            <?php 
                            $sauce_labels = [
                                'tidak-bersaus' => 'Tidak Bersaus',
                                'pedas' => 'Pedas',
                                'manis' => 'Manis'
                            ];
                            foreach ($item['sauce_options'] as $sauce): 
                            ?>
                            <span class="badge badge-spice">🍽️ <?php echo $sauce_labels[$sauce] ?? $sauce; ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if (!empty($item['notes'])): ?>
                        <p class="note-preview"><?php echo htmlspecialchars($item['notes']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <form method="POST" action="cart.php">
            <div class="cart-total-box">
                <div class="total-row">
                    <span>Total Pembelian:</span>
                    <span class="total-amount">Rp <?php echo number_format($total_price, 0, ',', '.'); ?></span>
                </div>
            </div>
            <div class="cart-actions">
                <button type="submit" name="batal_beli" class="btn-action btn-cancel">
                    Batalkan Semua
                </button>
                <button type="submit" name="lanjut_beli" class="btn-action btn-continue">
                    Lanjut ke Pembayaran
                </button>
            </div>
        </form>

        <p class="back-link" style="text-align: center;">
            <a href="menu.php" class="back-link">← Tambah Item Lagi</a>
        </p>
    <?php endif; ?>
</div>

<script>
// Toggle active class untuk checkbox saus
document.querySelectorAll('.sauce-option input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            this.parentElement.classList.add('active');
        } else {
            this.parentElement.classList.remove('active');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>