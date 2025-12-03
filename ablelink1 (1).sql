-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 02, 2025 at 07:19 PM
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
-- Database: `projet`
--

-- --------------------------------------------------------

--
-- Table structure for table `admindashboard`
--

CREATE TABLE `admindashboard` (
  `id` int(11) NOT NULL,
  `dummy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auditlog`
--

CREATE TABLE `auditlog` (
  `id` int(11) NOT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `action` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `candidature`
--

CREATE TABLE `candidature` (
  `id` int(11) NOT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `idOffre` int(11) NOT NULL,
  `dateDepot` datetime DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL,
  `cv` text DEFAULT NULL,
  `lettreMotivation` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `commentaire`
--

CREATE TABLE `commentaire` (
  `id` int(11) NOT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `idPost` int(11) NOT NULL,
  `contenu` text DEFAULT NULL,
  `dateCommentaire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evaluation`
--

CREATE TABLE `evaluation` (
  `id` int(11) NOT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `idEvenement` int(11) NOT NULL,
  `note` int(11) DEFAULT NULL,
  `commentaire` varchar(100) DEFAULT NULL,
  `signalee` tinyint(1) DEFAULT 0,
  `etat_moderation` varchar(50) DEFAULT 'Visible',
  `dateEvaluation` datetime DEFAULT NULL,
  `note_accessibilite` int(11) DEFAULT NULL,
  `note_inclusion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation`
--

INSERT INTO `evaluation` (`id`, `idUtilisateur`, `idEvenement`, `note`, `commentaire`, `signalee`, `etat_moderation`, `dateEvaluation`, `note_accessibilite`, `note_inclusion`) VALUES
(3, 1, 9, 0, 'Not yet evaluated', 0, 'Visible', '2025-11-18 17:07:53', NULL, NULL),
(7, 1, 3, 5, 'This is a test comment from debug script', 0, 'Visible', '2025-11-18 17:44:16', NULL, NULL),
(8, 1, 15, 0, 'Not yet evaluated', 0, 'Visible', '2025-11-18 17:46:46', NULL, NULL),
(9, 1, 16, 0, 'Not yet evaluated', 0, 'Visible', '2025-11-18 17:53:40', NULL, NULL),
(11, 1, 17, 1, 'erergr', 0, 'Visible', '2025-11-18 17:55:31', 1, 1),
(12, 1, 18, 2, 'hrheheheh 4@', 1, 'En attente', '2025-11-18 19:10:05', 1, 3),
(13, 1, 19, 2, 'rtgrthtrhrt', 0, 'Visible', '2025-11-18 19:57:05', 1, 2),
(14, 1, 28, 3, 'charlie kirrrrrrrk', 0, 'Visible', '2025-11-19 22:26:24', 2, 3),
(15, 6, 44, 1, 'fghhttrhrthrrhtrhtrthrth', 0, 'Visible', '2025-12-02 17:48:46', 1, 1),
(16, 6, 43, 3, 'ef,erojgerogjerojoerijgoe', 0, 'Visible', '2025-12-02 18:35:04', 3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `evenement`
--

CREATE TABLE `evenement` (
  `id` int(11) NOT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `titre` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `lieu` varchar(150) DEFAULT NULL,
  `theme` varchar(100) DEFAULT NULL,
  `accessibilite` varchar(255) DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL,
  `participants_max` int(11) DEFAULT 50,
  `inscrits` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evenement`
--

INSERT INTO `evenement` (`id`, `idUtilisateur`, `titre`, `description`, `date`, `lieu`, `theme`, `accessibilite`, `statut`, `participants_max`, `inscrits`) VALUES
(3, 1, '4@@@@@ll', '', '2025-11-18 23:05:00', '4@? twin towers', 'Inclusion', 'Langue des signes, Accès PMR', 'Publié', 50, 0),
(4, 1, 'grtrgrg', 'grrgtr', '2025-11-19 15:09:00', 'rttgr', 'Inclusion', 'Visio (en ligne)', 'Publié', 50, 0),
(6, 1, 'klerklkl', 'lkjlkjlkjklj', '2025-11-19 15:40:00', 'kjlk', 'Inclusion', 'Langue des signes', 'Publié', 50, 0),
(7, 1, 'oi,io,io,o,', 'àjàjiàjij', '2025-11-19 15:53:00', 'oijoijojoij', 'Inclusion', 'Accès PMR', 'Publié', 50, 0),
(8, 1, 'fddfdf', 'lknlkkln', '2025-08-19 15:59:00', 'lmlm', 'Inclusion', 'Accès PMR', 'Publié', 50, 0),
(9, 1, 'pokpokpo', 'kpkopkpo', '2025-10-22 16:07:00', 'ml;lm;ml', 'Inclusion', 'Sous-titrage', 'Publié', 50, 0),
(10, 1, '4@', 'kmkmlk', '2025-09-19 16:13:00', 'Paris, France*', 'Inclusion', 'Langue des signes', 'Publié', 50, 0),
(11, 1, '4@@@@@@@', '@?', '2025-08-15 16:15:00', '564564', 'Inclusion', 'Langue des signes', 'Publié', 50, 0),
(12, 1, 'dfkmdfkmfld', 'meklmkm', '2025-09-09 16:33:00', ';;;;', 'Inclusion', 'Langue des signes', 'Publié', 50, 0),
(13, 1, 'opipoi', 'opipoiopi', '2025-08-19 16:36:00', '587', 'Inclusion', 'Langue des signes', 'Publié', 50, 0),
(14, 1, '1231564564', '1564564654', '2025-11-01 16:43:00', '546545645665465897897', 'Inclusion', 'Langue des signes', 'Publié', 50, 0),
(15, 1, 'kkekekkfeke@', 'lkmklmklm', '2025-11-05 16:46:00', 'kkkk', 'Inclusion', 'Langue des signes', 'Publié', 50, 0),
(16, 1, '454541', 'kfekepkopkopker', '2025-11-01 16:53:00', '787897987897', 'Inclusion', 'Langue des signes', 'Publié', 50, 0),
(17, 1, 'kopkopkopk', 'opkpokpokpok', '2025-11-01 16:55:00', '56465465//', 'Inclusion', 'Visio (en ligne)', 'Publié', 50, 0),
(18, 1, 'kopkpokpokp', 'pokopkpokp', '2025-11-01 18:09:00', 'pkopkopk', 'Inclusion', 'Accès PMR', 'Publié', 50, 0),
(19, 1, 'ifduofiugou', 'iouougo', '2025-11-07 18:56:00', 'fgpkgfpgfkopk', 'Inclusion', 'Langue des signes', 'Publié', 50, 0),
(20, 1, '6@@@@', 'kkkkk', '2025-11-20 20:26:00', 'kmlk', 'Inclusion', 'Visio (en ligne)', 'Brouillon', 50, 0),
(21, 1, 'pokemon4@', 'llllpoke', '2025-11-01 21:19:00', '1223131', 'Inclusion', 'Langue des signes, Sous-titrage', 'Publié', 50, 0),
(22, 1, 'pokemon4@', 'llllpoke', '2025-11-01 21:19:00', '1223131', 'Inclusion', 'Langue des signes, Sous-titrage', 'Publié', 50, 0),
(23, 1, 'pokemon', 'llllpoke', '2025-11-01 21:19:00', 'ezfzefze', 'Inclusion', 'Langue des signes, Sous-titrage', 'Publié', 50, 0),
(24, 1, 'pokemon', 'llllpoke', '2025-11-01 21:19:00', 'ezfzefze', 'Inclusion', 'Langue des signes, Sous-titrage', 'Publié', 50, 0),
(25, 1, 'pokemon', 'llllpoke', '2025-11-01 21:19:00', 'ezfzefze', 'Inclusion', 'Langue des signes, Sous-titrage', 'Publié', 50, 0),
(26, 1, 'pokemon', 'llllpoke', '2025-11-01 21:19:00', 'ezfzefze', 'Inclusion', 'Langue des signes, Sous-titrage', 'Publié', 50, 0),
(27, 1, 'pokemon4@', 'pokem', '2025-11-01 21:23:00', 'home', 'Inclusion', 'Langue des signes', 'Publié', 50, 0),
(28, 1, 'gaalich', 'itshim', '2025-11-01 21:24:00', 'rerererere', 'Inclusion', 'Langue des signes', 'Publié', 50, 0),
(29, 1, 'teeest', 'tttt', '2025-11-01 21:55:00', 'kjjj', 'Inclusion', 'Langue des signes', 'Publié', 50, 0),
(30, 1, 'test1', 'test1', '2025-11-01 10:11:00', 'test1', 'Inclusion', 'Sous-titrage', 'Publié', 50, 0),
(31, 1, 'test2', 'test2', '2025-11-01 10:15:00', 'test2', 'Inclusion', 'Accès PMR', 'Publié', 50, 0),
(32, 0, 'Test Event Direct', 'Direct model test', '2025-11-27 21:48:24', 'Direct Location', 'Inclusion', 'Ramp', 'Brouillon', 50, 0),
(33, 1, 'Test Event Admin', 'Created by Admin via test script', '2025-11-27 21:53:40', 'Admin Location', 'Inclusion', 'Ramp', 'Brouillon', 50, 1),
(34, 0, 'oprkgopkrtpohtpohjptrhj', 'optjhoprtjohpjrtpjhopthoprh', '2025-11-27 20:55:00', 'trkprtk^ptkr', 'Inclusion', 'Langue des signes', 'Publié', 50, 1),
(35, 0, 'skkkkkrrrt', 'skkkkkkkkkkkkkkkkkkkkkkkkkkrrrrrrrrrrrrrrrrt', '2025-11-27 21:06:00', 'poekopkgperkoperk', 'Inclusion', 'Langue des signes, Accès PMR', 'Publié', 50, 0),
(36, 0, 'kilssssssssssss', 'ojgopjgojepogjprogjpergoperj', '2025-11-27 21:11:00', 'opfbpobjpojbopjopjpo', 'Inclusion', 'Langue des signes', 'Publié', 50, 0),
(37, 0, 'opkrpjerpogjerogpjergopj', 'joijpgjpojeropjeropgjeorpgj', '2025-11-27 21:16:00', 'ogperokerpogkep', 'Inclusion', 'Langue des signes', 'Publié', 50, 0),
(38, 0, 'tri tri', 'tritritritritritritritritritritritritritritritritritritritri', '2025-11-27 21:21:00', 'rgerjoiergjoerjg', 'Inclusion', 'Sous-titrage', 'Publié', 50, 0),
(39, 3, 'oijgoijroigjoigjeorjioj', 'oijoijoiegjeroijgreoigjeoigjeroij', '2025-11-27 21:31:00', 'oijoijoijoij', 'Inclusion', 'Langue des signes', 'Publié', 50, 0),
(40, 3, 'gjerjregjeroijgeoi', 'joierogjeoirjoerijpokpokpokpokopk', '2025-11-27 21:39:00', 'kpokpoekgpoerk', 'Inclusion', 'Langue des signes', 'Publié', 50, 0),
(41, 3, 'prtkhoprhkrtophjtophjr', 'pojprojhopjhojrophjoprj', '2025-11-27 21:43:00', 'jprojorpjhorphjopj', 'Inclusion', 'Langue des signes', 'Publié', 50, 1),
(42, 3, '5@@@@@@@@@@@@', '5eropkgeropgk5eropkgeropgk5eropkgeropgk5eropkgeropgk5eropkgeropgk', '2025-11-27 21:45:00', 'kerpokrepogkerpk', 'Inclusion', 'Langue des signes', 'Publié', 50, 1),
(43, 3, '6@@@@@@@@@@@@@@@@@', 'maaaaaaaaamaaaaaaaaamaaaaaaaaamaaaaaaaaamaaaaaaaaa', '2025-11-27 21:50:00', 'okfpokepok', 'Inclusion', 'Autre', 'Publié', 50, 1),
(44, 3, '7@@@pl^p', '7@@@7@@@7@@@7@@@7@@@7@@@7@@@', '2025-11-27 22:01:00', '45454546', 'Inclusion', 'Autre', 'Publié', 50, 1),
(45, 3, 'gojrohjtoihjtrohjrtjhtr', 'rgjeroigjeroigjoerijgreo', '2025-11-27 22:10:00', 'ergkreopgkeorpgkeropkerop', 'Inclusion', 'Sous-titrage', 'Publié', 50, 1),
(46, 3, 'Atelier Accessibilité Numérique', 'dhhrthrthrhrhtrhtrhtrhrthtrhtrhtrdhhrthrthrhrhtrhtrhtrhrthtrhtrhtr', '2025-11-28 08:44:00', 'grhrhtrhtrhtrhtr', 'Inclusion', 'Sous-titrage', 'Brouillon', 50, 0),
(47, 3, 'hamza', '897ktrjrtjhjhotjhprtjhportjhportjhoprtjhop', '2025-12-01 14:15:00', 'jkljrtlkjtrlktgj', 'Inclusion', 'Accès PMR', 'Publié', 50, 0),
(48, 3, 'testing testing', 'testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttest', '2025-12-12 17:42:00', 'pkoefzzzzz', 'Inclusion', 'Langue des signes', 'Publié', 50, 1),
(49, 3, 'retest retest', 'aaaaaaaaaaaAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', '2025-12-25 18:09:00', 'rkoprkhptrkhprtkh', 'Inclusion', 'Sous-titrage', 'Publié', 50, 1),
(50, 3, 'retttttttttttteeeeeeeeeeeest', 'retttttttttttteeeeeeeeeeeestretttttttttttteeeeeeeeeeeestretttttttttttteeeeeeeeeeeest', '2025-12-23 18:12:00', 'lkze,fffffl', 'Inclusion', 'Langue des signes', 'Publié', 50, 1);

-- --------------------------------------------------------

--
-- Table structure for table `offre`
--

CREATE TABLE `offre` (
  `id` int(11) NOT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `titre` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `typeContrat` varchar(100) DEFAULT NULL,
  `localisation` varchar(150) DEFAULT NULL,
  `accessibilite` varchar(255) DEFAULT NULL,
  `datePublication` datetime DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `participation`
--

CREATE TABLE `participation` (
  `id` int(11) NOT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `idEvenement` int(11) NOT NULL,
  `dateInscription` datetime DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL,
  `presence` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `participation`
--

INSERT INTO `participation` (`id`, `idUtilisateur`, `idEvenement`, `dateInscription`, `statut`, `presence`) VALUES
(1, 1, 7, '2025-11-18 16:53:33', 'confirmed', 0),
(2, 1, 8, '2025-11-18 16:59:56', 'confirmed', 0),
(3, 1, 9, '2025-11-18 17:07:53', 'confirmed', 0),
(4, 1, 10, '2025-11-18 17:13:38', 'confirmed', 0),
(5, 1, 11, '2025-11-18 17:16:12', 'confirmed', 0),
(6, 1, 12, '2025-11-18 17:33:29', 'confirmed', 0),
(7, 1, 13, '2025-11-18 17:37:07', 'confirmed', 0),
(8, 1, 14, '2025-11-18 17:43:31', 'confirmed', 0),
(9, 1, 15, '2025-11-18 17:46:46', 'confirmed', 0),
(10, 1, 16, '2025-11-18 17:53:40', 'confirmed', 0),
(11, 1, 17, '2025-11-18 17:55:24', 'confirmed', 0),
(12, 1, 18, '2025-11-18 19:09:45', 'confirmed', 0),
(13, 1, 19, '2025-11-18 19:56:40', 'confirmed', 0),
(14, 0, 34, '2025-11-26 22:03:31', 'Confirmée', 0),
(15, 0, 33, '2025-11-26 22:10:14', 'Confirmée', 0),
(16, 6, 41, '2025-11-26 22:44:05', 'Confirmée', 0),
(17, 6, 42, '2025-11-26 22:46:17', 'Confirmée', 0),
(18, 6, 43, '2025-11-26 22:51:09', 'Confirmée', 0),
(19, 6, 44, '2025-11-26 23:01:59', 'Confirmée', 0),
(20, 6, 45, '2025-11-26 23:11:08', 'Confirmée', 0),
(21, 6, 48, '2025-12-02 19:06:49', 'Confirmée', 0),
(22, 6, 49, '2025-12-02 19:09:47', 'Confirmée', 0),
(23, 6, 50, '2025-12-02 19:12:48', 'Confirmée', 0);

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

CREATE TABLE `permission` (
  `idPermission` int(11) NOT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `permission` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `titre` varchar(150) DEFAULT NULL,
  `contenu` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `datePublication` datetime DEFAULT NULL,
  `popularite` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reaction`
--

CREATE TABLE `reaction` (
  `id` int(11) NOT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `idCible` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `dateReaction` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `signalement`
--

CREATE TABLE `signalement` (
  `id` int(11) NOT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL,
  `cibleType` varchar(50) DEFAULT NULL,
  `cibleId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `statistique`
--

CREATE TABLE `statistique` (
  `id` int(11) NOT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `valeur` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temoignage`
--

CREATE TABLE `temoignage` (
  `id` int(11) NOT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `titre` varchar(150) DEFAULT NULL,
  `contenu` text DEFAULT NULL,
  `secteur` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `typeHandicap` varchar(100) DEFAULT NULL,
  `datePublication` datetime DEFAULT NULL,
  `valide` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `date_inscription` datetime DEFAULT current_timestamp(),
  `statut` enum('actif','banni') NOT NULL DEFAULT 'actif',
  `telephone` varchar(20) DEFAULT NULL,
  `role` enum('Utilisateur','Entreprise','Admin') NOT NULL DEFAULT 'Utilisateur',
  `date_modification` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `date_inscription`, `statut`, `telephone`, `role`, `date_modification`, `photo`) VALUES
(1, 'Epstein', 'Jeffrey', 'jeevacation@gmail.com', 'epsteinisland', '2019-08-10 22:06:11', 'actif', NULL, 'Admin', '2025-11-26 18:37:53', NULL),
(2, 'Kirk', 'Charlie', 'turningpointusa@gmail.com', 'sniped4@', '2025-09-10 22:06:11', 'actif', NULL, 'Utilisateur', '2025-11-26 18:37:53', NULL),
(3, 'ahmed', 'mohsen', 'ahmedmohsen@gmail.com', '$2y$10$7X84ZrbQrJoBGAMGp8qJlOffBGKuueNaTJERtqFqvj7oKPoIl8lnG', '2025-11-26 19:03:50', 'actif', '25111585', 'Admin', '2025-11-26 23:33:49', NULL),
(6, 'amine', 'gaalich', 'amingaalich@gmail.com', '$2y$10$SZsUbpgumsJXxL8U3D1dkOl2O5nruuQkfRiz4A7I0x4N6MxF7UzYy', '2025-11-26 22:29:22', 'actif', '25111584', 'Utilisateur', '2025-11-26 22:29:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur_backup`
--

CREATE TABLE `utilisateur_backup` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `date_inscription` datetime DEFAULT current_timestamp(),
  `statut` enum('actif','banni') NOT NULL DEFAULT 'actif',
  `isAdmin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateur_backup`
--

INSERT INTO `utilisateur_backup` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `date_inscription`, `statut`, `isAdmin`) VALUES
(1, 'Epstein', 'Jeffrey', 'jeevacation@gmail.com', 'epsteinisland', '2019-08-10 22:06:11', 'actif', 1),
(2, 'Kirk', 'Charlie', 'turningpointusa@gmail.com', 'sniped4@', '2025-09-10 22:06:11', 'actif', 0);

-- --------------------------------------------------------

--
-- Table structure for table `amenagements_disponibles`
--

CREATE TABLE `amenagements_disponibles` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `type` enum('materiel','logiciel','organisationnel') NOT NULL,
  `description` text DEFAULT NULL,
  `cout_estimatif` decimal(10,2) DEFAULT NULL,
  `fournisseur` varchar(100) DEFAULT NULL,
  `categorie_handicap` varchar(100) DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `demandes_amenagement`
--

CREATE TABLE `demandes_amenagement` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amenagement_id` int(11) NOT NULL,
  `entreprise_id` int(11) DEFAULT NULL,
  `statut` enum('demandé','validé','installé') NOT NULL DEFAULT 'demandé',
  `justification` text DEFAULT NULL,
  `date_demande` date DEFAULT NULL,
  `date_installation` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admindashboard`
--
ALTER TABLE `admindashboard`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auditlog`
--
ALTER TABLE `auditlog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUtilisateur` (`idUtilisateur`);

--
-- Indexes for table `candidature`
--
ALTER TABLE `candidature`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUtilisateur` (`idUtilisateur`),
  ADD KEY `idOffre` (`idOffre`);

--
-- Indexes for table `commentaire`
--
ALTER TABLE `commentaire`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUtilisateur` (`idUtilisateur`),
  ADD KEY `idPost` (`idPost`);

--
-- Indexes for table `evaluation`
--
ALTER TABLE `evaluation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUtilisateur` (`idUtilisateur`),
  ADD KEY `idEvenement` (`idEvenement`);

--
-- Indexes for table `evenement`
--
ALTER TABLE `evenement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUtilisateur` (`idUtilisateur`);

--
-- Indexes for table `offre`
--
ALTER TABLE `offre`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUtilisateur` (`idUtilisateur`);

--
-- Indexes for table `participation`
--
ALTER TABLE `participation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUtilisateur` (`idUtilisateur`),
  ADD KEY `idEvenement` (`idEvenement`);

--
-- Indexes for table `permission`
--
ALTER TABLE `permission`
  ADD PRIMARY KEY (`idPermission`),
  ADD KEY `idUtilisateur` (`idUtilisateur`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUtilisateur` (`idUtilisateur`);

--
-- Indexes for table `reaction`
--
ALTER TABLE `reaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUtilisateur` (`idUtilisateur`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `signalement`
--
ALTER TABLE `signalement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUtilisateur` (`idUtilisateur`);

--
-- Indexes for table `statistique`
--
ALTER TABLE `statistique`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUtilisateur` (`idUtilisateur`);

--
-- Indexes for table `temoignage`
--
ALTER TABLE `temoignage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUtilisateur` (`idUtilisateur`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `amenagements_disponibles`
--
ALTER TABLE `amenagements_disponibles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `demandes_amenagement`
--
ALTER TABLE `demandes_amenagement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `amenagement_id` (`amenagement_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admindashboard`
--
ALTER TABLE `admindashboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auditlog`
--
ALTER TABLE `auditlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `candidature`
--
ALTER TABLE `candidature`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `commentaire`
--
ALTER TABLE `commentaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `evenement`
--
ALTER TABLE `evenement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `offre`
--
ALTER TABLE `offre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `participation`
--
ALTER TABLE `participation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `permission`
--
ALTER TABLE `permission`
  MODIFY `idPermission` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reaction`
--
ALTER TABLE `reaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `signalement`
--
ALTER TABLE `signalement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `statistique`
--
ALTER TABLE `statistique`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `temoignage`
--
ALTER TABLE `temoignage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `amenagements_disponibles`
--
ALTER TABLE `amenagements_disponibles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `demandes_amenagement`
--
ALTER TABLE `demandes_amenagement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auditlog`
--
ALTER TABLE `auditlog`
  ADD CONSTRAINT `auditlog_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`);

--
-- Constraints for table `candidature`
--
ALTER TABLE `candidature`
  ADD CONSTRAINT `candidature_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `candidature_ibfk_2` FOREIGN KEY (`idOffre`) REFERENCES `offre` (`id`);

--
-- Constraints for table `commentaire`
--
ALTER TABLE `commentaire`
  ADD CONSTRAINT `commentaire_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `commentaire_ibfk_2` FOREIGN KEY (`idPost`) REFERENCES `post` (`id`);

--
-- Constraints for table `evaluation`
--
ALTER TABLE `evaluation`
  ADD CONSTRAINT `evaluation_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `evaluation_ibfk_2` FOREIGN KEY (`idEvenement`) REFERENCES `evenement` (`id`);

--
-- Constraints for table `evenement`
--
ALTER TABLE `evenement`
  ADD CONSTRAINT `evenement_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`);

--
-- Constraints for table `offre`
--
ALTER TABLE `offre`
  ADD CONSTRAINT `offre_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`);

--
-- Constraints for table `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `participation_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `participation_ibfk_2` FOREIGN KEY (`idEvenement`) REFERENCES `evenement` (`id`);

--
-- Constraints for table `permission`
--
ALTER TABLE `permission`
  ADD CONSTRAINT `permission_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`);

--
-- Constraints for table `reaction`
--
ALTER TABLE `reaction`
  ADD CONSTRAINT `reaction_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`);

--
-- Constraints for table `signalement`
--
ALTER TABLE `signalement`
  ADD CONSTRAINT `signalement_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`);

--
-- Constraints for table `statistique`
--
ALTER TABLE `statistique`
  ADD CONSTRAINT `statistique_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`);

ALTER TABLE `temoignage`
  ADD CONSTRAINT `temoignage_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`);

--
-- Constraints for table `demandes_amenagement`
--
ALTER TABLE `demandes_amenagement`
  ADD CONSTRAINT `demandes_amenagement_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `demandes_amenagement_ibfk_2` FOREIGN KEY (`amenagement_id`) REFERENCES `amenagements_disponibles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
