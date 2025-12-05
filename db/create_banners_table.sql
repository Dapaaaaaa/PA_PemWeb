-- Tabel untuk menyimpan banner slider
CREATE TABLE IF NOT EXISTS `banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(500) NOT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_link` varchar(500) DEFAULT NULL,
  `urutan` int(11) DEFAULT 0,
  `aktif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default banner
INSERT INTO `banners` (`title`, `description`, `image_url`, `button_text`, `button_link`, `urutan`, `aktif`) VALUES
('Burger Ayam Premium', 'Nikmati rasa burger yang segar, juicy, dan lezat yang dibuat dengan bahan premium dan penuh cinta.', 'assets/img/product/hero-burger.png', 'Pesan Sekarang', 'menu.php', 1, 1);
