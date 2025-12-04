<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil data dari POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $img = $_POST['img'] ?? '';
    $qty = intval($_POST['qty'] ?? 1);

    // Validasi data
    if (empty($name) || $price <= 0 || empty($img)) {
        header('Location: menu.php?error=invalid_data');
        exit;
    }

    // Inisialisasi cart jika belum ada
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Cek apakah item sudah ada di cart
    $item_exists = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['name'] === $name) {
            $item['qty'] += $qty;
            $item_exists = true;
            break;
        }
    }

    // Jika item belum ada, tambahkan baru
    if (!$item_exists) {
        $_SESSION['cart'][] = [
            'name' => $name,
            'price' => $price,
            'img' => $img,
            'qty' => $qty,
            'notes' => '',
            'sauce_options' => []
        ];
    }

    // Redirect ke halaman sebelumnya atau cart
    $redirect = $_POST['redirect'] ?? 'menu.php';
    $product_id = $_POST['product_id'] ?? '';
    
    // Tambahkan anchor untuk kembali ke posisi produk
    if (!empty($product_id)) {
        header('Location: ' . $redirect . '?added=success#product-' . $product_id);
    } else {
        header('Location: ' . $redirect . '?added=success');
    }
    exit;
} else {
    // Jika bukan POST, redirect ke menu
    header('Location: menu.php');
    exit;
}
