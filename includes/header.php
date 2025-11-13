<?php
// header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OurStuffies</title>

    <!-- Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon -->
     <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">



    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="main-header">
    <a href="index.php" class="logo">
        OurStuffies
    </a>

     <div class="nav-toggle" id="nav-toggle">
            <span></span>
            <span></span>
            <span></span>
    </div>

    <nav class="nav-menu" id="nav-menu">  
        <a href="index.php" class="nav-item">
            <!-- Beranda Icon -->
            <span class="material-icons">
                home
            </span>
            <span>Beranda</span>
        </a>
        <a href="menu.php" class="nav-item">
            <!-- Menu Icon -->
            <span class="material-icons">
                restaurant
            </span>
            <span>Menu</span>
        </a>
        <a href="located.php" class="nav-item">
            <!-- Location Icon -->
            <span class="material-icons">
                location_on
            </span>
            <span>Lokasi</span>
        </a>
        <a href="contact.php" class="nav-item">
            <!-- Phone Icon -->
            <span class="material-icons">
                phone_iphone
            </span>
            <span>Kontak</span>
        </a>
        <a href="account.php" class="nav-item">
            <!-- User Icon -->
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="7" r="4" stroke="#537B2F" stroke-width="2"/><path d="M6 21C6 17.13 8.69 14 12 14C15.31 14 18 17.13 18 21" stroke="#537B2F" stroke-width="2"/></svg>
            <span>Account</span>
        </a>
    </nav>

    <div class="nav-overlay" id="nav-overlay">
    </div>
</header>

<script>
const toggle = document.getElementById('nav-toggle');
const menu = document.getElementById('nav-menu');
const overlay = document.getElementById('nav-overlay');

toggle.addEventListener('click', () => {
  menu.classList.toggle('active');
  overlay.classList.toggle('active');
});

// Klik di overlay untuk menutup
overlay.addEventListener('click', () => {
  menu.classList.remove('active');
  overlay.classList.remove('active');
});

</script>