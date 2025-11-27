-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2025 at 10:10 PM
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
-- Database: `ablelink1`
--

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `role` enum('Utilisateur','Entreprise','Admin') NOT NULL DEFAULT 'Utilisateur',
  `statut` enum('actif','banni') NOT NULL DEFAULT 'actif',
  `date_inscription` datetime DEFAULT current_timestamp(),
  `date_modification` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `telephone`, `role`, `statut`, `date_inscription`, `date_modification`, `photo`) VALUES
(39, 'klaiii', 'nourhenee', 'klai12345@gmail.com', '$2y$10$RRZRBeEU/gR/uBmS0QShSOs9h08yhB6YXKozb.u.waLLWuNcqCwHC', '9619671111', 'Utilisateur', 'actif', '2025-11-23 11:45:34', '2025-11-25 21:55:37', 'user_39_1764104137.jpg'),
(40, 'wafa', 'wafa', 'wafa1@gmail.com', '$2y$10$Dnwfardz8bvYOtpWLeoXVeGKl7EZDnlWGyKIgxM2MP4p9R/33A8Lm', '54430441', 'Entreprise', 'actif', '2025-11-23 11:48:16', '2025-11-25 21:54:54', 'user_40_1764104094.JPG'),
(42, 'off', 'off', 'off@gmail.com', '$2y$10$gDbdF1JHvFCxAVuucdA1l.tskjV9QvBMBxI1eioiS95yrfMMIfCKe', '', 'Utilisateur', 'banni', '2025-11-23 18:57:28', '2025-11-23 18:57:48', NULL),
(48, 'Admin', 'System', 'admin@ablelink.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0000000000', 'Admin', 'actif', '2025-11-25 22:08:08', '2025-11-25 22:08:08', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
