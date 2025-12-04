<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/settings_helper.php';
include 'includes/header.php'; 

// --- Logika Penanganan Aksi ---
if (isset($_POST['batal_beli'])) {
    unset($_SESSION['cart']);
    $_SESSION['cart'] = [];
    header('Location: cart.php?status=cancelled'); 
    exit;
}
if (isset($_POST['lanjut_beli'])) {
    if (!empty($_SESSION['cart'])) {
        // Validasi: Setiap item harus punya pilihan saus
        $all_items_have_sauce = true;
        $missing_sauce_items = [];
        
        foreach ($_SESSION['cart'] as $index => $item) {
            if (empty($item['sauce_options']) || count($item['sauce_options']) == 0) {
                $all_items_have_sauce = false;
                $missing_sauce_items[] = $item['name'];
            }
        }
        
        if (!$all_items_have_sauce) {
            $_SESSION['checkout_error'] = 'Silakan pilih saus untuk semua item sebelum melanjutkan ke checkout!';
            header('Location: cart.php?error=sauce_required');
            exit;
        }
        
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

    <?php if (isset($_GET['error']) && $_GET['error'] == 'sauce_required'): ?>
        <p class="alert-message alert-error">
            <?php 
            echo isset($_SESSION['checkout_error']) ? $_SESSION['checkout_error'] : 'Silakan pilih saus untuk semua item!';
            unset($_SESSION['checkout_error']);
            ?>
        </p>
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
                    <div class="customization-form" data-item-index="<?php echo $index; ?>">
                        
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
                            <small style="color: #666; font-size: 12px; margin-top: 5px; display: block;">
                                <strong>Aturan:</strong> "Tidak Bersaus" hanya bisa dipilih sendiri. "Pedas" dan "Manis" bisa dipilih bersamaan.
                            </small>
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
                    </div>
                    
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
                            <span class="badge badge-spice"> <?php echo $sauce_labels[$sauce] ?? $sauce; ?></span>
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
// Auto-save sauce selection
document.querySelectorAll('.sauce-option input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const form = this.closest('.customization-form');
        const itemIndex = form.getAttribute('data-item-index');
        const sauceSelector = this.closest('.sauce-selector');
        const tidakBersausCheckbox = sauceSelector.querySelector('input[value="tidak-bersaus"]');
        const pedasCheckbox = sauceSelector.querySelector('input[value="pedas"]');
        const manisCheckbox = sauceSelector.querySelector('input[value="manis"]');
        
        // Jika "Tidak Bersaus" dicentang, uncheck yang lain
        if (this.value === 'tidak-bersaus' && this.checked) {
            if (pedasCheckbox) pedasCheckbox.checked = false;
            if (manisCheckbox) manisCheckbox.checked = false;
        }
        
        // Jika "Pedas" atau "Manis" dicentang, uncheck "Tidak Bersaus"
        if ((this.value === 'pedas' || this.value === 'manis') && this.checked) {
            if (tidakBersausCheckbox) tidakBersausCheckbox.checked = false;
        }
        
        // Update visual active class untuk semua checkbox
        sauceSelector.querySelectorAll('.sauce-option').forEach(option => {
            const input = option.querySelector('input[type="checkbox"]');
            if (input.checked) {
                option.classList.add('active');
            } else {
                option.classList.remove('active');
            }
        });
        
        // Auto-save via AJAX
        const formData = new FormData();
        formData.append('item_index', itemIndex);
        formData.append('field', 'sauce');
        
        // Get all checked sauces
        const checkedSauces = sauceSelector.querySelectorAll('input[type="checkbox"]:checked');
        checkedSauces.forEach(sauce => {
            formData.append('sauce_options[]', sauce.value);
        });
        
        fetch('update_cart_item.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload page to update display
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    });
});

// Auto-save notes on blur (when user leaves textarea)
document.querySelectorAll('.customization-form textarea').forEach(textarea => {
    let saveTimeout;
    
    textarea.addEventListener('input', function() {
        // Clear previous timeout
        clearTimeout(saveTimeout);
        
        // Set new timeout to save after 1 second of no typing
        saveTimeout = setTimeout(() => {
            const form = this.closest('.customization-form');
            const itemIndex = form.getAttribute('data-item-index');
            const notes = this.value;
            
            const formData = new FormData();
            formData.append('item_index', itemIndex);
            formData.append('field', 'notes');
            formData.append('notes', notes);
            
            fetch('update_cart_item.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show subtle indicator that notes were saved
                    if (window.showToast) {
                        showToast('Catatan tersimpan', 'success', 1000);
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }, 1000);
    });
});
</script>

<?php include 'includes/footer.php'; ?>