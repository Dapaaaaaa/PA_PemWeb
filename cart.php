<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
        header('Location: checkout.php');
        exit;
    }
}

// Hitung Total Pembelian
$total_price = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'] * $item['qty'];
    }
}
?>

<div class="cart-container">
    <h2>Keranjang Pesanan Anda</h2>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'cancelled'): ?>
        <p class="alert-message alert-error">Semua pesanan telah dibatalkan!</p>
    <?php endif; ?>

    <?php if (isset($_GET['added']) && $_GET['added'] == 'success'): ?>
        <p class="alert-message alert-success">Item berhasil ditambahkan ke keranjang!</p>
    <?php endif; ?>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="empty-cart">
            <p>Keranjang Anda masih kosong. Mari mulai berbelanja!</p>
            <a href="menu.php" class="btn-action btn-back">← Kembali ke Menu</a>
        </div>
    <?php else: ?>
        <form method="POST" action="cart.php">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                    <tr>
                        <td>
                            <div class="cart-item-image">
                                <img src="<?php echo htmlspecialchars($item['img']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <span><?php echo htmlspecialchars($item['name']); ?></span>
                            </div>
                        </td>
                        <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                        <td><?php echo $item['qty']; ?></td>
                        <td style="text-align: right;">Rp <?php echo number_format($item['price'] * $item['qty'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="cart-total-row">
                        <td colspan="3" style="text-align: right; font-weight: 700;">Total Pembelian:</td>
                        <td style="text-align: right; font-weight: 700; font-size: 22px; color: #537b2f;">
                            Rp <?php echo number_format($total_price, 0, ',', '.'); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>

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

<?php include 'includes/footer.php'; ?>