<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroy the session
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - OurStuffies Admin</title>

    <!-- Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Admin CSS -->
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<section class="admin-login-page">
    <div class="login-container">
        <div class="login-header">
            <h1>Logging Out...</h1>
            <p>Thank you for using OurStuffies Admin Panel</p>
        </div>

        <div style="text-align: center; padding: 30px 0;">
            <div style="font-size: 64px; margin-bottom: 20px;">✅</div>
            <p style="font-size: 16px; color: #666; margin-bottom: 25px;">
                You have been successfully logged out.
            </p>
            <a href="login.php" class="btn-login" style="display: inline-block; text-decoration: none;">
                Login Again
            </a>
        </div>

        <div class="login-footer" style="margin-top: 30px;">
            <p><a href="../index.php">← Back to Main Site</a></p>
        </div>
    </div>
</section>

<script>
// Auto redirect after 3 seconds
setTimeout(function() {
    window.location.href = 'login.php';
}, 3000);

console.log('Admin logged out successfully');
</script>

</body>
</html>