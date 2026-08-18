-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 13, 2026 at 02:04 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restorant`
--

-- --------------------------------------------------------

--
-- Table structure for table `meja`
--

CREATE TABLE `meja` (
  `id_meja` int(11) NOT NULL,
  `nomor_meja` varchar(10) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  `lokasi` varchar(50) DEFAULT 'Indoor',
  `status` enum('Tersedia','Terisi','Maintenance') DEFAULT 'Tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meja`
--

INSERT INTO `meja` (`id_meja`, `nomor_meja`, `kapasitas`, `lokasi`, `status`) VALUES
(1, 'M-01', 2, 'Indoor', 'Tersedia'),
(2, 'M-02', 4, 'Indoor', 'Tersedia'),
(3, 'M-03', 4, 'Outdoor', 'Tersedia'),
(4, 'M-04', 6, 'VIP Room', 'Tersedia'),
(5, 'M-05', 8, 'VIP Room', 'Tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `reservasi`
--

CREATE TABLE `reservasi` (
  `id_reservasi` int(11) NOT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `id_meja` int(11) NOT NULL,
  `waktu_reservasi` datetime NOT NULL,
  `jumlah_tamu` int(11) NOT NULL,
  `status_booking` enum('Pending','Dikonfirmasi','Selesai','Batal') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservasi`
--

INSERT INTO `reservasi` (`id_reservasi`, `nama_pemesan`, `no_hp`, `email`, `id_meja`, `waktu_reservasi`, `jumlah_tamu`, `status_booking`, `created_at`) VALUES
(1, 'Davinn', '0838713342', 'vinnkenzi@gmail.com', 2, '2026-08-12 20:25:00', 3, 'Dikonfirmasi', '2026-08-12 12:25:09'),
(2, 'Davinn', '0838713342', 'vinnkenzi@gmail.com', 3, '2026-08-13 07:30:00', 3, 'Dikonfirmasi', '2026-08-12 23:30:39'),
(4, 'Hansfi', '0838713342', 'vinnkenzi@gmail.com', 5, '2026-08-13 07:38:00', 3, 'Pending', '2026-08-12 23:38:52'),
(5, 'Jovanimo', '0838713342', 'jovan@gmail.com', 4, '2026-08-13 07:46:00', 3, 'Dikonfirmasi', '2026-08-12 23:47:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `meja`
--
ALTER TABLE `meja`
  ADD PRIMARY KEY (`id_meja`),
  ADD UNIQUE KEY `nomor_meja` (`nomor_meja`);

--
-- Indexes for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD PRIMARY KEY (`id_reservasi`),
  ADD KEY `id_meja` (`id_meja`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `meja`
--
ALTER TABLE `meja`
  MODIFY `id_meja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id_reservasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD CONSTRAINT `reservasi_ibfk_1` FOREIGN KEY (`id_meja`) REFERENCES `meja` (`id_meja`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
