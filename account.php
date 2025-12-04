<?php
session_start();
include 'includes/header.php';
include 'koneksi.php';
include 'includes/settings_helper.php';

// Check if user is logged in (simple check - you can expand this)
$isLoggedIn = isset($_SESSION['user_id']);
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
?>

<?php if (!$isLoggedIn): ?>
<!-- Login/Register Page -->
<section class="account-page">
    <div class="account-container">
        <div class="account-forms">
            <!-- Login Form -->
            <div class="form-wrapper" id="loginForm">
                <div class="form-header">
                    <h2>Selamat Datang Kembali! 👋</h2>
                    <p>Masuk untuk mengakses akun Anda</p>
                </div>
                
                <form class="auth-form" action="process-login.php" method="POST">
                    <div class="form-group">
                        <label for="login-email">Alamat Email</label>
                        <input type="email" id="login-email" name="email" required placeholder="namaemail@gmail.com">
                    </div>

                    <div class="form-group">
                        <label for="login-password">Kata Sandi</label>
                        <input type="password" id="login-password" name="password" required placeholder="Masukkan kata sandi Anda">
                    </div>

                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember"> Ingat saya
                        </label>
                        <a href="#" class="forgot-link">Lupa kata sandi?</a>
                    </div>

                    <button type="submit" class="btn-auth">Masuk</button>
                </form>

                <div class="form-footer">
                    <p>Belum punya akun? <a href="#" onclick="toggleForms(); return false;">Daftar</a></p>
                </div>
            </div>

            <!-- Register Form -->
            <div class="form-wrapper hidden" id="registerForm">
                <div class="form-header">
                    <h2>Buat Akun 🎉</h2>
                    <p>Bergabunglah dengan keluarga OurStuff hari ini!</p>
                </div>
                
                <form class="auth-form" action="process-register.php" method="POST">
                    <div class="form-group">
                        <label for="register-name">Nama Lengkap</label>
                        <input type="text" id="register-name" name="name" required placeholder="Nama lengkap Anda">
                    </div>

                    <div class="form-group">
                        <label for="register-email">Alamat Email</label>
                        <input type="email" id="register-email" name="email" required placeholder="namaemail@gmail.com">
                    </div>

                    <div class="form-group">
                        <label for="register-phone">Nomor Telepon</label>
                        <input type="tel" id="register-phone" name="phone" required placeholder="+62 xxx-xxxx-xxxx">
                    </div>

                    <div class="form-group">
                        <label for="register-password">Kata Sandi</label>
                        <input type="password" id="register-password" name="password" required placeholder="Buat kata sandi yang kuat">
                    </div>

                    <div class="form-group">
                        <label for="register-confirm">Konfirmasi Kata Sandi</label>
                        <input type="password" id="register-confirm" name="confirm_password" required placeholder="Konfirmasi kata sandi Anda">
                    </div>

                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="terms" required> Saya setuju dengan <a href="#">Syarat & Ketentuan</a>
                        </label>
                    </div>

                    <button type="submit" class="btn-auth">Buat Akun</button>
                </form>

                <div class="form-footer">
                    <p>Sudah punya akun? <a href="#" onclick="toggleForms(); return false;">Masuk</a></p>
                </div>
            </div>
        </div>

        <!-- Info Side -->
        <div class="account-info">
            <div class="info-content">
                <h2>Mengapa Membuat Akun?</h2>
                <div class="benefits-list">
                    <!-- <div class="benefit-item">
                        <div class="benefit-icon"></div>
                        <div class="benefit-text">
                            <h4>Exclusive Rewards</h4>
                            <p>Earn points with every purchase and get special discounts</p>
                        </div>
                    </div> -->
                    <div class="benefit-item">
                        <div class="benefit-icon"></div>
                        <div class="benefit-text">
                            <h4>Checkout Cepat</h4>
                            <p>Simpan info Anda untuk pemesanan lebih cepat berikutnya</p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <div class="benefit-icon"></div>
                        <div class="benefit-text">
                            <h4>Riwayat Pesanan</h4>
                            <p>Lacak pesanan Anda dan pesan ulang favorit Anda dengan mudah</p>
                        </div>
                    </div>
                    <!-- <div class="benefit-item">
                        <div class="benefit-icon"></div>
                        <div class="benefit-text">
                            <h4>Special Offers</h4>
                            <p>Dapatkan pemberitahuan tentang menu baru dan promosi</p>
                        </div>
                    </div> -->
                </div>

                <div class="quick-order">
                    <h3>Ingin Memesan Tanpa Akun?</h3>
                    <p>Anda selalu dapat memesan langsung melalui WhatsApp!</p>
                    <a href="https://wa.me/<?php echo getSetting('store_whatsapp', '6285974906945'); ?>?text=Halo%20OurStuff,%20saya%20ingin%20order!" 
                       class="btn-whatsapp-account" target="_blank">
                        Pesan via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function toggleForms() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    loginForm.classList.toggle('hidden');
    registerForm.classList.toggle('hidden');
}
</script>

<?php else: ?>
<!-- User Dashboard -->
<section class="page-header">
    <div class="page-header-content">
        <h1>Halo, <?php echo htmlspecialchars($userName); ?>!</h1>
        <p>Selamat datang di dasbor akun Anda</p>
    </div>
</section>

<section class="dashboard-page">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="dashboard-sidebar">
            <div class="user-profile">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($userName, 0, 1)); ?>
                </div>
                <h3><?php echo htmlspecialchars($userName); ?></h3>
                <p class="user-email">member@ourstuff.com</p>
            </div>

            <nav class="dashboard-nav">
                <a href="#" class="nav-link active" data-section="orders">
                    <span></span> My Orders
                </a>
                <a href="#" class="nav-link" data-section="profile">
                    <span></span> Profile
                </a>
                <a href="#" class="nav-link" data-section="rewards">
                    <span></span> Rewards
                </a>
                <a href="#" class="nav-link" data-section="addresses">
                    <span></span> Saved Addresses
                </a>
                <a href="#" class="nav-link" data-section="settings">
                    <span></span> Settings
                </a>
                <a href="logout.php" class="nav-link logout">
                    <span></span> Logout
                </a>
            </nav>
        </div>

        <!-- Content Area -->
        <div class="dashboard-content">
            <!-- Orders Section -->
            <div class="content-section active" id="orders-section">
                <h2>My Orders</h2>
                <div class="orders-list">
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <span class="order-id">#ORD-001</span>
                                <span class="order-date">Nov 5, 2025</span>
                            </div>
                            <span class="order-status status-delivered">Delivered</span>
                        </div>
                        <div class="order-items">
                            <p>2x Premium Cheeseburger</p>
                            <p>1x French Fries</p>
                        </div>
                        <div class="order-footer">
                            <span class="order-total">Rp 105.000</span>
                            <a href="#" class="btn-reorder">Reorder</a>
                        </div>
                    </div>

                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <span class="order-id">#ORD-002</span>
                                <span class="order-date">Nov 3, 2025</span>
                            </div>
                            <span class="order-status status-processing">Processing</span>
                        </div>
                        <div class="order-items">
                            <p>1x Spicy Double Patty</p>
                            <p>2x Soft Drink</p>
                        </div>
                        <div class="order-footer">
                            <span class="order-total">Rp 71.000</span>
                            <a href="#" class="btn-track">Track Order</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Section -->
            <div class="content-section" id="profile-section">
                <h2>My Profile</h2>
                <form class="profile-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" value="<?php echo htmlspecialchars($userName); ?>">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" value="member@ourstuff.com">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="tel" value="+62 812-3456-7890">
                        </div>
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" value="1990-01-01">
                        </div>
                    </div>
                    <button type="submit" class="btn-save">Save Changes</button>
                </form>
            </div>

            <!-- Rewards Section -->
            <div class="content-section" id="rewards-section">
                <h2>Rewards & Points</h2>
                <div class="rewards-card">
                    <div class="points-display">
                        <h3>Your Points</h3>
                        <div class="points-number">1,250</div>
                        <p>Keep ordering to earn more points!</p>
                    </div>
                    <div class="rewards-progress">
                        <p>🎁 500 more points to get a free burger!</p>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 71%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Dashboard navigation
document.querySelectorAll('.dashboard-nav .nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        if (!this.classList.contains('logout')) {
            e.preventDefault();
            const section = this.getAttribute('data-section');
            
            // Update active nav
            document.querySelectorAll('.dashboard-nav .nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // Show section
            document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
            document.getElementById(section + '-section').classList.add('active');
        }
    });
});
</script>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>
