-- Menambahkan kolom catatan dan level_pedas ke tabel item_pesanan
-- Jalankan query ini di database Anda

ALTER TABLE item_pesanan 
ADD COLUMN catatan TEXT NULL AFTER subtotal,
ADD COLUMN level_pedas VARCHAR(100) NULL AFTER catatan;

-- Kolom level_pedas sekarang digunakan untuk menyimpan pilihan saus
-- Format: 'pedas,manis' atau 'tidak-bersaus' (comma separated untuk multiple choice)
-- Pilihan yang tersedia: 'tidak-bersaus', 'pedas', 'manis'

-- Untuk memverifikasi perubahan:
-- DESCRIBE item_pesanan;
