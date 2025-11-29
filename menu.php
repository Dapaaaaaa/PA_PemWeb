<?php 

include 'includes/header.php'; 
include 'koneksi.php';
include 'includes/settings_helper.php';

?>

<section class="page-header">
    <div class="page-header-content">
        <h1>Menu Yang Tersedia</h1>
        <p>Jelajahi pilihan burger premium kami yang lezat, dibuat dengan cinta!</p>
    </div>
</section>

<!-- <section class="menu-page"> -->
    <!-- Menu Categories -->
    <!-- <div class="menu-categories">
        <button class="category-btn active" data-category="all">All Menu</button>
        <button class="category-btn" data-category="signature">Signature Burgers</button>
        <button class="category-btn" data-category="special">Special Edition</button>
        <button class="category-btn" data-category="sides">Sides & Drinks</button>
    </div> -->

    <!-- Signature Burgers -->
    <!-- <div class="menu-page"> -->

<!-- Menu Categories Filter -->
<div class="menu-categories">
    <button class="category-btn active" data-category="all">All Menu</button>
    <?php 
    // Ambil semua kategori untuk tombol filter
    $queryKategoriFilter = "SELECT * FROM kategori ORDER BY id ASC";
    $resultKategoriFilter = mysqli_query($conn, $queryKategoriFilter);
    while ($katFilter = mysqli_fetch_assoc($resultKategoriFilter)) {
        $categorySlug = strtolower($katFilter['nama']);
        echo '<button class="category-btn" data-category="'.$categorySlug.'">'.$katFilter['nama'].'</button>';
    }
    ?>
</div>

<?php 
// Ambil semua kategori dari database
$queryKategori = "SELECT * FROM kategori ORDER BY id ASC";
$resultKategori = mysqli_query($conn, $queryKategori);

while ($kategori = mysqli_fetch_assoc($resultKategori)) {
    $dataCategory = strtolower($kategori['nama']);
?>
    <div class="menu-section" data-category="<?php echo $dataCategory; ?>">
        <h2 class="menu-section-title"><?php echo $kategori['nama']; ?></h2>
        <div class="menu-grid">
            <?php
            // Ambil produk sesuai kategori
            $kategori_id = $kategori['id'];
            $queryProduk = "SELECT * FROM produk WHERE kategori_id = '$kategori_id' AND aktif = 1";
            $resultProduk = mysqli_query($conn, $queryProduk);

            if (mysqli_num_rows($resultProduk) == 0) {
                echo "<p style='color:#888;'>Belum ada produk di kategori ini.</p>";
            }

            while ($produk = mysqli_fetch_assoc($resultProduk)) {
                $gambar = !empty($produk['url_gambar']) ? str_replace("\\","/",$produk['url_gambar']) : "assets/img/no-image.png";
                $nama = htmlspecialchars($produk['nama']);
                $deskripsi = htmlspecialchars($produk['deskripsi']);
                $harga = number_format($produk['harga'], 0, ',', '.');
                $namaEncode = urlencode($produk['nama']);
            ?>
                <div class="menu-item">
                    <div class="menu-item-image">
                        <img src="<?php echo $gambar; ?>" alt="<?php echo $nama; ?>">
                        <?php if ($produk['stok'] > 0) { ?>
                            <span class="badge-popular">Tersedia</span>
                        <?php } else { ?>
                            <span class="badge-popular" style="background:red;">Habis</span>
                        <?php } ?>
                    </div>
                    <div class="menu-item-content">
                        <h3><?php echo $nama; ?></h3>
                        <p><?php echo $deskripsi; ?></p>
                        <div class="menu-item-footer">
                            <span class="price">Rp <?php echo $harga; ?></span>
                            <div style="display: flex; gap: 8px;">
                                <!-- Add to Cart Form -->
                                <form method="POST" action="add_to_cart.php" style="margin: 0;">
                                    <input type="hidden" name="name" value="<?php echo $nama; ?>">
                                    <input type="hidden" name="price" value="<?php echo $produk['harga']; ?>">
                                    <input type="hidden" name="img" value="<?php echo $gambar; ?>">
                                    <input type="hidden" name="qty" value="1">
                                    <input type="hidden" name="redirect" value="menu.php">
                                    <button type="submit" class="btn-add-cart" 
                                            <?php echo ($produk['stok'] <= 0) ? 'disabled' : ''; ?>>
                                        Add
                                    </button>
                                </form>
                                <!-- WhatsApp Order
                                <a href="https://wa.me/<?php echo getSetting('store_whatsapp', '6285974906945'); ?>?text=Halo,%20saya%20ingin%20order%20<?php echo $namaEncode; ?>" 
                                   class="btn-order-menu" target="_blank">Order Now</a> -->
                            </div>
                        </div>
                    </div>
                </div>
            <?php } // end while produk ?>
        </div>
    </div>
<?php } // end while kategori ?>
</div>


    

    <!-- Call to Action -->
    <div class="menu-cta">
        <h2>Ready to Order?</h2>
        <p>Contact us via WhatsApp for fast ordering and delivery!</p>
        <a href="https://wa.me/<?php echo getSetting('store_whatsapp', '6285974906945'); ?>?text=Halo%20OurStuff,%20saya%20ingin%20melihat%20menu%20lengkap!" 
           class="btn-whatsapp-large" target="_blank">
            Order via WhatsApp
        </a>
    </div>
</section>

<script>
// Simple category filter
document.querySelectorAll('.category-btn').forEach(button => {
    button.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
        // Add active class to clicked button
        this.classList.add('active');
        
        const category = this.getAttribute('data-category');
        
        // Show/hide sections based on category
        document.querySelectorAll('.menu-section').forEach(section => {
            if (category === 'all') {
                section.style.display = 'block';
            } else if (section.getAttribute('data-category') === category) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
