-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2025 at 04:10 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `banksampah`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$9QveSMUt/X27q6rgW4Lt4.QtICj3pouRqiAYM5LKMNzvp9gJ3G2QK', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id_berita` int(10) UNSIGNED NOT NULL,
  `judul` varchar(150) DEFAULT NULL,
  `isi` text NOT NULL,
  `gambar` varchar(150) DEFAULT NULL,
  `tanggal` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `berita`
--

INSERT INTO `berita` (`id_berita`, `judul`, `isi`, `gambar`, `tanggal`) VALUES
(1, 'Banjir Parah Landa Mataram, Ratusan Warga Terpaksa Mengungsi', 'Hujan deras yang terjadi selama beberapa hari terakhir menyebabkan banjir parah di sejumlah wilayah di Mataram. Ketinggian air mencapai hingga 1 meter di beberapa permukiman, memaksa ratusan warga mengungsi dari rumah mereka. Pihak berwenang setempat telah menyediakan tempat penampungan darurat dan menyalurkan bantuan bagi warga terdampak, sementara upaya penyedotan air terus dilakukan untuk menurunkan genangan.', '1746995783-68210a47a8c65.jpg', '2025-05-11 00:00:00'),
(4, 'Banjir Parah Landa Mataram, Ratusan Warga Terpaksa Mengungsi', 'Hujan deras yang mengguyur Kota Mataram sejak dini hari mengakibatkan banjir parah di sejumlah wilayah, memaksa ratusan warga mengungsi ke tempat yang lebih aman. Genangan air setinggi hingga satu meter merendam permukiman warga di Kecamatan Ampenan, Selaparang, dan Cakranegara, serta melumpuhkan aktivitas sehari-hari. Pemerintah setempat telah mendirikan posko darurat dan dapur umum untuk menampung para korban banjir, sementara petugas gabungan dari BPBD, TNI, dan relawan terus melakukan evakuasi dan pendistribusian bantuan. Warga mengeluhkan kurangnya sistem drainase yang memadai sebagai salah satu penyebab utama banjir yang rutin terjadi setiap musim hujan.', '1747057912-6821fcf8bbac7.jpg', '2025-05-12 00:00:00'),
(5, 'Bank Sampah Kumala Bantu Anak Jalanan Hidup Lebih Baik di Jakarta Utara', 'Bank Sampah Induk Kumala di Tanjung Priok, Jakarta Utara, telah menjadi pelopor dalam mengubah kehidupan anak-anak jalanan melalui pengelolaan sampah. Dengan melibatkan mereka dalam kegiatan memilah dan mendaur ulang sampah, bank sampah ini tidak hanya membantu mengurangi limbah, tetapi juga memberikan pelatihan keterampilan dan penghasilan tambahan bagi anak-anak tersebut.\r\n\r\nProgram ini bertujuan untuk memberdayakan anak-anak jalanan agar memiliki masa depan yang lebih cerah. Melalui kegiatan ini, mereka belajar tentang pentingnya menjaga kebersihan lingkungan sekaligus memperoleh keterampilan yang dapat digunakan untuk mencari nafkah. Inisiatif ini mendapat dukungan dari berbagai pihak, termasuk pemerintah daerah dan organisasi sosial, yang melihat potensi besar dalam pendekatan berbasis komunitas untuk mengatasi masalah sosial dan lingkungan secara bersamaan.\r\n', '1747968666-682fe29aa6f3f.jpeg', '2025-05-23 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id_feedback` int(11) NOT NULL,
  `id_user` int(10) UNSIGNED NOT NULL,
  `judul` varchar(150) DEFAULT NULL,
  `pesan` text NOT NULL,
  `kategori` enum('saran','keluhan','pujian','lainnya') DEFAULT 'saran',
  `status` enum('baru','dibaca','diproses','selesai') DEFAULT 'baru',
  `tanggal_kirim` datetime DEFAULT current_timestamp(),
  `tanggal_baca` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id_feedback`, `id_user`, `judul`, `pesan`, `kategori`, `status`, `tanggal_kirim`, `tanggal_baca`) VALUES
(1, 31, 'Tambahkan Payment Gateway Deposit & Withdraw', 'Saya menyarankan agar sistem bank sampah ini dilengkapi dengan fitur payment gateway untuk mempermudah proses deposit dan withdraw saldo secara online. Dengan adanya fitur ini, pengguna bisa langsung mencairkan saldo hasil penukaran sampah ke rekening pribadi atau e-wallet (seperti Dana, OVO, atau GoPay) tanpa harus datang langsung ke lokasi. Selain itu, pengguna juga bisa melakukan deposit saldo untuk keperluan pembelian barang ramah lingkungan dari aplikasi bila nanti tersedia.\r\n\r\nFitur ini akan sangat membantu dalam mempercepat transaksi dan memberikan pengalaman yang lebih modern dan fleksibel kepada pengguna.', 'saran', 'dibaca', '2025-06-13 12:46:53', '2025-06-14 06:18:38'),
(2, 34, 'Bank Sampah Kumala Bantu Anak Jalanan Hidup Lebih Baik di Jakarta Utara', 'Saya sangat senang dengan website ini karena membantu banyak anak di jalanan', 'pujian', 'dibaca', '2025-06-20 09:52:40', '2025-06-20 09:53:05');

-- --------------------------------------------------------

--
-- Table structure for table `penjemputan`
--

CREATE TABLE `penjemputan` (
  `id_penjemputan` int(10) UNSIGNED NOT NULL,
  `id_user` int(10) UNSIGNED NOT NULL,
  `tanggal` datetime NOT NULL,
  `berat_perkiraan` decimal(6,2) DEFAULT NULL,
  `status` enum('menunggu','diterima','ditolak') DEFAULT 'menunggu',
  `id_petugas` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjemputan`
--

INSERT INTO `penjemputan` (`id_penjemputan`, `id_user`, `tanggal`, `berat_perkiraan`, `status`, `id_petugas`) VALUES
(28, 31, '2025-05-23 00:39:56', 0.00, 'diterima', 19),
(29, 32, '2025-05-23 00:41:56', 0.00, 'diterima', 19),
(30, 32, '2025-05-23 01:08:01', 0.00, 'diterima', 19),
(31, 13, '2025-05-23 01:52:42', 0.00, 'diterima', 25),
(32, 32, '2025-05-23 01:55:47', 0.00, 'diterima', 25),
(33, 13, '2025-05-23 08:00:56', 0.00, 'diterima', 19),
(34, 34, '2025-05-23 10:45:21', 0.00, 'diterima', 19),
(35, 31, '2025-05-23 11:12:37', 0.00, 'ditolak', 19),
(36, 31, '2025-05-23 11:12:40', 0.00, 'ditolak', 19),
(37, 31, '2025-05-23 11:12:42', 0.00, 'ditolak', 19),
(38, 31, '2025-06-13 09:38:04', 3.40, 'diterima', 19),
(39, 31, '2025-06-13 09:47:18', 3.40, 'diterima', 19),
(40, 34, '2025-06-20 09:53:52', 30.00, 'diterima', 19);

-- --------------------------------------------------------

--
-- Table structure for table `petugas`
--

CREATE TABLE `petugas` (
  `id_petugas` int(10) UNSIGNED NOT NULL,
  `nama_petugas` varchar(75) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `role` enum('petugas') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `petugas`
--

INSERT INTO `petugas` (`id_petugas`, `nama_petugas`, `username`, `password`, `no_hp`, `role`) VALUES
(19, 'Petugas1', 'petugas1', '$2y$10$BBweuRPoD186AATKTplxyu06HBTjzZUlYopGld.RKDInvXVRcTH9W', '081234567890', 'petugas'),
(25, 'Petugas2', 'petugas2', '$2y$10$iBFRXKr8UiVxzN1up1kcIuQKM13Bo.YaFdVUBhvDrZeCoWda9bJA.', '081243834669', 'petugas');

-- --------------------------------------------------------

--
-- Table structure for table `sampah`
--

CREATE TABLE `sampah` (
  `id_sampah` int(10) UNSIGNED NOT NULL,
  `nama_sampah` varchar(75) DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `gambar` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sampah`
--

INSERT INTO `sampah` (`id_sampah`, `nama_sampah`, `harga`, `gambar`) VALUES
(6, 'Botol Plastik', 4000.00, '68245d66ebdff_erik-mclean-GjCx5KhulZI-unsplash.jpg'),
(8, 'Koran Bekas', 3000.00, '682201c0ddd84_Kertas.jpg'),
(9, 'Plastik', 2000.00, '68245ecb37b26_Plastik.jpg'),
(15, 'Besi', 5000.00, '682ef653e69fc_Besi.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(10) UNSIGNED NOT NULL,
  `id_petugas` int(10) UNSIGNED NOT NULL,
  `id_user` int(10) UNSIGNED NOT NULL,
  `id_sampah` int(10) UNSIGNED NOT NULL,
  `nama_sampah` varchar(100) DEFAULT NULL,
  `jumlah` decimal(10,2) DEFAULT NULL,
  `harga` decimal(10,2) DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `status` enum('menunggu','diterima','ditolak') NOT NULL DEFAULT 'menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_petugas`, `id_user`, `id_sampah`, `nama_sampah`, `jumlah`, `harga`, `tanggal`, `status`) VALUES
(44, 19, 13, 15, NULL, 2.00, 5000.00, '2025-05-22 17:38:38', 'diterima'),
(45, 19, 13, 15, NULL, 2.00, 5000.00, '2025-05-22 17:45:38', 'diterima'),
(46, 19, 31, 6, NULL, 5.00, 4000.00, '2025-05-23 00:40:26', 'diterima'),
(47, 19, 32, 8, NULL, 5.00, 3000.00, '2025-05-23 01:03:53', 'diterima'),
(48, 19, 32, 9, NULL, 2.00, 2000.00, '2025-05-23 01:08:40', 'diterima'),
(49, 25, 13, 15, NULL, 5.00, 5000.00, '2025-05-23 01:10:22', 'diterima'),
(50, 19, 13, 9, NULL, 10.00, 2000.00, '2025-05-23 01:56:43', 'diterima'),
(51, 19, 13, 9, NULL, 5.00, 2000.00, '2025-05-23 08:03:15', 'diterima'),
(52, 19, 31, 8, NULL, 3.00, 3000.00, '2025-05-23 10:47:38', 'diterima'),
(53, 19, 31, 15, NULL, 3.40, 5000.00, '2025-06-13 09:49:29', 'menunggu'),
(54, 19, 13, 6, 'Botol Plastik', 30.00, 4000.00, '2025-06-20 09:57:47', 'diterima');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(10) UNSIGNED NOT NULL,
  `nama_user` varchar(75) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `email` varchar(75) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `role` enum('user') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama_user`, `username`, `password`, `no_hp`, `email`, `alamat`, `role`) VALUES
(13, 'Apriesna Zulhan', 'zulhan', '$2y$10$cmzwALYephf3C/tsXmH5MefD5g.1ZQpwnLqJnGiE00VEibylE1pWq', '081243834669', 'apriesnazulhan1250@gmail.com', 'Mataram Nusa Tenggara Barat', 'user'),
(31, 'Ridho Aidil', 'ridho', '$2y$10$aDR/i0NJWyPKfx3iqfqSv./DxdtWyYqD9RcYd1iohDefCeHeVI/US', '081234567890', 'f1d02310100@student.unram.ac.id', 'Jalan Penjanggik', 'user'),
(32, 'Kahfi Yuda', 'kahfi', '$2y$10$UvsplUzL7csWxsC2F6ZMtunougNAWPSWtHvjtc5a5yC1C1JzwQnGK', '081243834669', 'Dia@gmail.com', 'Jalan Sukarara', 'user'),
(34, 'Apriesna Zulhan', 'bagas', '$2y$10$PRPB26VXIb/zX7Jnhvm1e.NbYNeZLjdZkKjF4gWqPMWaNyq1SQLnK', '081243834669', 'aku@gmail.com', 'Jalan Merdeka', 'user'),
(35, 'Jamal Musiala', 'jamal', '$2y$10$Zzw.aZcgc0YPC75f0u6xzeT48FCGu/6H.VXPaZwGQvaF98Qld3/42', '081234567890', 'jamalmusiala@gmail.com', 'Jalan Merdeka Raya\r\nKecamatan Sekarbela', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id_berita`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id_feedback`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `penjemputan`
--
ALTER TABLE `penjemputan`
  ADD PRIMARY KEY (`id_penjemputan`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `fk_id_petugas` (`id_petugas`);

--
-- Indexes for table `petugas`
--
ALTER TABLE `petugas`
  ADD PRIMARY KEY (`id_petugas`);

--
-- Indexes for table `sampah`
--
ALTER TABLE `sampah`
  ADD PRIMARY KEY (`id_sampah`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `transaksi_id_petugas_foreign` (`id_petugas`),
  ADD KEY `transaksi_id_user_foreign` (`id_user`),
  ADD KEY `transaksi_id_sampah_foreign` (`id_sampah`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `id_berita` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id_feedback` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `penjemputan`
--
ALTER TABLE `penjemputan`
  MODIFY `id_penjemputan` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `petugas`
--
ALTER TABLE `petugas`
  MODIFY `id_petugas` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `sampah`
--
ALTER TABLE `sampah`
  MODIFY `id_sampah` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `penjemputan`
--
ALTER TABLE `penjemputan`
  ADD CONSTRAINT `fk_id_petugas` FOREIGN KEY (`id_petugas`) REFERENCES `petugas` (`id_petugas`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `penjemputan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_id_petugas_foreign` FOREIGN KEY (`id_petugas`) REFERENCES `petugas` (`id_petugas`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_id_sampah_foreign` FOREIGN KEY (`id_sampah`) REFERENCES `sampah` (`id_sampah`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
