<?php include 'includes/header.php'; ?>

<section class="page-header">
    <div class="page-header-content">
        <h1>📍 Temukan Kami</h1>
        <p>Visit our store and experience the best burgers in town!</p>
    </div>
</section>

<section class="Lokasi-main">
    <div class="Lokasi-intro">
        <h2>Selamat Datang di OurStuffies!</h2>
        <p>Kami berada di tengah kota Samarinda, siap melayani Anda dengan menu paling lezat yang dibuat dengan cinta dan bahan berkualitas premium.Anda dapat memesan untuk pengantaran, kami siap memuaskan selera Anda!</p>
    </div>

    <!-- Google Maps -->
    <div class="map-wrapper">
        <h3>📌 Lokasi Kami Pada Map</h3>
        <div class="map-container">
            <iframe 
                class="google-map"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.687140235874!2d117.14484747589199!3d-0.46461823528196516!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df67900560a5033%3A0xd14b9dfd79c14c60!2sOurStuffies!5e0!3m2!1sid!2sid!4v1762506331525!5m2!1sid!2sid"
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>

    <!-- Lokasi Details -->
    <div class="Lokasi-details">
        <div class="detail-card">
            <div class="detail-icon">📍</div>
            <h3>Alamat</h3>
            <p>Blk. A-B No.53b, Gn. Kelua</p>
            <p>Kec. Samarinda Ulu</p>
            <p>Kota Samarinda, Kalimantan Timur</p>
            <p class="postal-code">75243</p>
            <a href="https://maps.app.goo.gl/yourlink" class="btn-direction" target="_blank">Get Directions</a>
        </div>

        <div class="detail-card">
            <div class="detail-icon">🕐</div>
            <h3>Jam Operasional</h3>
            <div class="hours-list">
                <div class="hours-item">
                    <span class="day">Senin - Jumat</span>
                    <span class="time">10:00 - 17:00</span>
                </div>
                <div class="hours-item">
                    <span class="day">Sabtu - Minggu</span>
                    <span class="time">10:00 - 17:00</span>
                </div>
            </div>
            <p class="hours-note">⚠️ Pesanan di luar jam operasional akan diproses keesokan hari.</p>
        </div>

        <div class="detail-card">
            <div class="detail-icon">📞</div>
            <h3>Hubungi Kami</h3>
            <div class="Kontak-info">
                <p><strong>WhatsApp:</strong></p>
                <a href="https://wa.me/6285974906945" class="Kontak-link" target="_blank">
                    +62 859-7490-6945
                </a>
                <p class="Kontak-desc">Klik untuk chat langsung via WhatsApp</p>
            </div>
            <a href="https://wa.me/6285974906945?text=Halo%20OurStuff,%20saya%20ingin%20order%20burger!" 
               class="btn-whatsapp" target="_blank">
                💬 Chat di WhatsApp
            </a>
        </div>
    </div>

    <!-- Why Visit Us Section -->
    <div class="why-visit">
        <h2>Kenapa OurStuffies?</h2>
        <div class="reasons-grid">
            <div class="reason-card">
                <div class="reason-icon">🍔</div>
                <h4>Premium Quality</h4>
                <p>Bahan-bahan segar berkualitas tinggi dipilih setiap hari</p>
            </div>
            <div class="reason-card">
                <div class="reason-icon">👨‍🍳</div>
                <h4>Expert Chefs</h4>
                <p>Tim chef berpengalaman yang passionate dalam setiap burger</p>
            </div>
            <div class="reason-card">
                <div class="reason-icon">🚚</div>
                <h4>Fast Delivery</h4>
                <p>Layanan delivery cepat ke seluruh area Samarinda</p>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="Lokasi-cta">
        <h2>Ready to Try Our Burgers?</h2>
        <p>Visit us today or order online for delivery!</p>
        <div class="cta-buttons">
            <a href="menu.php" class="btn-primary">View Menu</a>
            <a href="https://wa.me/6285974906945?text=Halo%20OurStuff,%20saya%20ingin%20order!" 
               class="btn-secondary" target="_blank">Order Now</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
