<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/header.php';
include 'koneksi.php';

// Redirect jika keranjang kosong
if (empty($_SESSION['cart'])) {
    header('Location: menu.php');
    exit;
}

// Hitung Total Pembelian
$total_price = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_price += $item['price'] * $item['qty'];
}

// Logika untuk menyelesaikan pesanan
if (isset($_POST['complete_order'])) {
    // Ambil data customer
    $customer_name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $customer_address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
    $customer_phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    
    // Validasi input
    if (empty($customer_name) || empty($customer_address) || empty($customer_phone)) {
        $_SESSION['error'] = "Semua field harus diisi!";
    } else {
        // START TRANSACTION
        mysqli_begin_transaction($conn);
        
        try {
            // 1. Cek atau buat pelanggan baru
            $query_check_customer = "SELECT id FROM pelanggan WHERE telepon = '$customer_phone' LIMIT 1";
            $result_customer = mysqli_query($conn, $query_check_customer);
            
            if (mysqli_num_rows($result_customer) > 0) {
                $customer = mysqli_fetch_assoc($result_customer);
                $customer_id = $customer['id'];
            } else {
                // Buat pelanggan baru
                $query_insert_customer = "INSERT INTO pelanggan (nama, telepon) VALUES ('$customer_name', '$customer_phone')";
                if (!mysqli_query($conn, $query_insert_customer)) {
                    throw new Exception("Gagal membuat data pelanggan");
                }
                $customer_id = mysqli_insert_id($conn);
            }
            
            // 2. Buat alamat pengiriman
            $query_insert_address = "INSERT INTO alamat (pelanggan_id, label, jalan, kota) 
                                     VALUES ($customer_id, 'Default', '$customer_address', 'Jakarta')";
            if (!mysqli_query($conn, $query_insert_address)) {
                throw new Exception("Gagal menyimpan alamat");
            }
            $address_id = mysqli_insert_id($conn);
            
            // 3. Generate nomor pesanan
            $date = date('Ymd');
            $query_last_order = "SELECT nomor_pesanan FROM pesanan WHERE nomor_pesanan LIKE 'ORD$date%' ORDER BY id DESC LIMIT 1";
            $result_last = mysqli_query($conn, $query_last_order);
            
            if (mysqli_num_rows($result_last) > 0) {
                $last_order = mysqli_fetch_assoc($result_last);
                $last_num = intval(substr($last_order['nomor_pesanan'], -4));
                $new_num = str_pad($last_num + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $new_num = '0001';
            }
            $order_number = 'ORD' . $date . $new_num;
            
            // 4. Buat pesanan
            $query_insert_order = "INSERT INTO pesanan (nomor_pesanan, pelanggan_id, alamat_id, status, total, catatan) 
                                   VALUES ('$order_number', $customer_id, $address_id, 'pending', $total_price, 'Order dari website')";
            if (!mysqli_query($conn, $query_insert_order)) {
                throw new Exception("Gagal membuat pesanan");
            }
            $order_id = mysqli_insert_id($conn);
            
            // 5. Simpan item pesanan
            foreach ($_SESSION['cart'] as $item) {
                $item_name = mysqli_real_escape_string($conn, $item['name']);
                $item_price = floatval($item['price']);
                $item_qty = intval($item['qty']);
                $subtotal = $item_price * $item_qty;
                
                // Cari produk_id berdasarkan nama
                $query_find_product = "SELECT id FROM produk WHERE nama = '$item_name' LIMIT 1";
                $result_product = mysqli_query($conn, $query_find_product);
                
                if (mysqli_num_rows($result_product) > 0) {
                    $product = mysqli_fetch_assoc($result_product);
                    $product_id = $product['id'];
                    
                    $query_insert_item = "INSERT INTO item_pesanan (pesanan_id, produk_id, qty, harga_satuan, subtotal) 
                                          VALUES ($order_id, $product_id, $item_qty, $item_price, $subtotal)";
                    if (!mysqli_query($conn, $query_insert_item)) {
                        throw new Exception("Gagal menyimpan item pesanan");
                    }
                }
            }
            
            // COMMIT TRANSACTION
            mysqli_commit($conn);
            
            // HAPUS keranjang sesi setelah pesanan sukses
            unset($_SESSION['cart']);
            
            // Redirect dengan nomor pesanan
            header('Location: index.php?status=order_placed&order=' . $order_number);
            exit;
            
        } catch (Exception $e) {
            // ROLLBACK jika ada error
            mysqli_rollback($conn);
            $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>

<div class="cart-container">
    <h2>📦 Konfirmasi Pesanan</h2>
    <p>Silakan lengkapi detail Anda dan konfirmasi pesanan.</p>

    <?php if (isset($_SESSION['error'])): ?>
        <p class="alert-message alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <div class="checkout-summary">
        <h3>Ringkasan Pesanan</h3>
        <?php foreach ($_SESSION['cart'] as $item): ?>
            <div class="summary-item">
                <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['qty']; ?>)</span>
                <span>Rp <?php echo number_format($item['price'] * $item['qty'], 0, ',', '.'); ?></span>
            </div>
        <?php endforeach; ?>
        <div class="summary-item">
            <span class="summary-total">Total Akhir:</span>
            <span class="summary-total">Rp <?php echo number_format($total_price, 0, ',', '.'); ?></span>
        </div>
    </div>

    <form method="POST" action="checkout.php" class="checkout-form">
        <h3>Detail Pelanggan</h3>
        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" placeholder="Nama Anda" required>
        </div>
        <div class="form-group">
            <label for="phone">Nomor Telepon / WhatsApp</label>
            <input type="tel" id="phone" name="phone" placeholder="08xxxxxxxxxx" required>
        </div>
        <div class="form-group">
            <label for="address">Alamat Pengiriman</label>
            <textarea id="address" name="address" placeholder="Alamat lengkap dan detail" required></textarea>
        </div>

        <button type="submit" name="complete_order" class="btn-action btn-continue">
            💳 Bayar & Selesaikan Pesanan
        </button>
    </form>
    
    <p class="back-link" style="text-align: center;">
        <a href="cart.php" class="back-link">← Kembali ke Keranjang</a>
    </p>
</div>

<?php include 'includes/footer.php'; ?>