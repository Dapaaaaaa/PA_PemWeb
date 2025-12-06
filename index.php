<?php 
include 'includes/header.php'; 
include 'koneksi.php';
include 'includes/settings_helper.php';
?>

<!-- Success Message untuk Order Placed -->
<?php if (isset($_GET['status']) && $_GET['status'] == 'order_placed'): ?>
<div style="max-width: 800px; margin: 30px auto; padding: 20px;">
    <div class="alert-message alert-success" style="text-align: center; font-size: 18px;">
        Pesanan Anda telah berhasil diproses!
        <?php if (isset($_GET['order'])): ?>
            <br><strong>Nomor Pesanan: <?php echo htmlspecialchars($_GET['order']); ?></strong>
        <?php endif; ?>
        <br><small>Admin kami akan segera menghubungi Anda untuk konfirmasi.</small>
    </div>
</div>
<?php endif; ?>

<section class="hero">
    <?php
    // Ambil banner aktif dari database
    $banners_query = mysqli_query($conn, "SELECT * FROM banners WHERE aktif = 1 ORDER BY urutan ASC LIMIT 5");
    $banners = [];
    while ($banner = mysqli_fetch_assoc($banners_query)) {
        $banners[] = $banner;
    }
    
    // Jika tidak ada banner, gunakan default
    if (empty($banners)) {
        $banners = [[
            'title' => getSetting('hero_title', 'Burger Ayam'),
            'description' => getSetting('hero_description', 'Nikmati rasa burger yang segar, juicy, dan lezat yang dibuat dengan bahan premium dan penuh cinta.'),
            'image_url' => getSetting('hero_image', 'assets/img/product/hero-burger.png'),
            'button_text' => getSetting('hero_button_text', 'Pesan Sekarang'),
            'button_link' => 'menu.php'
        ]];
    }
    ?>
    
    <div class="hero-slider">
        <?php foreach ($banners as $index => $banner): ?>
        <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>">
            <div class="hero-content">
                <div class="hero-banner">
                    <img src="<?php echo htmlspecialchars($banner['image_url']); ?>" alt="Banner <?php echo $index + 1; ?>">
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php if (count($banners) > 1): ?>
    <!-- Slider Navigation -->
    <button class="slider-nav prev" onclick="changeSlide(-1)">❮</button>
    <button class="slider-nav next" onclick="changeSlide(1)">❯</button>
    
    <!-- Slider Indicators -->
    <div class="slider-indicators">
        <?php foreach ($banners as $index => $banner): ?>
        <span class="indicator <?php echo $index === 0 ? 'active' : ''; ?>" onclick="goToSlide(<?php echo $index; ?>)"></span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<script>
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slide');
const indicators = document.querySelectorAll('.indicator');
const totalSlides = slides.length;

function showSlide(index) {
    slides.forEach(slide => slide.classList.remove('active'));
    indicators.forEach(ind => ind.classList.remove('active'));
    
    if (index >= totalSlides) currentSlide = 0;
    if (index < 0) currentSlide = totalSlides - 1;
    
    slides[currentSlide].classList.add('active');
    if (indicators[currentSlide]) {
        indicators[currentSlide].classList.add('active');
    }
}

function changeSlide(direction) {
    currentSlide += direction;
    showSlide(currentSlide);
}

function goToSlide(index) {
    currentSlide = index;
    showSlide(currentSlide);
}

// Auto-slide every 5 seconds
if (totalSlides > 1) {
    setInterval(() => {
        currentSlide++;
        showSlide(currentSlide);
    }, 5000);
}
</script>

<section class="product-section">
    <h2>Menu Kami!</h2>

    <div class="product-grid">

    <?php 
    // Ambil produk yang sudah diatur admin dari tabel menu_display
    $query_featured = "SELECT p.*, k.nama as kategori_nama, md.label
                       FROM menu_display md
                       JOIN produk p ON md.produk_id = p.id
                       LEFT JOIN kategori k ON p.kategori_id = k.id
                       WHERE md.aktif = 1 AND p.aktif = 1
                       ORDER BY md.urutan ASC
                       LIMIT 6";
    $result_featured = mysqli_query($conn, $query_featured);

    if ($result_featured && mysqli_num_rows($result_featured) > 0) {
        while ($produk = mysqli_fetch_assoc($result_featured)) {
            // Tentukan badge label
            $label_html = '';
            if ($produk['label'] === 'best_seller') {
                $label_html = '<span class="product-label" style="background: linear-gradient(135deg, #ff6b6b, #ff5252); color: white; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; position: absolute; top: 10px; right: 10px; box-shadow: 0 2px 8px rgba(255,82,82,0.3); z-index: 1;">🔥 Best Seller</span>';
            } elseif ($produk['label'] === 'favorit') {
                $label_html = '<span class="product-label" style="background: linear-gradient(135deg, #ffd700, #ffb700); color: #333; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; position: absolute; top: 10px; right: 10px; box-shadow: 0 2px 8px rgba(255,183,0,0.3); z-index: 1;">⭐ Favorit</span>';
            }
            
            echo '
                <div class="product-card" style="position: relative;">
                    '.$label_html.'
                    <img src="'.$produk['url_gambar'].'" alt="'.htmlspecialchars($produk['nama']).'">
                    <h3>'.htmlspecialchars($produk['nama']).'</h3>
                    <p>'.htmlspecialchars($produk['deskripsi']).'</p>
                    <span class="price">Rp '.number_format($produk['harga'], 0, ',', '.').'</span>

                    <a href="menu.php?kategori='.$produk['kategori_id'].'" class="btn-lihat">
                        Lihat Semua '.htmlspecialchars($produk['kategori_nama']).'
                    </a>
                </div>
            ';
        }
    } else {
        echo '<p style="text-align: center; color: #666;">Belum ada produk yang ditampilkan. Silakan atur di admin panel.</p>';
    }
    ?>

    </div>
</section>

<section class="Lokasi-section">
    <h2>Alamat Kami</h2>
    <!-- <p class="Lokasi-desc">Come and taste our delicious burgers at our restaurant!</p> -->
    
    <div class="map-container">
        <iframe 
            class="google-map"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.687140235874!2d117.14484747589199!3d-0.46461823528196516!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df67900560a5033%3A0xd14b9dfd79c14c60!2sOurStuffies!5e0!3m2!1sid!2sid!4v1762506331525!5m2!1sid!2sid"
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>

    <div class="Lokasi-info">
        <div class="info-box">
            <h3>Alamat</h3>
            <p>Blk. A-B No.53b, Gn. Kelua,<br> Kec. Samarinda Ulu, Kota Samarinda,<br> Kalimantan Timur 75243</p>
        </div>
        <div class="info-box">
            <h3>Kontak</h3>
            <p>WhatsApp: <?php echo getSetting('store_phone', '+62 859-7490-6945'); ?></p>
        </div>
        <div class="info-box">
            <h3>Jam Operasional</h3>
            <p>Senin - Minggu: 10:00 Pagi - 5 Sore<br>Jika melebihi dari jam pelayanan, maka akan dikirim esok hari.</p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
