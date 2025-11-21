<?php 
include 'includes/header.php'; 
include 'koneksi.php';
include 'includes/settings_helper.php';
?>

<section class="page-header">
    <div class="page-header-content">
        <h1>Kontak Us</h1>
        <p>We'd love to hear from you! Get in touch with us today.</p>
    </div>
</section>

<section class="Kontak-page">
    <div class="Kontak-container">
        <!-- Kontak Form -->
        <div class="Kontak-form-section">
            <h2>Send Us a Message</h2>
            <p class="form-description">Have questions or special requests? Fill out the form below and we'll get back to you as soon as possible!</p>
            
            <form class="Kontak-form" id="KontakForm">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" required placeholder="Your name">
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required placeholder="your.email@example.com">
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" required placeholder="+62 xxx-xxxx-xxxx">
                </div>

                <div class="form-group">
                    <label for="subject">Subject *</label>
                    <select id="subject" name="subject" required>
                        <option value="">Select a subject</option>
                        <option value="order">Order Inquiry</option>
                        <option value="catering">Catering Services</option>
                        <option value="feedback">Feedback & Suggestions</option>
                        <option value="complaint">Complaint</option>
                        <option value="partnership">Partnership</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea id="message" name="message" rows="6" required placeholder="Tell us what's on your mind..."></textarea>
                </div>

                <button type="submit" class="btn-submit">Send Message</button>
            </form>
        </div>

        <!-- Kontak Information -->
        <div class="Kontak-info-section">
            <h2>Get In Touch</h2>
            
            <div class="Kontak-info-card">
                <div class="info-icon"></div>
                <div class="info-content">
                    <h3>Visit Us</h3>
                    <p>Blk. A-B No.53b, Gn. Kelua<br>
                    Kec. Samarinda Ulu<br>
                    Kota Samarinda, Kalimantan Timur<br>
                    75243</p>
                    <a href="located.php" class="info-link">View on Map →</a>
                </div>
            </div>

            <div class="Kontak-info-card">
                <div class="info-icon"></div>
                <div class="info-content">
                    <h3>Call or Text</h3>
                    <p><strong>WhatsApp:</strong></p>
                    <a href="https://wa.me/<?php echo getSetting('store_whatsapp', '6285974906945'); ?>" class="Kontak-phone" target="_blank"><?php echo getSetting('store_phone', '+62 859-7490-6945'); ?></a>
                    <p class="info-note">Available during business hours</p>
                </div>
            </div>

            <div class="Kontak-info-card">
                <div class="info-icon"></div>
                <div class="info-content">
                    <h3>Business Hours</h3>
                    <p><strong>Senin - Minggu</strong><br>
                    10:00 AM - 5:00 PM</p>
                    <p class="info-warning">Orders after closing time will be processed the next day</p>
                </div>
            </div>

            <div class="Kontak-info-card">
                <div class="info-icon"></div>
                <div class="info-content">
                    <h3>Quick Order</h3>
                    <p>Want to order right away?</p>
                    <a href="https://wa.me/<?php echo getSetting('store_whatsapp', '6285974906945'); ?>?text=Halo%20OurStuff,%20saya%20ingin%20order!" 
                       class="btn-whatsapp-Kontak" target="_blank">
                        Chat on WhatsApp
                    </a>
                </div>
            </div>

            <!-- Social Media -->
            <div class="social-media-section">
                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="#" class="social-link" title="Instagram">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                            <rect x="2" y="2" width="20" height="20" rx="5" stroke="currentColor" stroke-width="2"/>
                            <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="2"/>
                            <circle cx="18" cy="6" r="1" fill="currentColor"/>
                        </svg>
                    </a>
                    <a href="#" class="social-link" title="Facebook">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <a href="#" class="social-link" title="Twitter">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                            <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <a href="https://wa.me/<?php echo getSetting('store_whatsapp', '6285974906945'); ?>" class="social-link" title="WhatsApp">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z" fill="currentColor"/>
                            <path d="M20.52 3.449C18.24 1.245 15.24 0 12.045 0 5.463 0 .104 5.334.101 11.893c0 2.096.549 4.14 1.595 5.945L0 24l6.335-1.652a12.062 12.062 0 0 0 5.713 1.447h.005c6.581 0 11.941-5.334 11.944-11.893 0-3.176-1.24-6.165-3.477-8.453zm-8.475 18.302h-.004a9.945 9.945 0 0 1-5.057-1.383l-.363-.214-3.76.982.999-3.648-.235-.374a9.86 9.86 0 0 1-1.511-5.26c.002-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.892 6.993c-.002 5.45-4.436 9.885-9.888 9.885z" fill="currentColor"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="faq-section">
        <h2>Frequently Asked Questions</h2>
        <div class="faq-grid">
            <div class="faq-item">
                <h4>Do you offer delivery?</h4>
                <p>Yes! We offer delivery services throughout Samarinda. Delivery fee varies based on distance.</p>
            </div>
            <div class="faq-item">
                <h4>What payment methods do you accept?</h4>
                <p>We accept cash, bank transfer, e-wallets (GoPay, OVO, Dana), and debit/credit cards.</p>
            </div>
            <div class="faq-item">
                <h4>Do you cater for events?</h4>
                <p>Absolutely! We offer catering services for parties, corporate events, and special occasions. Kontak us for details!</p>
            </div>
            <div class="faq-item">
                <h4>Can I pre-order?</h4>
                <p>Yes, you can pre-order by Kontaking us via WhatsApp. We recommend ordering at least 1 hour in advance.</p>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('KontakForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form data
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    const subject = document.getElementById('subject').value;
    const message = document.getElementById('message').value;
    
    // Create WhatsApp message
    const waMessage = `Halo OurStuff!%0A%0A` +
                     `*Nama:* ${encodeURIComponent(name)}%0A` +
                     `*Email:* ${encodeURIComponent(email)}%0A` +
                     `*Phone:* ${encodeURIComponent(phone)}%0A` +
                     `*Subject:* ${encodeURIComponent(subject)}%0A%0A` +
                     `*Message:*%0A${encodeURIComponent(message)}`;
    
    // Redirect to WhatsApp
    window.open(`https://wa.me/<?php echo getSetting('store_whatsapp', '6285974906945'); ?>?text=${waMessage}`, '_blank');
    
    // Optional: Reset form
    this.reset();
    alert('Redirecting to WhatsApp...');
});
</script>

<?php include 'includes/footer.php'; ?>
