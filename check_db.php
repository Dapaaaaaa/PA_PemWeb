<?php
include 'koneksi.php';

$query = "SELECT id, nama, url_gambar FROM produk LIMIT 5";
$result = mysqli_query($conn, $query);

echo "=== ISI DATABASE KOLOM url_gambar ===\n\n";

while ($row = mysqli_fetch_assoc($result)) {
    echo "ID: " . $row['id'] . "\n";
    echo "Nama: " . $row['nama'] . "\n";
    echo "Path: " . $row['url_gambar'] . "\n";
    echo "---\n";
}
?>
