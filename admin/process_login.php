<?php
// Include koneksi database dan session
include '../koneksi.php';
include 'includes/session.php';

// Cek apakah form disubmit menggunakan POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil data dari form (POST)
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // Query untuk cek email di database
    $query = "SELECT * FROM admin WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    // Cek apakah email ditemukan
    if (mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        
        // Verifikasi password (gunakan password_verify jika pakai hash)
        // Untuk password biasa (tidak di-hash):
        if ($password === $admin['password']) {
            // Login berhasil, simpan ke session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['nama'];
            $_SESSION['admin_email'] = $admin['email'];
            
            // Redirect ke dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // Password salah
            $_SESSION['error'] = "Password salah!";
            header("Location: login.php");
            exit();
        }
    } else {
        // Email tidak ditemukan
        $_SESSION['error'] = "Email tidak ditemukan!";
        header("Location: login.php");
        exit();
    }
    
} else {
    // Jika diakses langsung tanpa POST
    header("Location: login.php");
    exit();
}
?>