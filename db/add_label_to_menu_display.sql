-- Menambahkan kolom label ke tabel menu_display
-- Jalankan file ini jika tabel menu_display sudah ada sebelumnya

ALTER TABLE `menu_display` 
ADD COLUMN `label` varchar(50) DEFAULT NULL COMMENT 'best_seller, favorit, atau NULL' 
AFTER `urutan`;

-- Catatan: Jika kolom label sudah ada, query ini akan error. Itu normal, abaikan saja.
