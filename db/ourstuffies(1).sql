-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 21, 2025 at 10:41 AM
-- Server version: 8.0.30
-- PHP Version: 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ourstuffies`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_generate_nomor_pesanan` (OUT `new_nomor` VARCHAR(20))   BEGIN
    DECLARE today VARCHAR(8);
    DECLARE seq INT;

    SET today = DATE_FORMAT(NOW(), '%Y%m%d');

    SELECT COALESCE(MAX(CAST(SUBSTRING(nomor_pesanan, 10) AS UNSIGNED)), 0) + 1
    INTO seq
    FROM pesanan
    WHERE nomor_pesanan LIKE CONCAT('ORD', today, '%');

    SET new_nomor = CONCAT('ORD', today, LPAD(seq, 4, '0'));
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_process_checkout` (IN `p_keranjang_id` INT, IN `p_pelanggan_id` INT, IN `p_alamat_id` INT, IN `p_ongkir` DECIMAL(10,2), IN `p_catatan` TEXT, OUT `p_pesanan_id` INT, OUT `p_nomor_pesanan` VARCHAR(20))   BEGIN
    DECLARE v_total DECIMAL(12,2);

    START TRANSACTION;

    CALL sp_generate_nomor_pesanan(p_nomor_pesanan);

    INSERT INTO pesanan (nomor_pesanan, pelanggan_id, alamat_id, ongkir, catatan, total)
    VALUES (p_nomor_pesanan, p_pelanggan_id, p_alamat_id, p_ongkir, p_catatan, 0);

    SET p_pesanan_id = LAST_INSERT_ID();

    INSERT INTO item_pesanan (pesanan_id, produk_id, qty, harga_satuan, subtotal, catatan)
    SELECT p_pesanan_id, ik.produk_id, ik.qty, ik.harga_saat_tambah, (ik.qty*ik.harga_saat_tambah), ik.catatan
    FROM item_keranjang ik
    WHERE ik.keranjang_id = p_keranjang_id;

    SELECT COALESCE(SUM(subtotal),0) INTO v_total
    FROM item_pesanan
    WHERE pesanan_id = p_pesanan_id;

    UPDATE pesanan SET total = v_total + p_ongkir WHERE id = p_pesanan_id;

    DELETE FROM item_keranjang WHERE keranjang_id = p_keranjang_id;

    COMMIT;
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_format_rupiah` (`amount` DECIMAL(12,2)) RETURNS VARCHAR(50) CHARSET utf8mb4 DETERMINISTIC BEGIN
    RETURN CONCAT('Rp ', FORMAT(amount, 0, 'id_ID'));
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `nama`, `email`, `password`, `dibuat_pada`, `diperbarui_pada`) VALUES
(1, 'Admin OurStuffies', 'admin@ourstuffies.com', 'admin123', '2025-11-18 16:23:10', '2025-11-18 16:23:10');

-- --------------------------------------------------------

--
-- Table structure for table `alamat`
--

CREATE TABLE `alamat` (
  `id` int NOT NULL,
  `pelanggan_id` int NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jalan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `kota` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_pos` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `alamat`
--

INSERT INTO `alamat` (`id`, `pelanggan_id`, `label`, `jalan`, `kota`, `kode_pos`, `catatan`, `dibuat_pada`) VALUES
(1, 1, 'Rumah', 'Jl. Merdeka No. 123', 'Jakarta', '12345', NULL, '2025-11-11 00:29:35'),
(2, 2, 'Kantor', 'Jl. Sudirman No. 456', 'Jakarta', '12346', NULL, '2025-11-11 00:29:35');

-- --------------------------------------------------------

--
-- Table structure for table `item_keranjang`
--

CREATE TABLE `item_keranjang` (
  `id` int NOT NULL,
  `keranjang_id` int NOT NULL,
  `produk_id` int NOT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `harga_saat_tambah` decimal(10,2) NOT NULL,
  `catatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_pesanan`
--

CREATE TABLE `item_pesanan` (
  `id` int NOT NULL,
  `pesanan_id` int NOT NULL,
  `produk_id` int NOT NULL,
  `qty` int NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `catatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama`, `slug`, `deskripsi`, `dibuat_pada`, `diperbarui_pada`) VALUES
(1, 'Kebab', 'kebab', 'Aneka kebab dengan isian pilihan', '2025-11-11 00:29:35', '2025-11-11 00:29:35'),
(2, 'Burger', 'burger', 'Burger segar dengan daging berkualitas', '2025-11-11 00:29:35', '2025-11-11 00:29:35'),
(3, 'Minuman', 'minuman', 'Berbagai minuman segar', '2025-11-11 00:29:35', '2025-11-11 00:29:35'),
(4, 'Snack', 'snack', 'Camilan pelengkap', '2025-11-11 00:29:35', '2025-11-11 00:29:35');

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `id` int NOT NULL,
  `pelanggan_id` int DEFAULT NULL,
  `session_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_wa`
--

CREATE TABLE `log_wa` (
  `id` int NOT NULL,
  `pesanan_id` int DEFAULT NULL,
  `telepon_tujuan` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pesan` text COLLATE utf8mb4_unicode_ci,
  `wa_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dikirim_pada` datetime DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` int NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telepon` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id`, `nama`, `email`, `telepon`, `dibuat_pada`) VALUES
(1, 'John Doe', 'john@example.com', '628123456789', '2025-11-11 00:29:35'),
(2, 'Jane Smith', 'jane@example.com', '628987654321', '2025-11-11 00:29:35');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int NOT NULL,
  `pesanan_id` int NOT NULL,
  `metode` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT 'unpaid',
  `jumlah` decimal(12,2) NOT NULL,
  `dibayar_pada` datetime DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id` int NOT NULL,
  `nomor_pesanan` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pelanggan_id` int NOT NULL,
  `alamat_id` int DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `ongkir` decimal(10,2) DEFAULT '0.00',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int NOT NULL,
  `kategori_id` int DEFAULT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `harga` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stok` int DEFAULT NULL,
  `url_gambar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT '1',
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `kategori_id`, `nama`, `slug`, `deskripsi`, `harga`, `stok`, `url_gambar`, `aktif`, `dibuat_pada`, `diperbarui_pada`) VALUES
(1, 1, 'Kebab Ayam', 'kebab-ayam', 'Kebab dengan isian ayam dan sayuran segar', 15000.00, 50, 'assets\\img\\burger1.png', 1, '2025-11-11 00:29:35', '2025-11-11 21:32:13'),
(2, 1, 'Kebab Sapi', 'kebab-sapi', 'Kebab dengan isian daging sapi premium', 20000.00, 30, NULL, 1, '2025-11-11 00:29:35', '2025-11-11 00:29:35'),
(3, 2, 'Burger Beef', 'burger-beef', 'Burger dengan patty daging sapi 100%', 25000.00, 40, NULL, 1, '2025-11-11 00:29:35', '2025-11-11 00:29:35'),
(4, 2, 'Burger Chicken', 'burger-chicken', 'Burger dengan chicken crispy', 20000.00, 45, NULL, 1, '2025-11-11 00:29:35', '2025-11-11 00:29:35'),
(5, 3, 'Es Teh Manis', 'es-teh-manis', 'Es teh manis segar', 5000.00, 100, NULL, 1, '2025-11-11 00:29:35', '2025-11-11 00:29:35'),
(6, 3, 'Jus Jeruk', 'jus-jeruk', 'Jus jeruk segar tanpa gula tambahan', 10000.00, 50, NULL, 1, '2025-11-11 00:29:35', '2025-11-11 00:29:35'),
(7, 4, 'French Fries', 'french-fries', 'Kentang goreng crispy', 12000.00, 60, NULL, 1, '2025-11-11 00:29:35', '2025-11-11 00:29:35');

-- --------------------------------------------------------

--
-- Table structure for table `struk`
--

CREATE TABLE `struk` (
  `id` int NOT NULL,
  `pesanan_id` int NOT NULL,
  `nomor_struk` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `konten` text COLLATE utf8mb4_unicode_ci,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_item_keranjang`
-- (See below for the actual view)
--
CREATE TABLE `v_item_keranjang` (
`catatan` varchar(255)
,`harga_saat_tambah` decimal(10,2)
,`item_keranjang_id` int
,`keranjang_id` int
,`nama_produk` varchar(150)
,`pelanggan_id` int
,`produk_id` int
,`qty` int
,`session_id` varchar(128)
,`subtotal` decimal(20,2)
,`url_gambar` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_item_pesanan`
-- (See below for the actual view)
--
CREATE TABLE `v_item_pesanan` (
`catatan_item` varchar(255)
,`harga_satuan` decimal(10,2)
,`jalan` text
,`kode_pos` varchar(20)
,`kota` varchar(100)
,`nama_pelanggan` varchar(150)
,`nama_produk` varchar(150)
,`nomor_pesanan` varchar(20)
,`ongkir` decimal(10,2)
,`pesanan_id` int
,`produk_id` int
,`qty` int
,`status` varchar(30)
,`subtotal` decimal(12,2)
,`tanggal_pesanan` datetime
,`telepon_pelanggan` varchar(32)
,`total` decimal(12,2)
);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `alamat`
--
ALTER TABLE `alamat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pelanggan` (`pelanggan_id`);

--
-- Indexes for table `item_keranjang`
--
ALTER TABLE `item_keranjang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_keranjang` (`keranjang_id`),
  ADD KEY `idx_produk` (`produk_id`);

--
-- Indexes for table `item_pesanan`
--
ALTER TABLE `item_pesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pesanan` (`pesanan_id`),
  ADD KEY `idx_produk` (`produk_id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`);

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cart_session` (`pelanggan_id`,`session_id`),
  ADD KEY `idx_session` (`session_id`);

--
-- Indexes for table `log_wa`
--
ALTER TABLE `log_wa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pesanan` (`pesanan_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_telepon` (`telepon`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pesanan` (`pesanan_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_pesanan` (`nomor_pesanan`),
  ADD KEY `alamat_id` (`alamat_id`),
  ADD KEY `idx_nomor` (`nomor_pesanan`),
  ADD KEY `idx_pelanggan` (`pelanggan_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_dibuat` (`dibuat_pada`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_kategori` (`kategori_id`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_aktif` (`aktif`);

--
-- Indexes for table `struk`
--
ALTER TABLE `struk`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pesanan_id` (`pesanan_id`),
  ADD UNIQUE KEY `nomor_struk` (`nomor_struk`),
  ADD KEY `idx_nomor_struk` (`nomor_struk`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `alamat`
--
ALTER TABLE `alamat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `item_keranjang`
--
ALTER TABLE `item_keranjang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_pesanan`
--
ALTER TABLE `item_pesanan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_wa`
--
ALTER TABLE `log_wa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `struk`
--
ALTER TABLE `struk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Structure for view `v_item_keranjang`
--
DROP TABLE IF EXISTS `v_item_keranjang`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_item_keranjang`  AS SELECT `k`.`id` AS `keranjang_id`, `k`.`pelanggan_id` AS `pelanggan_id`, `k`.`session_id` AS `session_id`, `ik`.`id` AS `item_keranjang_id`, `ik`.`produk_id` AS `produk_id`, `p`.`nama` AS `nama_produk`, `p`.`url_gambar` AS `url_gambar`, `ik`.`qty` AS `qty`, `ik`.`harga_saat_tambah` AS `harga_saat_tambah`, (`ik`.`qty` * `ik`.`harga_saat_tambah`) AS `subtotal`, `ik`.`catatan` AS `catatan` FROM ((`keranjang` `k` join `item_keranjang` `ik` on((`k`.`id` = `ik`.`keranjang_id`))) join `produk` `p` on((`ik`.`produk_id` = `p`.`id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `v_item_pesanan`
--
DROP TABLE IF EXISTS `v_item_pesanan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_item_pesanan`  AS SELECT `ps`.`id` AS `pesanan_id`, `ps`.`nomor_pesanan` AS `nomor_pesanan`, `ps`.`status` AS `status`, `ps`.`total` AS `total`, `ps`.`ongkir` AS `ongkir`, `ps`.`dibuat_pada` AS `tanggal_pesanan`, `pl`.`nama` AS `nama_pelanggan`, `pl`.`telepon` AS `telepon_pelanggan`, `a`.`jalan` AS `jalan`, `a`.`kota` AS `kota`, `a`.`kode_pos` AS `kode_pos`, `ip`.`produk_id` AS `produk_id`, `pr`.`nama` AS `nama_produk`, `ip`.`qty` AS `qty`, `ip`.`harga_satuan` AS `harga_satuan`, `ip`.`subtotal` AS `subtotal`, `ip`.`catatan` AS `catatan_item` FROM ((((`pesanan` `ps` join `pelanggan` `pl` on((`ps`.`pelanggan_id` = `pl`.`id`))) left join `alamat` `a` on((`ps`.`alamat_id` = `a`.`id`))) join `item_pesanan` `ip` on((`ps`.`id` = `ip`.`pesanan_id`))) join `produk` `pr` on((`ip`.`produk_id` = `pr`.`id`))) ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alamat`
--
ALTER TABLE `alamat`
  ADD CONSTRAINT `alamat_ibfk_1` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_keranjang`
--
ALTER TABLE `item_keranjang`
  ADD CONSTRAINT `item_keranjang_ibfk_1` FOREIGN KEY (`keranjang_id`) REFERENCES `keranjang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_keranjang_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Constraints for table `item_pesanan`
--
ALTER TABLE `item_pesanan`
  ADD CONSTRAINT `item_pesanan_ibfk_1` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_pesanan_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Constraints for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `keranjang_ibfk_1` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `log_wa`
--
ALTER TABLE `log_wa`
  ADD CONSTRAINT `log_wa_ibfk_1` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`);

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`),
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`alamat_id`) REFERENCES `alamat` (`id`);

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `struk`
--
ALTER TABLE `struk`
  ADD CONSTRAINT `struk_ibfk_1` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
