<?php
session_start();
require_once 'koneksi.php';
require_once 'includes/settings_helper.php';

// Cek apakah ada parameter order number
if (!isset($_GET['order']) || empty($_GET['order'])) {
    header('Location: index.php');
    exit;
}

$order_number = mysqli_real_escape_string($conn, $_GET['order']);

// Ambil data pesanan dari database
$query = "SELECT p.*, pel.nama, pel.email, pel.telepon, a.jalan, a.kota, a.kode_pos,
          (p.total + p.ongkir) as total_bayar
          FROM pesanan p
          JOIN pelanggan pel ON p.pelanggan_id = pel.id
          LEFT JOIN alamat a ON p.alamat_id = a.id
          WHERE p.nomor_pesanan = '$order_number'
          LIMIT 1";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = "Pesanan tidak ditemukan!";
    header('Location: index.php');
    exit;
}

$order = mysqli_fetch_assoc($result);

// Ambil item pesanan
$query_items = "SELECT ip.*, pr.nama as product_name
                FROM item_pesanan ip
                JOIN produk pr ON ip.produk_id = pr.id
                WHERE ip.pesanan_id = " . $order['id'];
$result_items = mysqli_query($conn, $query_items);
$items = [];
while ($row = mysqli_fetch_assoc($result_items)) {
    $items[] = $row;
}

// Format nomor WhatsApp
$wa_number = getSetting('store_whatsapp', '6281234567890');
// Hapus karakter selain angka dan +
$wa_number = preg_replace('/[^0-9+]/', '', $wa_number);
// Jika diawali 0, ganti dengan 62
if (substr($wa_number, 0, 1) == '0') {
    $wa_number = '62' . substr($wa_number, 1);
}

// Buat pesan WhatsApp otomatis
$items_text = "";
$no = 1;
foreach ($items as $item) {
    $items_text .= $no . ". " . $item['product_name'] . " (x" . $item['qty'] . ") - Rp " . number_format($item['subtotal'], 0, ',', '.') . "%0A";
    
    // Tambahkan info pilihan saus
    if (!empty($item['level_pedas'])) {
        $sauce_labels = [
            'tidak-bersaus' => 'Tidak Bersaus',
            'pedas' => 'Pedas',
            'manis' => 'Manis'
        ];
        
        $sauces = explode(',', $item['level_pedas']);
        $sauce_names = [];
        foreach ($sauces as $sauce) {
            $sauce = trim($sauce);
            if (isset($sauce_labels[$sauce])) {
                $sauce_names[] = $sauce_labels[$sauce];
            }
        }
        
        if (!empty($sauce_names)) {
            $items_text .= implode(', ', $sauce_names) . "%0A";
        }
    }
    
    // Tambahkan catatan jika ada
    if (!empty($item['catatan'])) {
        $items_text .= $item['catatan'] . "%0A";
    }
    
    $no++;
}

$wa_message = "*KONFIRMASI PEMBAYARAN*%0A%0A";
$wa_message .= "Nomor Pesanan: *" . $order_number . "*%0A";
$wa_message .= "Nama: " . $order['nama'] . "%0A";
$wa_message .= "Telepon: " . $order['telepon'] . "%0A";
$wa_message .= "Email: " . ($order['email'] ?? '-') . "%0A%0A";
$wa_message .= "*Alamat Pengiriman:*%0A";
$wa_message .= ($order['jalan'] ?? '-') . "%0A";
$wa_message .= ($order['kota'] ?? '-') . " " . ($order['kode_pos'] ?? '') . "%0A%0A";
$wa_message .= "*Detail Pesanan:*%0A" . $items_text . "%0A";
$wa_message .= "Subtotal: Rp " . number_format($order['total'], 0, ',', '.') . "%0A";
$wa_message .= "Ongkir: Rp " . number_format($order['ongkir'], 0, ',', '.') . "%0A";
$wa_message .= "*Total Bayar: Rp " . number_format($order['total_bayar'], 0, ',', '.') . "*%0A%0A";
$wa_message .= "Saya telah melakukan pembayaran via QRIS.%0A";
$wa_message .= "Mohon konfirmasi pesanan saya.%0A%0A";
$wa_message .= "Terima kasih!";

$wa_link = "https://wa.me/" . $wa_number . "?text=" . $wa_message;

include 'includes/header.php';
?>

<section class="payment-page">
    <div class="container">
        <div class="payment-header">
            <h1>Pembayaran Pesanan</h1>
            <p class="order-number">Nomor Pesanan: <strong><?php echo $order_number; ?></strong></p>
        </div>

        <div class="payment-content">
            <!-- Detail Pesanan -->
            <div class="order-details-box">
                <h2>Detail Pesanan</h2>
                <div class="customer-info">
                    <p><strong>Nama:</strong> <?php echo htmlspecialchars($order['nama']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ?? '-'); ?></p>
                    <p><strong>Telepon:</strong> <?php echo htmlspecialchars($order['telepon']); ?></p>
                    <p><strong>Alamat Pengiriman:</strong> <?php echo htmlspecialchars($order['jalan'] ?? '-'); ?>, <?php echo htmlspecialchars($order['kota'] ?? '-'); ?> <?php echo htmlspecialchars($order['kode_pos'] ?? ''); ?></p>
                </div>

                <h3>Item Pesanan</h3>
                <table class="order-items-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo $item['qty']; ?></td>
                            <td>Rp <?php echo number_format($item['harga_satuan'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="3"><strong>Total Bayar</strong></td>
                            <td><strong>Rp <?php echo number_format($order['total_bayar'], 0, ',', '.'); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- QRIS Payment -->
            <div class="qris-payment-box">
                <h2>Scan QRIS untuk Bayar</h2>
                <div class="qris-container">
                    <img src="assets/img/qris.png" alt="QRIS Payment" class="qris-image">
                </div>

                <div class="payment-instructions">
                    <h3>Cara Pembayaran:</h3>
                    <ol>
                        <li>Buka aplikasi mobile banking atau e-wallet Anda</li>
                        <li>Pilih menu "Scan QRIS" atau "Bayar dengan QRIS"</li>
                        <li>Scan kode QR di atas</li>
                        <li>Pastikan nominal yang dibayar sesuai: <strong>Rp <?php echo number_format($order['total_bayar'], 0, ',', '.'); ?></strong></li>
                        <li>Lakukan pembayaran dan simpan bukti transfer</li>
                        <li>Klik tombol "Konfirmasi Pembayaran via WhatsApp" di bawah</li>
                        <li>Kirim screenshot bukti pembayaran melalui WhatsApp</li>
                    </ol>
                </div>

                <div class="payment-actions">
                    <a href="<?php echo $wa_link; ?>" target="_blank" class="btn-confirm-wa">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        Konfirmasi Pembayaran via WhatsApp
                    </a>
                    <a href="index.php" class="btn-back-home" onclick="return confirm('Apakah Anda yakin ingin kembali ke beranda? Pastikan Anda sudah mengkonfirmasi pembayaran melalui WhatsApp.');">Kembali ke Beranda</a>
                </div>

                <div class="payment-note">
                    <p><strong>Catatan:</strong> Admin akan mengkonfirmasi pesanan Anda setelah menerima bukti pembayaran melalui WhatsApp. Proses verifikasi biasanya memakan waktu 5-15 menit.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
