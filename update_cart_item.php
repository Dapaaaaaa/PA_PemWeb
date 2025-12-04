<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $index = isset($_POST['item_index']) ? intval($_POST['item_index']) : -1;
    $field = isset($_POST['field']) ? $_POST['field'] : '';
    
    if ($index >= 0 && isset($_SESSION['cart'][$index])) {
        if ($field === 'sauce') {
            // Update sauce options
            $sauce_selections = isset($_POST['sauce_options']) ? $_POST['sauce_options'] : [];
            $_SESSION['cart'][$index]['sauce_options'] = $sauce_selections;
            
            echo json_encode([
                'success' => true,
                'message' => 'Pilihan saus berhasil diperbarui'
            ]);
        } elseif ($field === 'notes') {
            // Update notes
            $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
            $_SESSION['cart'][$index]['notes'] = $notes;
            
            echo json_encode([
                'success' => true,
                'message' => 'Catatan berhasil diperbarui'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Field tidak valid'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Item tidak ditemukan'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak valid'
    ]);
}
