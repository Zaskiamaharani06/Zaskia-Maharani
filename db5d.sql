-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2026 at 02:26 AM
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
-- Database: `db5d`
--

-- --------------------------------------------------------

--
-- Table structure for table `galeri`
--

CREATE TABLE `galeri` (
  `id` int(6) UNSIGNED NOT NULL,
  `namafile` varchar(100) NOT NULL,
  `tglunggah` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `galeri`
--

INSERT INTO `galeri` (`id`, `namafile`, `tglunggah`) VALUES
(1, 'galeri/wp james.jpeg', '2026-10-21 07:02:05'),
(2, 'galeri/WhatsApp Image 2026-09-24 at 09.28.27.jpeg', '2026-10-21 07:14:49');

-- --------------------------------------------------------

--
-- Table structure for table `myguests`
--

CREATE TABLE `myguests` (
  `id` int(6) UNSIGNED NOT NULL,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `myguests`
--

INSERT INTO `myguests` (`id`, `firstname`, `lastname`, `email`, `reg_date`) VALUES
(15, 'Zaskia', 'Maharani', 'zaskiarani780@gmail.com', '2026-10-14 02:37:57'),
(16, 'zaskia', 'rani', 'zaskiarani780@gmail.com', '2026-10-14 02:38:16'),
(18, 'Zahria', 'Rahmadani', 'Zahria@google.com', '2026-10-14 02:44:07'),
(19, 'Tami', 'izzati', 'Tami@google.com', '2026-10-14 03:59:44');

-- --------------------------------------------------------

--
-- Table structure for table `nilai_mhsw`
--

CREATE TABLE `nilai_mhsw` (
  `nim` varchar(15) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `nilai_akhir` decimal(5,2) NOT NULL,
  `nilai_huruf` char(1) NOT NULL,
  `predikat` varchar(30) NOT NULL,
  `foto` varchar(100) DEFAULT NULL,
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `tgl_diubah` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nilai_mhsw`
--

INSERT INTO `nilai_mhsw` (`nim`, `nama`, `nilai_akhir`, `nilai_huruf`, `predikat`, `foto`, `reg_date`, `tgl_diubah`) VALUES
('3202415022', 'hyunjae', 65.00, 'C', 'Cukup', NULL, '2026-10-27 22:25:00', '2026-10-27 22:25:00'),
('3202415023', 'archen', 55.00, 'C', 'Cukup', 'assets/img/prod-5.jpg', '2026-10-27 22:22:45', '2026-10-27 22:23:28'),
('3202416022', 'Tami Izzatik', 90.00, 'A', 'Sangat Memuaskan', NULL, '2026-10-14 07:38:46', '2026-10-15 01:53:54'),
('3202416024', 'William', 35.00, 'E', 'Sangat Kurang', 'assets/img/prod-1.jpg', '2026-10-27 14:06:21', '2026-10-27 22:19:18'),
('3202416027', 'juyeon', 100.00, 'A', 'Sangat Memuaskan', 'assets/img/avatar5.png', '2026-10-27 22:37:05', '2026-10-27 22:37:05'),
('3202416029', 'Zahria Rahmadani', 80.00, 'B', 'Memuaskan', NULL, '2026-10-27 06:32:05', '2026-10-27 06:32:05'),
('3202416030', 'Mark Lee', 70.00, 'B', 'Memuaskan', NULL, '2026-10-27 06:32:50', '2026-10-27 06:32:50'),
('3202416120', 'Zaskia Maharani', 85.00, 'A', 'Sangat Memuaskan', NULL, '2026-10-14 07:53:16', '2026-10-14 07:53:16'),
('3202416121', 'luke', 65.00, 'C', 'Cukup', 'assets/img/user3-128x128.jpg', '2026-10-27 22:39:10', '2026-10-27 22:39:10'),
('3202416126', 'Karina', 45.00, 'D', 'Kurang', NULL, '2026-10-27 06:48:01', '2026-10-27 06:48:01'),
('3202416127', 'Giselle', 60.00, 'C', 'Cukup', NULL, '2026-10-27 06:49:29', '2026-10-27 06:49:29'),
('3203416125', 'James', 50.00, 'D', 'Kurang', NULL, '2026-10-27 06:47:13', '2026-10-27 06:47:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `myguests`
--
ALTER TABLE `myguests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nilai_mhsw`
--
ALTER TABLE `nilai_mhsw`
  ADD PRIMARY KEY (`nim`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `galeri`
--
ALTER TABLE `galeri`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `myguests`
--
ALTER TABLE `myguests`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
