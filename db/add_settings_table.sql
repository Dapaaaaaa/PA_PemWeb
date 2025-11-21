-- Tabel Settings untuk menyimpan konfigurasi toko
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `setting_group` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `diperbarui_pada` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`) VALUES
-- Store Information
('store_name', 'OurStuffies', 'store'),
('store_email', 'info@ourstuffies.com', 'store'),
('store_phone', '+62 859-7490-6945', 'store'),
('store_whatsapp', '6285974906945', 'store'),
('store_address', 'Blk. A-B No.53b, Gn. Kelua, Kec. Samarinda Ulu, Kota Samarinda, Kalimantan Timur 75243', 'store'),
('store_city', 'Samarinda', 'store'),
('store_postal', '75243', 'store'),

-- Business Hours
('opening_time', '10:00', 'hours'),
('closing_time', '22:00', 'hours'),
('holiday_note', 'Buka setiap hari kecuali hari libur nasional', 'hours'),

-- Location
('map_latitude', '-0.4897', 'location'),
('map_longitude', '117.1436', 'location'),
('map_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.6607285388495!2d117.14139931475395!3d-0.4897068997544673!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df67f15e96c0c91%3A0x5e5e5e5e5e5e5e5e!2sGunung%20Kelua!5e0!3m2!1sen!2sid!4v1234567890123!5m2!1sen!2sid', 'location'),

-- Social Media
('social_instagram', 'https://instagram.com/ourstuffies', 'social'),
('social_facebook', 'https://facebook.com/ourstuffies', 'social'),
('social_twitter', '', 'social'),

-- Delivery
('delivery_fee', '10000', 'delivery'),
('free_delivery_min', '100000', 'delivery'),
('delivery_note', 'Pengiriman tersedia di seluruh Samarinda. Estimasi waktu: 30-45 menit.', 'delivery')

ON DUPLICATE KEY UPDATE 
setting_value = VALUES(setting_value);
