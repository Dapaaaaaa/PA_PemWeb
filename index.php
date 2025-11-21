<?php 
include 'includes/header.php'; 
include 'koneksi.php';
?>

<!-- Success Message untuk Order Placed -->
<?php if (isset($_GET['status']) && $_GET['status'] == 'order_placed'): ?>
<div style="max-width: 800px; margin: 30px auto; padding: 20px;">
    <div class="alert-message alert-success" style="text-align: center; font-size: 18px;">
        🎉 Pesanan Anda telah berhasil diproses!
        <?php if (isset($_GET['order'])): ?>
            <br><strong>Nomor Pesanan: <?php echo htmlspecialchars($_GET['order']); ?></strong>
        <?php endif; ?>
        <br><small>Admin kami akan segera menghubungi Anda untuk konfirmasi.</small>
    </div>
</div>
<?php endif; ?>

<section class="hero">
    <div class="hero-text">
        <h1>Single Patty</h1>
        <p>Enjoy the taste of a fresh, juicy, flavorful burger made with premium ingredients and crafted with love.</p>
        <a href="menu.php" class="btn-order">Order Now</a>
    </div>

    <div class="hero-image">
        <img src="assets/img/hero-burger.png" alt="Burger">
    </div>
</section>

<section class="product-section">
    <h2>Menu Kami!</h2>

    <div class="product-grid">

    <?php 
    
    $query = "SELECT * FROM produk";
    $query = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($query)) {
        echo '
            <div class="product-card">
                <img src ="' .$row['url_gambar']. '"alt ="'. $row['nama']. '">
                <h3>'. $row['nama']. '</h3>
                <p>'. $row['deskripsi']. '</p>
                <span class = "price">Rp' . number_format($row['harga'], 0, ','. '.') .' </span>
            </div>
        ';
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
            <p>WhatsApp: +62 859-7490-6945</p>
        </div>
        <div class="info-box">
            <h3>Jam Operasional</h3>
            <p>Senin - Minggu: 10:00 Pagi - 5 Sore<br>Jika melebihi dari jam pelayanan, maka akan dikirim esok hari.</p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
