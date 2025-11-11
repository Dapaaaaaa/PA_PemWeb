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


    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="main-header">
    <a href="index.php" class="logo">
        <div class="logo">OurStuffies</div>
    </a>

    <nav class="nav-menu">
        <a href="index.php" class="nav-item">
            <!-- Beranda Icon -->
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M3 10L12 3L21 10V20C21 20.55 20.55 21 20 21H4C3.45 21 3 20.55 3 20V10Z" stroke="#537B2F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>Beranda<pan>
        </a>
        <a href="menu.php" class="nav-item">
            <!-- Menu Icon -->
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M4 6H20M4 12H20M4 18H20" stroke="#537B2F" stroke-width="2" stroke-linecap="round"/></svg>
            <span>Menu</span>
        </a>
        <a href="located.php" class="nav-item">
            <!-- Location Icon -->
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M12 3C8.13 3 5 6.13 5 10C5 15.25 12 21 12 21C12 21 19 15.25 19 10C19 6.13 15.87 3 12 3Z" stroke="#537B2F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>Lokasi</span>
        </a>
        <a href="contact.php" class="nav-item">
            <!-- Phone Icon -->
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M22 16.92V20C22 20.55 21.55 21 21 21C10.61 21 3 13.39 3 3C3 2.45 3.45 2 4 2H7.09C7.52 2 7.89 2.28 7.98 2.69C8.34 4.33 8.98 5.9 9.88 7.34C10.07 7.64 10.05 8.04 9.82 8.32L8.13 10.3C9.66 12.97 12.04 15.34 14.7 16.87L16.68 15.18C16.96 14.94 17.36 14.93 17.66 15.12C19.1 16.02 20.67 16.66 22.31 17.02C22.72 17.11 23 17.48 23 17.91V17.92H22Z" stroke="#537B2F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>Kontak</span>
        </a>
        <a href="account.php" class="nav-item">
            <!-- User Icon -->
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="7" r="4" stroke="#537B2F" stroke-width="2"/><path d="M6 21C6 17.13 8.69 14 12 14C15.31 14 18 17.13 18 21" stroke="#537B2F" stroke-width="2"/></svg>
            <span>Account</span>
        </a>
    </nav>
</header>
