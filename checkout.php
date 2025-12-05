<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/header.php';
include 'koneksi.php';
include 'includes/settings_helper.php';

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

// Default delivery fee
$delivery_fee = 0;
$distance_km = 0;

// Logika untuk menyelesaikan pesanan
if (isset($_POST['complete_order'])) {
    // Ambil data customer
    $customer_name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $customer_address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
    $customer_phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $customer_lat = floatval($_POST['latitude'] ?? 0);
    $customer_lng = floatval($_POST['longitude'] ?? 0);
    
    // Hitung ongkir berdasarkan jarak
    $delivery_fee = 0;
    $distance_km = 0;
    
    if ($customer_lat != 0 && $customer_lng != 0) {
        $store_lat = floatval(getSetting('store_latitude', '-0.464618'));
        $store_lng = floatval(getSetting('store_longitude', '117.147607'));
        
        // Hitung jarak menggunakan Haversine formula
        $earth_radius = 6371; // km
        $lat_diff = deg2rad($customer_lat - $store_lat);
        $lng_diff = deg2rad($customer_lng - $store_lng);
        
        $a = sin($lat_diff/2) * sin($lat_diff/2) +
             cos(deg2rad($store_lat)) * cos(deg2rad($customer_lat)) *
             sin($lng_diff/2) * sin($lng_diff/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance_km = $earth_radius * $c;
        
        // Cek maksimal jarak
        $max_distance = floatval(getSetting('max_delivery_distance', '15'));
        if ($distance_km > $max_distance) {
            $_SESSION['error'] = "Maaf, alamat Anda terlalu jauh dari toko kami (" . number_format($distance_km, 1) . " km). Maksimal jarak pengiriman: $max_distance km.";
            header('Location: checkout.php');
            exit;
        }
        
        // Hitung ongkir
        $fee_per_km = floatval(getSetting('delivery_fee_per_km', '3000'));
        $base_fee = floatval(getSetting('delivery_base_fee', '5000'));
        $delivery_fee = $base_fee + ($distance_km * $fee_per_km);
        
        // Bulatkan ke atas per 1000
        $delivery_fee = ceil($delivery_fee / 1000) * 1000;
        
        // Cek gratis ongkir
        $free_delivery_min = floatval(getSetting('free_delivery_min', '100000'));
        if ($total_price >= $free_delivery_min) {
            $delivery_fee = 0;
        }
    }
    
    // Total akhir
    $final_total = $total_price + $delivery_fee;
    
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
                                     VALUES ($customer_id, 'Default', '$customer_address', 'Samarinda')";
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
            $delivery_notes = "Jarak: " . number_format($distance_km, 2) . " km | Ongkir: Rp " . number_format($delivery_fee, 0, ',', '.');
            $query_insert_order = "INSERT INTO pesanan (nomor_pesanan, pelanggan_id, alamat_id, status, total, catatan) 
                                   VALUES ('$order_number', $customer_id, $address_id, 'pending', $final_total, '$delivery_notes')";
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
                $item_notes = mysqli_real_escape_string($conn, $item['notes'] ?? '');
                
                // Gabungkan pilihan saus menjadi string
                $sauce_options = isset($item['sauce_options']) && is_array($item['sauce_options']) 
                    ? implode(',', $item['sauce_options']) 
                    : '';
                
                // Gabungkan saus + catatan jadi satu
                $combined_notes = '';
                if (!empty($sauce_options)) {
                    $combined_notes = "Saus: $sauce_options";
                }
                if (!empty($item_notes)) {
                    $combined_notes .= ($combined_notes ? ' | ' : '') . "Catatan: $item_notes";
                }
                $final_notes = mysqli_real_escape_string($conn, $combined_notes);
                
                // Cari produk_id berdasarkan nama
                $query_find_product = "SELECT id FROM produk WHERE nama = '$item_name' LIMIT 1";
                $result_product = mysqli_query($conn, $query_find_product);
                
                if (mysqli_num_rows($result_product) > 0) {
                    $product = mysqli_fetch_assoc($result_product);
                    $product_id = $product['id'];
                    
                    $query_insert_item = "INSERT INTO item_pesanan (pesanan_id, produk_id, qty, harga_satuan, subtotal, catatan) 
                                          VALUES ($order_id, $product_id, $item_qty, $item_price, $subtotal, '$final_notes')";
                    if (!mysqli_query($conn, $query_insert_item)) {
                        throw new Exception("Gagal menyimpan item pesanan");
                    }
                }
            }
            
            // COMMIT TRANSACTION
            mysqli_commit($conn);
            
            // HAPUS keranjang sesi setelah pesanan sukses
            unset($_SESSION['cart']);
            
            // Redirect ke halaman pembayaran QRIS
            header('Location: payment_qris.php?order=' . $order_number);
            exit;
            
        } catch (Exception $e) {
            // ROLLBACK jika ada error
            mysqli_rollback($conn);
            $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>

<div class="checkout-container checkout-page-container">
    <h2>Konfirmasi Pesanan</h2>
    <p>Silakan lengkapi detail Anda dan konfirmasi pesanan.</p>

    <?php if (isset($_SESSION['error'])): ?>
        <p class="alert-message alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <div class="checkout-grid">
        <!-- Left Column: Ringkasan Pesanan -->
        <div class="checkout-summary">
            <h3>Ringkasan Pesanan</h3>
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <div class="summary-item">
                    <div class="summary-item-main">
                        <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['qty']; ?>)</span>
                        <span>Rp <?php echo number_format($item['price'] * $item['qty'], 0, ',', '.'); ?></span>
                    </div>
                    <?php if (!empty($item['notes']) || !empty($item['sauce_options'])): ?>
                    <div class="summary-item-details">
                        <?php if (!empty($item['sauce_options'])): ?>
                        <small class="spice-info">
                            <?php 
                            $sauce_labels = [
                                'tidak-bersaus' => 'Tidak Bersaus',
                                'pedas' => 'Pedas',
                                'manis' => 'Manis'
                            ];
                            $sauce_names = [];
                            foreach ($item['sauce_options'] as $sauce) {
                                $sauce_names[] = $sauce_labels[$sauce] ?? $sauce;
                            }
                            echo implode(', ', $sauce_names);
                            ?>
                        </small>
                        <?php endif; ?>
                        <?php if (!empty($item['notes'])): ?>
                        <small class="note-info"> <?php echo htmlspecialchars($item['notes']); ?></small>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <div class="summary-item">
                <span>Subtotal Produk:</span>
                <span>Rp <?php echo number_format($total_price, 0, ',', '.'); ?></span>
            </div>
            <div class="summary-item" id="delivery-summary" style="display: none;">
                <span>Ongkos Kirim:</span>
                <span id="delivery-fee-summary">Rp 0</span>
            </div>
            <div class="summary-item">
                <span class="summary-total">Total Akhir:</span>
                <span class="summary-total" id="final-total">Rp <?php echo number_format($total_price, 0, ',', '.'); ?></span>
            </div>
        </div>

        <!-- Right Column: Form Detail Pelanggan -->
        <div class="checkout-form-wrapper">
            <form method="POST" action="checkout.php" class="checkout-form" id="checkoutForm">
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
                    <small>Klik peta untuk menandai lokasi Anda</small>
                </div>

                <div class="form-group">
                    <label>Tandai Lokasi Pengiriman Anda</label>
                    <div id="map" style="width: 100%; height: 300px; border-radius: 8px; margin-bottom: 10px;"></div>
                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">
                    <div id="distance-info" style="padding: 10px; background: #f0f0f0; border-radius: 4px; display: none;">
                        <p style="margin: 0;"><strong>Jarak dari toko:</strong> <span id="distance-text">-</span> km</p>
                        <p style="margin: 5px 0 0;"><strong>Ongkos kirim:</strong> <span id="delivery-fee-text">Rp 0</span></p>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tombol di bawah -->
    <div class="checkout-actions">
        <a href="cart.php" class="btn-back">← Kembali ke Keranjang</a>
        <button type="submit" form="checkoutForm" name="complete_order" class="btn-action btn-continue">
            Bayar & Selesaikan Pesanan
        </button>
    </div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let map;
let customerMarker;
const storeLocation = [<?php echo getSetting('store_latitude', '-0.464618'); ?>, <?php echo getSetting('store_longitude', '117.147607'); ?>];
const subtotalPrice = <?php echo $total_price; ?>;
const feePerKm = <?php echo getSetting('delivery_fee_per_km', '3000'); ?>;
const baseFee = <?php echo getSetting('delivery_base_fee', '5000'); ?>;
const freeDeliveryMin = <?php echo getSetting('free_delivery_min', '100000'); ?>;
const maxDistance = <?php echo getSetting('max_delivery_distance', '15'); ?>;

// Inisialisasi map saat halaman load
document.addEventListener('DOMContentLoaded', function() {
    // Buat map dengan Leaflet
    map = L.map('map').setView(storeLocation, 13);
    
    // Tambahkan tile layer dari OpenStreetMap (gratis)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Marker untuk toko (biru)
    const storeMarker = L.marker(storeLocation, {
        icon: L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        })
    }).addTo(map);
    storeMarker.bindPopup('<b>OurStuffies</b><br>Lokasi Toko').openPopup();
    
    // Marker untuk pelanggan (merah, draggable)
    customerMarker = L.marker(storeLocation, {
        draggable: true,
        icon: L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        })
    }).addTo(map);
    customerMarker.bindPopup('<b>Lokasi Pengiriman</b><br>Drag marker ini atau klik map');
    
    // Event klik map untuk pindahkan marker
    map.on('click', function(e) {
        customerMarker.setLatLng(e.latlng);
        calculateDistance(e.latlng.lat, e.latlng.lng);
    });
    
    // Event drag marker
    customerMarker.on('dragend', function(e) {
        const position = e.target.getLatLng();
        calculateDistance(position.lat, position.lng);
    });
    
    // Coba deteksi lokasi user
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const userLocation = [position.coords.latitude, position.coords.longitude];
            customerMarker.setLatLng(userLocation);
            map.setView(userLocation, 13);
            calculateDistance(position.coords.latitude, position.coords.longitude);
        }, function(error) {
            console.log('Geolocation error:', error);
        });
    }
});

function calculateDistance(lat, lng) {
    // Simpan koordinat ke hidden input
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
    
    // Haversine formula untuk hitung jarak
    const R = 6371; // Radius bumi dalam km
    const dLat = (lat - storeLocation[0]) * Math.PI / 180;
    const dLon = (lng - storeLocation[1]) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(storeLocation[0] * Math.PI / 180) * Math.cos(lat * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const distance = R * c;
    
    // Cek apakah melebihi jarak maksimal
    if (distance > maxDistance) {
        document.getElementById('distance-info').style.background = '#ffebee';
        document.getElementById('distance-info').style.color = '#c62828';
        document.getElementById('distance-text').textContent = distance.toFixed(2) + ' (Terlalu jauh! Max: ' + maxDistance + ' km)';
        document.getElementById('delivery-fee-text').textContent = '-';
        if (window.showToast) {
            showToast('Lokasi terlalu jauh dari toko! Maksimal ' + maxDistance + ' km', 'error', 3000);
        }
        return;
    }
    
    // Hitung ongkir
    let deliveryFee = baseFee + (distance * feePerKm);
    
    // Bulatkan ke atas per 1000 (7.200 -> 8.000, 7.000 -> 7.000)
    deliveryFee = Math.ceil(deliveryFee / 1000) * 1000;
    
    // Cek gratis ongkir
    if (subtotalPrice >= freeDeliveryMin) {
        deliveryFee = 0;
    }
    
    // Update tampilan
    document.getElementById('distance-info').style.display = 'block';
    document.getElementById('distance-info').style.background = '#e8f5e9';
    document.getElementById('distance-info').style.color = '#2e7d32';
    document.getElementById('distance-text').textContent = distance.toFixed(2);
    document.getElementById('delivery-fee-text').textContent = deliveryFee === 0 ? 'GRATIS! ' : 'Rp ' + deliveryFee.toLocaleString('id-ID');
    
    // Update summary
    document.getElementById('delivery-summary').style.display = 'flex';
    document.getElementById('delivery-fee-summary').textContent = deliveryFee === 0 ? 'GRATIS' : 'Rp ' + deliveryFee.toLocaleString('id-ID');
    
    const finalTotal = subtotalPrice + deliveryFee;
    document.getElementById('final-total').textContent = 'Rp ' + finalTotal.toLocaleString('id-ID');
}
</script>

<?php include 'includes/footer.php'; ?>