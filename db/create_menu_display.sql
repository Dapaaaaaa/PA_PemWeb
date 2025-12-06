-- Tabel untuk menyimpan produk yang ditampilkan di section "Menu Kami" di homepage
CREATE TABLE IF NOT EXISTS `menu_display` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produk_id` int(11) NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `label` varchar(50) DEFAULT NULL COMMENT 'best_seller, favorit, atau NULL',
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `dibuat_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `produk_id` (`produk_id`),
  KEY `urutan` (`urutan`),
  KEY `aktif` (`aktif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tambahkan foreign key constraint
ALTER TABLE `menu_display`
  ADD CONSTRAINT `fk_menu_display_produk` 
  FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) 
  ON DELETE CASCADE ON UPDATE CASCADE;

-- Insert data default (ambil 1 produk dari setiap kategori)
INSERT INTO `menu_display` (`produk_id`, `urutan`, `aktif`)
SELECT p.id, (@row_number:=@row_number + 1) AS urutan, 1
FROM (
  SELECT MIN(id) as id
  FROM produk
  WHERE aktif = 1
  GROUP BY kategori_id
  ORDER BY kategori_id ASC
) p, (SELECT @row_number:=0) r
LIMIT 6;
