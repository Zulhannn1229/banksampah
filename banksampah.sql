-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2025 at 03:46 AM
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
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$9QveSMUt/X27q6rgW4Lt4.QtICj3pouRqiAYM5LKMNzvp9gJ3G2QK');

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id_berita` int(10) UNSIGNED NOT NULL,
  `judul` varchar(255) NOT NULL,
  `isi` text NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `tanggal` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `berita`
--

INSERT INTO `berita` (`id_berita`, `judul`, `isi`, `gambar`, `tanggal`) VALUES
(1, 'Banjir Parah Landa Mataram, Ratusan Warga Terpaksa Mengungsi', 'Hujan deras yang terjadi selama beberapa hari terakhir menyebabkan banjir parah di sejumlah wilayah di Mataram. Ketinggian air mencapai hingga 1 meter di beberapa permukiman, memaksa ratusan warga mengungsi dari rumah mereka. Pihak berwenang setempat telah menyediakan tempat penampungan darurat dan menyalurkan bantuan bagi warga terdampak, sementara upaya penyedotan air terus dilakukan untuk menurunkan genangan.', '1746995783-68210a47a8c65.jpg', '2025-05-11 00:00:00'),
(4, 'Banjir Parah Landa Mataram, Ratusan Warga Terpaksa Mengungsi', 'Hujan deras yang mengguyur Kota Mataram sejak dini hari mengakibatkan banjir parah di sejumlah wilayah, memaksa ratusan warga mengungsi ke tempat yang lebih aman. Genangan air setinggi hingga satu meter merendam permukiman warga di Kecamatan Ampenan, Selaparang, dan Cakranegara, serta melumpuhkan aktivitas sehari-hari. Pemerintah setempat telah mendirikan posko darurat dan dapur umum untuk menampung para korban banjir, sementara petugas gabungan dari BPBD, TNI, dan relawan terus melakukan evakuasi dan pendistribusian bantuan. Warga mengeluhkan kurangnya sistem drainase yang memadai sebagai salah satu penyebab utama banjir yang rutin terjadi setiap musim hujan.', '1747057912-6821fcf8bbac7.jpg', '2025-05-12 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `penjemputan`
--

CREATE TABLE `penjemputan` (
  `id_penjemputan` int(10) UNSIGNED NOT NULL,
  `id_user` int(10) UNSIGNED NOT NULL,
  `tanggal` datetime NOT NULL,
  `status` enum('menunggu','diterima','ditolak') DEFAULT 'menunggu',
  `id_petugas` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjemputan`
--

INSERT INTO `penjemputan` (`id_penjemputan`, `id_user`, `tanggal`, `status`, `id_petugas`) VALUES
(21, 13, '2025-05-13 00:00:00', 'diterima', 16),
(22, 13, '2025-05-13 00:00:00', 'diterima', 16),
(24, 20, '2025-05-14 00:00:00', 'diterima', 16),
(25, 20, '2025-05-14 16:51:40', 'diterima', 16),
(26, 20, '2025-05-14 16:53:46', 'diterima', 16);

-- --------------------------------------------------------

--
-- Table structure for table `petugas`
--

CREATE TABLE `petugas` (
  `id_petugas` int(10) UNSIGNED NOT NULL,
  `nama_petugas` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `petugas`
--

INSERT INTO `petugas` (`id_petugas`, `nama_petugas`, `username`, `password`, `no_hp`) VALUES
(16, 'Petugas1', 'petugas1', '$2y$10$ElypDAcISBN1s77ZNtiX8.28SFtnNZoKblSsjX1mXFrZFwhTkRzcK', '081234567890'),
(19, 'Petugas2', 'petugas2', '$2y$10$4Z428lVsEhUVW5ryBuKh1.CE1HP54qsj/kzoIMzBe.GZFaoDR1vI2', '081234567890');

-- --------------------------------------------------------

--
-- Table structure for table `sampah`
--

CREATE TABLE `sampah` (
  `id_sampah` int(10) UNSIGNED NOT NULL,
  `nama_sampah` varchar(100) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sampah`
--

INSERT INTO `sampah` (`id_sampah`, `nama_sampah`, `harga`, `gambar`) VALUES
(6, 'Botol Plastik', 4000.00, '68245d66ebdff_erik-mclean-GjCx5KhulZI-unsplash.jpg'),
(8, 'Koran Bekas', 3000.00, '682201c0ddd84_Kertas.jpg'),
(9, 'Plastik', 2000.00, '68245ecb37b26_Plastik.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(10) UNSIGNED NOT NULL,
  `id_petugas` int(10) UNSIGNED NOT NULL,
  `id_user` int(10) UNSIGNED NOT NULL,
  `id_sampah` int(10) UNSIGNED NOT NULL,
  `nama_sampah` varchar(255) NOT NULL,
  `jumlah` decimal(10,2) DEFAULT NULL,
  `harga` int(11) NOT NULL,
  `tanggal` datetime DEFAULT NULL,
  `status` enum('menunggu','diterima','ditolak') NOT NULL DEFAULT 'menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_petugas`, `id_user`, `id_sampah`, `nama_sampah`, `jumlah`, `harga`, `tanggal`, `status`) VALUES
(8, 16, 13, 6, 'Botol Plastic', 3.00, 4000, '2025-05-12 22:31:12', 'diterima'),
(9, 16, 13, 8, 'Koran Bekas', 2.00, 3000, '2025-05-12 22:35:44', 'diterima'),
(10, 16, 13, 6, 'Botol Plastic', 1.00, 4000, '2025-05-12 22:43:40', 'diterima'),
(11, 16, 13, 8, 'Koran Bekas', 7.00, 3000, '2025-05-13 00:10:44', 'diterima'),
(13, 16, 20, 8, 'Koran Bekas', 25.00, 3000, '2025-05-14 16:12:30', 'diterima'),
(14, 16, 20, 8, 'Koran Bekas', 2.00, 3000, '2025-05-14 16:22:25', 'ditolak'),
(15, 16, 20, 8, 'Koran Bekas', 2.00, 3000, '2025-05-14 16:22:45', 'ditolak'),
(16, 16, 20, 6, 'Botol Plastic', 1.00, 4000, '2025-05-14 16:28:04', 'ditolak'),
(17, 16, 20, 6, 'Botol Plastic', 1.00, 4000, '2025-05-14 16:28:08', 'ditolak'),
(18, 16, 20, 8, 'Koran Bekas', 1.00, 3000, '2025-05-14 16:28:17', 'ditolak'),
(19, 16, 20, 8, 'Koran Bekas', 1.00, 3000, '2025-05-14 16:28:40', 'ditolak'),
(20, 16, 20, 6, 'Botol Plastic', 2.00, 4000, '2025-05-14 16:28:51', 'ditolak'),
(21, 16, 20, 6, 'Botol Plastic', 2.00, 4000, '2025-05-14 16:29:00', 'ditolak'),
(22, 16, 20, 6, 'Botol Plastic', 2.20, 4000, '2025-05-14 16:30:27', 'ditolak'),
(23, 16, 20, 6, 'Botol Plastic', 12.30, 4000, '2025-05-14 16:30:41', 'ditolak'),
(24, 16, 20, 8, 'Koran Bekas', 3.11, 3000, '2025-05-14 16:30:56', 'diterima'),
(25, 16, 20, 8, 'Koran Bekas', 2.50, 3000, '2025-05-14 16:37:57', 'diterima'),
(26, 16, 20, 6, 'Botol Plastik', 2.90, 4000, '2025-05-14 17:34:04', 'diterima'),
(27, 16, 20, 6, 'Botol Plastik', 2.90, 4000, '2025-05-14 17:34:42', 'diterima'),
(28, 19, 20, 8, 'Koran Bekas', 10.50, 3000, '2025-05-14 18:15:16', 'diterima');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(10) UNSIGNED NOT NULL,
  `nama_user` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama_user`, `username`, `password`, `no_hp`, `email`, `alamat`) VALUES
(13, 'Apriesna 1229', 'zulhan1229', '$2y$10$By9KjWqjtJpsCPkld/E5j.wLDbXUhkMv1FBVt11aIReDAqkdPhbS.', '081243834669', 'apriesnazulhan1250@gmail.com', 'Mataram Nusa Tenggara Barat'),
(20, 'Ridho Aidil', 'ridho1229', '$2y$10$XbxeeZ9pIpDbm4c90c7lVuXINLNSG35/rju0L4f/P0LXrtr7QBF2S', '081243834669', 'ridho1250@gmail.com', 'Mataram Nusa Tenggara Barat');

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
  MODIFY `id_berita` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `penjemputan`
--
ALTER TABLE `penjemputan`
  MODIFY `id_penjemputan` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `petugas`
--
ALTER TABLE `petugas`
  MODIFY `id_petugas` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `sampah`
--
ALTER TABLE `sampah`
  MODIFY `id_sampah` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

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
