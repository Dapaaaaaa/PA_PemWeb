<?php 

include 'includes/session.php';

if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - OurStuffies</title>

    <!-- Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Admin CSS -->
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<section class="admin-login-page">
    <div class="login-container">
        <div class="login-header">
            <h1>🔐 Admin Login</h1>
            <p>Welcome back! Please login to your account.</p>
        </div>

        <?php
        // Tampilkan pesan error jika ada
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']); // Hapus setelah ditampilkan
        }
        
        // Tampilkan pesan sukses jika ada
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        ?>

        <form class="login-form" action="process_login.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required 
                       placeholder="admin@ourstuffies.com" value="admin@ourstuffies.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Enter your password" value="admin123">
            </div>

            <button type="submit" class="btn-login">Login to Dashboard</button>
        </form>

        <div class="login-footer">
            <p><a href="../index.php">← Back to Main Site</a></p>
        </div>
    </div>
</section>

<script>
// Simple login simulation (Frontend only - no backend logic)
document.querySelector('.login-form').addEventListener('submit', function(e) {
    // For demo purposes, just proceed to dashboard
    // In real implementation, this would validate with backend
    console.log('Login submitted');
});
</script>

</body>
</html>