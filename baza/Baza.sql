-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql310.infinityfree.com
-- Generation Time: Nov 11, 2025 at 03:08 AM
-- Server version: 11.4.7-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_40317569_placeholder_baza`
--

-- --------------------------------------------------------

--
-- Table structure for table `gradiva`
--

CREATE TABLE `gradiva` (
  `id` int(11) NOT NULL,
  `naslov` varchar(100) NOT NULL,
  `vsebina` text DEFAULT NULL,
  `tip` enum('dokument','video','povezava','drugi') NOT NULL,
  `pot_do_datoteke` varchar(255) DEFAULT NULL,
  `id_predmeta` int(11) NOT NULL,
  `id_avtorja` int(11) NOT NULL,
  `datum_objave` datetime NOT NULL,
  `datum_spremembe` datetime DEFAULT NULL,
  `status` enum('aktiven','arhiviran') DEFAULT 'aktiven'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gradiva`
--

INSERT INTO `gradiva` (`id`, `naslov`, `vsebina`, `tip`, `pot_do_datoteke`, `id_predmeta`, `id_avtorja`, `datum_objave`, `datum_spremembe`, `status`) VALUES
(1, 'Uvod v algebro', 'Osnove algebraičnih izrazov in enačb.', 'dokument', '/gradiva/matematika/uvod_algebra.pdf', 1, 2, '2025-11-10 23:56:50', NULL, 'aktiven'),
(2, 'Slovenska slovnica', 'Pregled slovničnih pravil in razlag.', 'dokument', '/gradiva/slovenscina/slovnica.pdf', 2, 8, '2025-11-10 23:56:50', NULL, 'aktiven'),
(3, 'English Grammar', 'Basic English grammar rules and exercises.', 'video', 'https://youtube.com/watch?v=abc123', 3, 3, '2025-11-10 23:56:50', NULL, 'aktiven'),
(4, 'Mehanika tekočin', 'Osnove mehanike tekočin in hidrodinamike.', 'dokument', '/gradiva/fizika/tekočine.pdf', 4, 4, '2025-11-10 23:56:50', NULL, 'aktiven'),
(5, 'Periodni sistem', 'Interaktivni periodni sistem elementov.', 'povezava', 'https://www.rsc.org/periodic-table', 5, 10, '2025-11-10 23:56:50', NULL, 'aktiven');

-- --------------------------------------------------------

--
-- Table structure for table `naloge`
--

CREATE TABLE `naloge` (
  `id` int(11) NOT NULL,
  `naslov` varchar(100) NOT NULL,
  `navodila` text NOT NULL,
  `rok_addaje` datetime NOT NULL,
  `maksimalna_ocena` int(11) DEFAULT 10,
  `id_predmeta` int(11) NOT NULL,
  `id_avtorja` int(11) NOT NULL,
  `datum_objave` datetime NOT NULL,
  `datum_spremembe` datetime DEFAULT NULL,
  `status` enum('aktiven','zaključen','arhiviran') DEFAULT 'aktiven'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `naloge`
--

INSERT INTO `naloge` (`id`, `naslov`, `navodila`, `rok_addaje`, `maksimalna_ocena`, `id_predmeta`, `id_avtorja`, `datum_objave`, `datum_spremembe`, `status`) VALUES
(1, 'Algebraične enačbe', 'Rešite naslednje algebraične enačbe in prikažite postopek reševanja.', '2024-10-15 23:59:00', 10, 1, 2, '2025-11-10 23:56:50', NULL, 'aktiven'),
(2, 'Analiza pesmi', 'Analizirajte pesem \"Sonetni venec\" in opišite njene tematske značilnosti.', '2024-10-20 23:59:00', 10, 2, 8, '2025-11-10 23:56:50', NULL, 'aktiven'),
(3, 'English Essay', 'Write a 300-word essay about your favorite hobby.', '2024-10-18 23:59:00', 10, 3, 3, '2025-11-10 23:56:50', NULL, 'aktiven'),
(4, 'Newtonovi zakoni', 'Razložite Newtonove zakone gibanja s primeri iz vsakdanjega življenja.', '2024-10-22 23:59:00', 10, 4, 4, '2025-11-10 23:56:50', NULL, 'aktiven'),
(5, 'Kemijske reakcije', 'Opišite različne vrste kemijskih reakcij in navedite primere.', '2024-10-25 23:59:00', 10, 5, 10, '2025-11-10 23:56:50', NULL, 'aktiven');

-- --------------------------------------------------------

--
-- Table structure for table `ocene`
--

CREATE TABLE `ocene` (
  `id` int(11) NOT NULL,
  `id_ucenca` int(11) NOT NULL,
  `id_naloge` int(11) NOT NULL,
  `ocena` decimal(5,2) NOT NULL,
  `komentar` text DEFAULT NULL,
  `datum_ocenjevanja` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oddaje`
--

CREATE TABLE `oddaje` (
  `id` int(11) NOT NULL,
  `id_naloge` int(11) NOT NULL,
  `id_ucenca` int(11) NOT NULL,
  `datum_oddaje` datetime NOT NULL,
  `ocena` int(11) DEFAULT NULL,
  `komentar` text DEFAULT NULL,
  `pot_do_datoteke` varchar(255) NOT NULL,
  `originalno_ime_datoteke` varchar(255) NOT NULL,
  `status` enum('oddano','v_ocenjevanju','ocenjeno','popravljanje') DEFAULT 'oddano'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `oddaje`
--

INSERT INTO `oddaje` (`id`, `id_naloge`, `id_ucenca`, `datum_oddaje`, `ocena`, `komentar`, `pot_do_datoteke`, `originalno_ime_datoteke`, `status`) VALUES
(1, 1, 22, '2024-10-10 14:30:00', NULL, NULL, '/oddaje/naloga1/Mlakar_Ana_Algebraične_enačbe.pdf', 'algebra_resitve.pdf', 'oddano'),
(2, 1, 23, '2024-10-11 09:15:00', NULL, NULL, '/oddaje/naloga1/Kos_Nejc_Algebraične_enačbe.docx', 'matematika.docx', 'v_ocenjevanju'),
(3, 2, 24, '2024-10-12 16:45:00', NULL, NULL, '/oddaje/naloga2/Zupančič_Eva_Analiza_pesmi.pdf', 'analiza_sonetni_venec.pdf', 'ocenjeno'),
(4, 3, 25, '2024-10-09 11:20:00', NULL, NULL, '/oddaje/naloga3/Jerman_Matic_English_Essay.docx', 'my_hobby_essay.docx', 'oddano');

-- --------------------------------------------------------

--
-- Table structure for table `pravice`
--

CREATE TABLE `pravice` (
  `id` int(11) NOT NULL,
  `id_uporabnika` int(11) NOT NULL,
  `pravica` enum('dodajanje_nalog','ocenjevanje','oddajanje_nalog') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `pravice`
--

INSERT INTO `pravice` (`id`, `id_uporabnika`, `pravica`) VALUES
(1, 2, 'dodajanje_nalog'),
(2, 3, 'dodajanje_nalog'),
(3, 4, 'dodajanje_nalog'),
(4, 5, 'dodajanje_nalog'),
(5, 6, 'dodajanje_nalog'),
(6, 7, 'dodajanje_nalog'),
(7, 8, 'dodajanje_nalog'),
(8, 9, 'dodajanje_nalog'),
(9, 10, 'dodajanje_nalog'),
(10, 11, 'dodajanje_nalog'),
(11, 12, 'dodajanje_nalog'),
(12, 13, 'dodajanje_nalog'),
(13, 14, 'dodajanje_nalog'),
(14, 15, 'dodajanje_nalog'),
(15, 16, 'dodajanje_nalog'),
(16, 17, 'dodajanje_nalog'),
(17, 18, 'dodajanje_nalog'),
(18, 19, 'dodajanje_nalog'),
(19, 20, 'dodajanje_nalog'),
(20, 21, 'dodajanje_nalog'),
(21, 2, 'ocenjevanje'),
(22, 3, 'ocenjevanje'),
(23, 4, 'ocenjevanje'),
(24, 5, 'ocenjevanje'),
(25, 6, 'ocenjevanje'),
(26, 7, 'ocenjevanje'),
(27, 8, 'ocenjevanje'),
(28, 9, 'ocenjevanje'),
(29, 10, 'ocenjevanje'),
(30, 11, 'ocenjevanje'),
(31, 12, 'ocenjevanje'),
(32, 13, 'ocenjevanje'),
(33, 14, 'ocenjevanje'),
(34, 15, 'ocenjevanje'),
(35, 16, 'ocenjevanje'),
(36, 17, 'ocenjevanje'),
(37, 18, 'ocenjevanje'),
(38, 19, 'ocenjevanje'),
(39, 20, 'ocenjevanje'),
(40, 21, 'ocenjevanje'),
(41, 22, 'oddajanje_nalog'),
(42, 23, 'oddajanje_nalog'),
(43, 24, 'oddajanje_nalog'),
(44, 25, 'oddajanje_nalog'),
(45, 26, 'oddajanje_nalog'),
(46, 27, 'oddajanje_nalog'),
(47, 28, 'oddajanje_nalog'),
(48, 29, 'oddajanje_nalog'),
(49, 30, 'oddajanje_nalog'),
(50, 31, 'oddajanje_nalog');

-- --------------------------------------------------------

--
-- Table structure for table `predmeti`
--

CREATE TABLE `predmeti` (
  `id` int(11) NOT NULL,
  `ime` varchar(45) NOT NULL,
  `koda` varchar(10) NOT NULL,
  `opis` text DEFAULT NULL,
  `status` enum('aktiven','neaktiven') DEFAULT 'aktiven'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `predmeti`
--

INSERT INTO `predmeti` (`id`, `ime`, `koda`, `opis`, `status`) VALUES
(1, 'Matematika', 'MAT', 'Osnove matematike in linearna algebra', 'aktiven'),
(2, 'Slovenski jezik', 'SLO', 'Slovenska slovnica in književnost', 'aktiven'),
(3, 'Angleščina', 'ANG', 'Angleški jezik', 'aktiven'),
(4, 'Fizika', 'FIZ', 'Osnove sile fizike', 'aktiven'),
(5, 'Kemija', 'KEM', 'Kemijske reakcije in snovi', 'aktiven'),
(6, 'Zgodovina', 'ZGO', 'Svetovna in slovenska zgodovina', 'aktiven'),
(7, 'Geografija', 'GEO', 'Zemljepis in reliefi', 'aktiven'),
(8, 'Računalništvo', 'NRP', 'Programiranje in računalniške osnove', 'aktiven'),
(9, 'Biologija', 'BIO', 'Živi svet in ekosistemi', 'aktiven'),
(10, 'Stroka moderne vsebine', 'SMV', 'Racunalnistvo teorija in praksa', 'aktiven');

-- --------------------------------------------------------

--
-- Table structure for table `ucenci_predmeti`
--

CREATE TABLE `ucenci_predmeti` (
  `id` int(11) NOT NULL,
  `id_ucenca` int(11) NOT NULL,
  `id_predmeta` int(11) NOT NULL,
  `datum_vpisa` date NOT NULL,
  `status` enum('vpisano','opuščeno') DEFAULT 'vpisano'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ucenci_predmeti`
--

INSERT INTO `ucenci_predmeti` (`id`, `id_ucenca`, `id_predmeta`, `datum_vpisa`, `status`) VALUES
(1, 22, 1, '2024-09-01', 'vpisano'),
(2, 22, 2, '2024-09-01', 'vpisano'),
(3, 22, 3, '2024-09-01', 'vpisano'),
(4, 23, 1, '2024-09-01', 'vpisano'),
(5, 23, 4, '2024-09-01', 'vpisano'),
(6, 23, 8, '2024-09-01', 'vpisano'),
(7, 24, 2, '2024-09-01', 'vpisano'),
(8, 24, 5, '2024-09-01', 'vpisano'),
(9, 24, 9, '2024-09-01', 'vpisano'),
(10, 25, 3, '2024-09-01', 'vpisano'),
(11, 25, 6, '2024-09-01', 'vpisano'),
(12, 25, 10, '2024-09-01', 'vpisano'),
(13, 26, 1, '2024-09-01', 'vpisano'),
(14, 26, 7, '2024-09-01', 'vpisano'),
(15, 26, 8, '2024-09-01', 'vpisano'),
(16, 27, 2, '2024-09-01', 'vpisano'),
(17, 27, 4, '2024-09-01', 'vpisano'),
(18, 27, 9, '2024-09-01', 'vpisano'),
(19, 28, 3, '2024-09-01', 'vpisano'),
(20, 28, 5, '2024-09-01', 'vpisano'),
(21, 28, 10, '2024-09-01', 'vpisano'),
(22, 29, 1, '2024-09-01', 'vpisano'),
(23, 29, 6, '2024-09-01', 'vpisano'),
(24, 29, 7, '2024-09-01', 'vpisano'),
(25, 30, 2, '2024-09-01', 'vpisano'),
(26, 30, 8, '2024-09-01', 'vpisano'),
(27, 30, 9, '2024-09-01', 'vpisano'),
(28, 31, 3, '2024-09-01', 'vpisano'),
(29, 31, 4, '2024-09-01', 'vpisano'),
(30, 31, 10, '2024-09-01', 'vpisano'),
(31, 32, 1, '2024-09-01', 'vpisano'),
(32, 32, 2, '2024-09-01', 'vpisano'),
(33, 32, 3, '2024-09-01', 'vpisano'),
(34, 33, 1, '2024-09-01', 'vpisano'),
(35, 33, 4, '2024-09-01', 'vpisano'),
(36, 33, 8, '2024-09-01', 'vpisano'),
(37, 34, 2, '2024-09-01', 'vpisano'),
(38, 34, 5, '2024-09-01', 'vpisano'),
(39, 34, 9, '2024-09-01', 'vpisano'),
(40, 35, 3, '2024-09-01', 'vpisano'),
(41, 35, 6, '2024-09-01', 'vpisano'),
(42, 35, 10, '2024-09-01', 'vpisano'),
(43, 36, 1, '2024-09-01', 'vpisano'),
(44, 36, 7, '2024-09-01', 'vpisano'),
(45, 36, 8, '2024-09-01', 'vpisano'),
(46, 37, 2, '2024-09-01', 'vpisano'),
(47, 37, 4, '2024-09-01', 'vpisano'),
(48, 37, 9, '2024-09-01', 'vpisano'),
(49, 38, 3, '2024-09-01', 'vpisano'),
(50, 38, 5, '2024-09-01', 'vpisano'),
(51, 38, 10, '2024-09-01', 'vpisano'),
(52, 39, 1, '2024-09-01', 'vpisano'),
(53, 39, 6, '2024-09-01', 'vpisano'),
(54, 39, 7, '2024-09-01', 'vpisano'),
(55, 40, 2, '2024-09-01', 'vpisano'),
(56, 40, 8, '2024-09-01', 'vpisano'),
(57, 40, 9, '2024-09-01', 'vpisano'),
(58, 41, 3, '2024-09-01', 'vpisano'),
(59, 41, 4, '2024-09-01', 'vpisano'),
(60, 41, 10, '2024-09-01', 'vpisano'),
(61, 42, 1, '2024-09-01', 'vpisano'),
(62, 42, 2, '2024-09-01', 'vpisano'),
(63, 42, 5, '2024-09-01', 'vpisano'),
(64, 43, 3, '2024-09-01', 'vpisano'),
(65, 43, 6, '2024-09-01', 'vpisano'),
(66, 43, 8, '2024-09-01', 'vpisano'),
(67, 44, 4, '2024-09-01', 'vpisano'),
(68, 44, 7, '2024-09-01', 'vpisano'),
(69, 44, 9, '2024-09-01', 'vpisano'),
(70, 45, 5, '2024-09-01', 'vpisano'),
(71, 45, 10, '2024-09-01', 'vpisano'),
(72, 45, 1, '2024-09-01', 'vpisano'),
(73, 46, 2, '2024-09-01', 'vpisano'),
(74, 46, 6, '2024-09-01', 'vpisano'),
(75, 46, 8, '2024-09-01', 'vpisano'),
(76, 47, 3, '2024-09-01', 'vpisano'),
(77, 47, 7, '2024-09-01', 'vpisano'),
(78, 47, 9, '2024-09-01', 'vpisano'),
(79, 48, 4, '2024-09-01', 'vpisano'),
(80, 48, 8, '2024-09-01', 'vpisano'),
(81, 48, 10, '2024-09-01', 'vpisano'),
(82, 49, 1, '2024-09-01', 'vpisano'),
(83, 49, 5, '2024-09-01', 'vpisano'),
(84, 49, 6, '2024-09-01', 'vpisano'),
(85, 50, 2, '2024-09-01', 'vpisano'),
(86, 50, 7, '2024-09-01', 'vpisano'),
(87, 50, 9, '2024-09-01', 'vpisano'),
(88, 51, 3, '2024-09-01', 'vpisano'),
(89, 51, 8, '2024-09-01', 'vpisano'),
(90, 51, 10, '2024-09-01', 'vpisano'),
(91, 52, 1, '2024-09-01', 'vpisano'),
(92, 52, 4, '2024-09-01', 'vpisano'),
(93, 52, 9, '2024-09-01', 'vpisano'),
(94, 53, 2, '2024-09-01', 'vpisano'),
(95, 53, 5, '2024-09-01', 'vpisano'),
(96, 53, 7, '2024-09-01', 'vpisano'),
(97, 54, 3, '2024-09-01', 'vpisano'),
(98, 54, 6, '2024-09-01', 'vpisano'),
(99, 54, 8, '2024-09-01', 'vpisano'),
(100, 55, 4, '2024-09-01', 'vpisano'),
(101, 55, 9, '2024-09-01', 'vpisano'),
(102, 55, 10, '2024-09-01', 'vpisano'),
(103, 56, 1, '2024-09-01', 'vpisano'),
(104, 56, 5, '2024-09-01', 'vpisano'),
(105, 56, 6, '2024-09-01', 'vpisano'),
(106, 57, 2, '2024-09-01', 'vpisano'),
(107, 57, 7, '2024-09-01', 'vpisano'),
(108, 57, 8, '2024-09-01', 'vpisano'),
(109, 58, 3, '2024-09-01', 'vpisano'),
(110, 58, 9, '2024-09-01', 'vpisano'),
(111, 58, 10, '2024-09-01', 'vpisano'),
(112, 59, 4, '2024-09-01', 'vpisano'),
(113, 59, 1, '2024-09-01', 'vpisano'),
(114, 59, 6, '2024-09-01', 'vpisano'),
(115, 60, 5, '2024-09-01', 'vpisano'),
(116, 60, 2, '2024-09-01', 'vpisano'),
(117, 60, 7, '2024-09-01', 'vpisano'),
(118, 61, 6, '2024-09-01', 'vpisano'),
(119, 61, 3, '2024-09-01', 'vpisano'),
(120, 61, 8, '2024-09-01', 'vpisano'),
(121, 62, 7, '2024-09-01', 'vpisano'),
(122, 62, 4, '2024-09-01', 'vpisano'),
(123, 62, 9, '2024-09-01', 'vpisano'),
(124, 63, 8, '2024-09-01', 'vpisano'),
(125, 63, 5, '2024-09-01', 'vpisano'),
(126, 63, 10, '2024-09-01', 'vpisano'),
(127, 64, 9, '2024-09-01', 'vpisano'),
(128, 64, 1, '2024-09-01', 'vpisano'),
(129, 64, 6, '2024-09-01', 'vpisano'),
(130, 65, 10, '2024-09-01', 'vpisano'),
(131, 65, 2, '2024-09-01', 'vpisano'),
(132, 65, 7, '2024-09-01', 'vpisano'),
(133, 66, 1, '2024-09-01', 'vpisano'),
(134, 66, 3, '2024-09-01', 'vpisano'),
(135, 66, 8, '2024-09-01', 'vpisano'),
(136, 67, 2, '2024-09-01', 'vpisano'),
(137, 67, 4, '2024-09-01', 'vpisano'),
(138, 67, 9, '2024-09-01', 'vpisano'),
(139, 68, 3, '2024-09-01', 'vpisano'),
(140, 68, 5, '2024-09-01', 'vpisano'),
(141, 68, 10, '2024-09-01', 'vpisano'),
(142, 69, 4, '2024-09-01', 'vpisano'),
(143, 69, 6, '2024-09-01', 'vpisano'),
(144, 69, 1, '2024-09-01', 'vpisano'),
(145, 70, 5, '2024-09-01', 'vpisano'),
(146, 70, 7, '2024-09-01', 'vpisano'),
(147, 70, 2, '2024-09-01', 'vpisano'),
(148, 71, 6, '2024-09-01', 'vpisano'),
(149, 71, 8, '2024-09-01', 'vpisano'),
(150, 71, 3, '2024-09-01', 'vpisano'),
(151, 72, 7, '2024-09-01', 'vpisano'),
(152, 72, 9, '2024-09-01', 'vpisano'),
(153, 72, 4, '2024-09-01', 'vpisano'),
(154, 73, 8, '2024-09-01', 'vpisano'),
(155, 73, 10, '2024-09-01', 'vpisano'),
(156, 73, 5, '2024-09-01', 'vpisano'),
(157, 74, 9, '2024-09-01', 'vpisano'),
(158, 74, 1, '2024-09-01', 'vpisano'),
(159, 74, 6, '2024-09-01', 'vpisano'),
(160, 75, 10, '2024-09-01', 'vpisano'),
(161, 75, 2, '2024-09-01', 'vpisano'),
(162, 75, 7, '2024-09-01', 'vpisano'),
(163, 76, 1, '2024-09-01', 'vpisano'),
(164, 76, 3, '2024-09-01', 'vpisano'),
(165, 76, 8, '2024-09-01', 'vpisano'),
(166, 77, 2, '2024-09-01', 'vpisano'),
(167, 77, 4, '2024-09-01', 'vpisano'),
(168, 77, 9, '2024-09-01', 'vpisano'),
(169, 78, 3, '2024-09-01', 'vpisano'),
(170, 78, 5, '2024-09-01', 'vpisano'),
(171, 78, 10, '2024-09-01', 'vpisano'),
(172, 79, 4, '2024-09-01', 'vpisano'),
(173, 79, 6, '2024-09-01', 'vpisano'),
(174, 79, 1, '2024-09-01', 'vpisano'),
(175, 80, 5, '2024-09-01', 'vpisano'),
(176, 80, 7, '2024-09-01', 'vpisano'),
(177, 80, 2, '2024-09-01', 'vpisano'),
(178, 81, 6, '2024-09-01', 'vpisano'),
(179, 81, 8, '2024-09-01', 'vpisano'),
(180, 81, 3, '2024-09-01', 'vpisano'),
(181, 82, 7, '2024-09-01', 'vpisano'),
(182, 82, 9, '2024-09-01', 'vpisano'),
(183, 82, 4, '2024-09-01', 'vpisano'),
(184, 83, 8, '2024-09-01', 'vpisano'),
(185, 83, 10, '2024-09-01', 'vpisano'),
(186, 83, 5, '2024-09-01', 'vpisano'),
(187, 84, 9, '2024-09-01', 'vpisano'),
(188, 84, 1, '2024-09-01', 'vpisano'),
(189, 84, 6, '2024-09-01', 'vpisano'),
(190, 85, 10, '2024-09-01', 'vpisano'),
(191, 85, 2, '2024-09-01', 'vpisano'),
(192, 85, 7, '2024-09-01', 'vpisano'),
(193, 86, 1, '2024-09-01', 'vpisano'),
(194, 86, 3, '2024-09-01', 'vpisano'),
(195, 86, 8, '2024-09-01', 'vpisano'),
(196, 87, 2, '2024-09-01', 'vpisano'),
(197, 87, 4, '2024-09-01', 'vpisano'),
(198, 87, 9, '2024-09-01', 'vpisano'),
(199, 88, 3, '2024-09-01', 'vpisano'),
(200, 88, 5, '2024-09-01', 'vpisano'),
(201, 88, 10, '2024-09-01', 'vpisano'),
(202, 89, 4, '2024-09-01', 'vpisano'),
(203, 89, 6, '2024-09-01', 'vpisano'),
(204, 89, 1, '2024-09-01', 'vpisano'),
(205, 90, 5, '2024-09-01', 'vpisano'),
(206, 90, 7, '2024-09-01', 'vpisano'),
(207, 90, 2, '2024-09-01', 'vpisano'),
(208, 91, 6, '2024-09-01', 'vpisano'),
(209, 91, 8, '2024-09-01', 'vpisano'),
(210, 91, 3, '2024-09-01', 'vpisano'),
(211, 92, 7, '2024-09-01', 'vpisano'),
(212, 92, 9, '2024-09-01', 'vpisano'),
(213, 92, 4, '2024-09-01', 'vpisano'),
(214, 93, 8, '2024-09-01', 'vpisano'),
(215, 93, 10, '2024-09-01', 'vpisano'),
(216, 93, 5, '2024-09-01', 'vpisano'),
(217, 94, 9, '2024-09-01', 'vpisano'),
(218, 94, 1, '2024-09-01', 'vpisano'),
(219, 94, 6, '2024-09-01', 'vpisano'),
(220, 95, 10, '2024-09-01', 'vpisano'),
(221, 95, 2, '2024-09-01', 'vpisano'),
(222, 95, 7, '2024-09-01', 'vpisano'),
(223, 96, 1, '2024-09-01', 'vpisano'),
(224, 96, 3, '2024-09-01', 'vpisano'),
(225, 96, 8, '2024-09-01', 'vpisano'),
(226, 97, 2, '2024-09-01', 'vpisano'),
(227, 97, 4, '2024-09-01', 'vpisano'),
(228, 97, 9, '2024-09-01', 'vpisano'),
(229, 98, 3, '2024-09-01', 'vpisano'),
(230, 98, 5, '2024-09-01', 'vpisano'),
(231, 98, 10, '2024-09-01', 'vpisano'),
(232, 99, 4, '2024-09-01', 'vpisano'),
(233, 99, 6, '2024-09-01', 'vpisano'),
(234, 99, 1, '2024-09-01', 'vpisano'),
(235, 100, 5, '2024-09-01', 'vpisano'),
(236, 100, 7, '2024-09-01', 'vpisano'),
(237, 100, 2, '2024-09-01', 'vpisano'),
(238, 101, 6, '2024-09-01', 'vpisano'),
(239, 101, 8, '2024-09-01', 'vpisano'),
(240, 101, 3, '2024-09-01', 'vpisano'),
(241, 102, 7, '2024-09-01', 'vpisano'),
(242, 102, 9, '2024-09-01', 'vpisano'),
(243, 102, 4, '2024-09-01', 'vpisano'),
(244, 103, 8, '2024-09-01', 'vpisano'),
(245, 103, 10, '2024-09-01', 'vpisano'),
(246, 103, 5, '2024-09-01', 'vpisano'),
(247, 104, 9, '2024-09-01', 'vpisano'),
(248, 104, 1, '2024-09-01', 'vpisano'),
(249, 104, 6, '2024-09-01', 'vpisano'),
(250, 105, 10, '2024-09-01', 'vpisano'),
(251, 105, 2, '2024-09-01', 'vpisano'),
(252, 105, 7, '2024-09-01', 'vpisano'),
(253, 106, 1, '2024-09-01', 'vpisano'),
(254, 106, 3, '2024-09-01', 'vpisano'),
(255, 106, 8, '2024-09-01', 'vpisano'),
(256, 107, 2, '2024-09-01', 'vpisano'),
(257, 107, 4, '2024-09-01', 'vpisano'),
(258, 107, 9, '2024-09-01', 'vpisano'),
(259, 108, 3, '2024-09-01', 'vpisano'),
(260, 108, 5, '2024-09-01', 'vpisano'),
(261, 108, 10, '2024-09-01', 'vpisano'),
(262, 109, 4, '2024-09-01', 'vpisano'),
(263, 109, 6, '2024-09-01', 'vpisano'),
(264, 109, 1, '2024-09-01', 'vpisano'),
(265, 110, 5, '2024-09-01', 'vpisano'),
(266, 110, 7, '2024-09-01', 'vpisano'),
(267, 110, 2, '2024-09-01', 'vpisano'),
(268, 111, 6, '2024-09-01', 'vpisano'),
(269, 111, 8, '2024-09-01', 'vpisano'),
(270, 111, 3, '2024-09-01', 'vpisano'),
(271, 112, 7, '2024-09-01', 'vpisano'),
(272, 112, 9, '2024-09-01', 'vpisano'),
(273, 112, 4, '2024-09-01', 'vpisano'),
(274, 113, 8, '2024-09-01', 'vpisano'),
(275, 113, 10, '2024-09-01', 'vpisano'),
(276, 113, 5, '2024-09-01', 'vpisano'),
(277, 114, 9, '2024-09-01', 'vpisano'),
(278, 114, 1, '2024-09-01', 'vpisano'),
(279, 114, 6, '2024-09-01', 'vpisano'),
(280, 115, 10, '2024-09-01', 'vpisano'),
(281, 115, 2, '2024-09-01', 'vpisano'),
(282, 115, 7, '2024-09-01', 'vpisano'),
(283, 116, 1, '2024-09-01', 'vpisano'),
(284, 116, 3, '2024-09-01', 'vpisano'),
(285, 116, 8, '2024-09-01', 'vpisano'),
(286, 117, 2, '2024-09-01', 'vpisano'),
(287, 117, 4, '2024-09-01', 'vpisano'),
(288, 117, 9, '2024-09-01', 'vpisano'),
(289, 118, 3, '2024-09-01', 'vpisano'),
(290, 118, 5, '2024-09-01', 'vpisano'),
(291, 118, 10, '2024-09-01', 'vpisano'),
(292, 119, 4, '2024-09-01', 'vpisano'),
(293, 119, 6, '2024-09-01', 'vpisano'),
(294, 119, 1, '2024-09-01', 'vpisano'),
(295, 120, 5, '2024-09-01', 'vpisano'),
(296, 120, 7, '2024-09-01', 'vpisano'),
(297, 120, 2, '2024-09-01', 'vpisano'),
(298, 121, 6, '2024-09-01', 'vpisano'),
(299, 121, 8, '2024-09-01', 'vpisano'),
(300, 121, 3, '2024-09-01', 'vpisano'),
(301, 22, 1, '2024-09-01', 'vpisano'),
(302, 22, 2, '2024-09-01', 'vpisano'),
(303, 22, 3, '2024-09-01', 'vpisano'),
(304, 23, 1, '2024-09-01', 'vpisano'),
(305, 23, 4, '2024-09-01', 'vpisano'),
(306, 23, 8, '2024-09-01', 'vpisano'),
(307, 24, 2, '2024-09-01', 'vpisano'),
(308, 24, 5, '2024-09-01', 'vpisano'),
(309, 24, 9, '2024-09-01', 'vpisano'),
(310, 25, 3, '2024-09-01', 'vpisano'),
(311, 25, 6, '2024-09-01', 'vpisano'),
(312, 25, 10, '2024-09-01', 'vpisano'),
(313, 26, 1, '2024-09-01', 'vpisano'),
(314, 26, 7, '2024-09-01', 'vpisano'),
(315, 26, 8, '2024-09-01', 'vpisano'),
(316, 27, 2, '2024-09-01', 'vpisano'),
(317, 27, 4, '2024-09-01', 'vpisano'),
(318, 27, 9, '2024-09-01', 'vpisano'),
(319, 28, 3, '2024-09-01', 'vpisano'),
(320, 28, 5, '2024-09-01', 'vpisano'),
(321, 28, 10, '2024-09-01', 'vpisano'),
(322, 29, 1, '2024-09-01', 'vpisano'),
(323, 29, 6, '2024-09-01', 'vpisano'),
(324, 29, 7, '2024-09-01', 'vpisano'),
(325, 30, 2, '2024-09-01', 'vpisano'),
(326, 30, 8, '2024-09-01', 'vpisano'),
(327, 30, 9, '2024-09-01', 'vpisano'),
(328, 31, 3, '2024-09-01', 'vpisano'),
(329, 31, 4, '2024-09-01', 'vpisano'),
(330, 31, 10, '2024-09-01', 'vpisano');

-- --------------------------------------------------------

--
-- Table structure for table `ucitelji_predmeti`
--

CREATE TABLE `ucitelji_predmeti` (
  `id` int(11) NOT NULL,
  `id_ucitelja` int(11) NOT NULL,
  `id_predmeta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ucitelji_predmeti`
--

INSERT INTO `ucitelji_predmeti` (`id`, `id_ucitelja`, `id_predmeta`) VALUES
(1, 2, 1),
(2, 2, 2),
(3, 3, 3),
(4, 3, 7),
(5, 4, 4),
(6, 4, 5),
(7, 5, 6),
(8, 6, 8),
(9, 6, 9),
(10, 7, 10),
(11, 8, 1),
(12, 8, 4),
(13, 9, 2),
(14, 9, 3),
(15, 10, 5),
(16, 10, 9),
(17, 11, 6),
(18, 11, 7),
(19, 12, 8),
(20, 13, 10),
(21, 14, 1),
(22, 14, 5),
(23, 15, 2),
(24, 15, 6),
(25, 16, 3),
(26, 16, 7),
(27, 17, 4),
(28, 17, 8),
(29, 18, 9),
(30, 18, 10),
(31, 19, 1),
(32, 19, 3),
(33, 20, 2),
(34, 20, 4),
(35, 21, 5),
(36, 21, 6),
(37, 2, 1),
(38, 2, 2),
(39, 3, 3),
(40, 3, 7),
(41, 4, 4),
(42, 4, 5),
(43, 5, 6),
(44, 6, 8),
(45, 6, 9),
(46, 7, 10),
(47, 8, 1),
(48, 8, 4),
(49, 9, 2),
(50, 9, 3),
(51, 10, 5),
(52, 10, 9),
(53, 11, 6),
(54, 11, 7),
(55, 12, 8),
(56, 13, 10),
(57, 14, 1),
(58, 14, 5),
(59, 15, 2),
(60, 15, 6),
(61, 16, 3),
(62, 16, 7),
(63, 17, 4),
(64, 17, 8),
(65, 18, 9),
(66, 18, 10),
(67, 19, 1),
(68, 19, 3),
(69, 20, 2),
(70, 20, 4),
(71, 21, 5),
(72, 21, 6);

-- --------------------------------------------------------

--
-- Table structure for table `uporabniki`
--

CREATE TABLE `uporabniki` (
  `id` int(11) NOT NULL,
  `ime` varchar(45) NOT NULL,
  `priimek` varchar(45) NOT NULL,
  `uporabnisko_ime` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `geslo` varchar(255) NOT NULL,
  `vloga` enum('administrator','ucitelj','ucenec') NOT NULL,
  `datum_registracije` datetime NOT NULL,
  `datum_rojstva` date DEFAULT NULL,
  `status` enum('aktiven','neaktiven') DEFAULT 'aktiven',
  `razred`varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uporabniki`
--

INSERT INTO `uporabniki` (`id`, `ime`, `priimek`, `uporabnisko_ime`, `email`, `geslo`, `vloga`, `datum_registracije`, `datum_rojstva`, `status`,`razred`) VALUES
-- Osebje (Administrator/Učitelji)
(1, 'Admin', 'Sistemski', 'admin', 'admin@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'administrator', '2025-11-10 23:56:50', NULL, 'aktiven','STAFF'), -- Uporabite 'STAFF' ali 'UCITELJI' namesto razreda
(2, 'Tijan', 'Antunovic', 'tantunovic', 'Tijan.Antunovic@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),
(3, 'Valentina', 'Hrastnik', 'vhrastnik', 'Valentina.Hrastnik@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),
(4, 'David', 'Brezovnik', 'dbrezovnik', 'David.Brezovnik@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),
(5, 'Gal', 'Drnovsek', 'gdernovsek', 'Gal.Drnovsek@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),
(6, 'Helena', 'Viher', 'hviher', 'irena.zupan@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),
(7, 'Jaka', 'Decman', 'jdecman', 'Jaka.Decman@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),
(8, 'Matic', 'Fijavz', 'mfijavz', 'tanja.kralj@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),
(9, 'Lubej', 'Bostjan', 'lbostjan', 'Lubej.Bostjan@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),
(10, 'Nik', 'Gorenjec', 'ngorenjec', 'Nik.Gorenjec@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),
(11, 'Mico', 'Hrastnik', 'mhrastnik', 'Mico.Hrastnik@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),
(12, 'Urh', 'Kolar', 'ukolar', 'Urh.Kolar@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),
(13, 'Jost', 'Klancnik', 'jklancnik', 'Jost.Klancnik@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),
(14, 'Luka', 'Janecek', 'ljanecek', 'Luka.Janecek@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),
(15, 'Tim', 'Krusic', 'tkrusic', 'Tim.Krusic@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),
(16, 'Rosana', 'Breznik', 'rbreznik', 'Rosana.Breznik@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),
(17, 'Jaka', 'Koren', 'jkoren', 'Jaka.Koren@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),
(18, 'Andrej', 'Mamedjarovic', 'amamedjarovic', 'Andrej.Mamedjarovic@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),
(19, 'Borut', 'Slemensek', 'bslemensek', 'Borut.Slemensek@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),
(20, 'David', 'Lukman', 'dlukman', 'David.Lukman@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),
(21, 'Miha', 'Stramsak', 'mstramsak', 'Miha.Stramsak@sola.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucitelj', '2025-11-10 23:56:50', NULL, 'aktiven','UCITELJI'),

-- Učenci - r1a (ID 22 do 31)
(22, 'Miha', 'Znidarsic', 'mznidarsic', 'Miha.Znidarsic@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:56:50', NULL, 'aktiven','r1a'),
(23, 'Srecko', 'Kosovel', 'srkosovel', 'Srecko@Kosovel.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:56:50', NULL, 'aktiven','r1a'),
(24, 'Slavko', 'Grum', 'sgrum', 'Slavko.Grum@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:56:50', NULL, 'aktiven','r1a'),
(25, 'Lovro', 'Kuhar', 'lkuhar', 'Lovro.Kuhar@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:56:50', NULL, 'aktiven','r1a'),
(26, 'Franz', 'Kafka', 'fkafka', 'Franz.Kafka@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:56:50', NULL, 'aktiven','r1a'),
(27, 'Anthony', 'Burgess', 'aburgess', 'Anthony.Burgess@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:56:50', NULL, 'aktiven','r1a'),
(28, 'Rebeka', 'Kuang', 'rkuang', 'Rebeka.Kuang@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:56:50', NULL, 'aktiven','r1a'),
(29, 'Janez', 'Golob', 'jgolob', '', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:56:50', NULL, 'aktiven','r1a'),
(30, 'Robert', 'Jansa', 'rjansa', 'Robert.JAnsa@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:56:50', NULL, 'aktiven','r1a'),
(31, 'Slavko', 'Kosovel', 'slkosovel', 'Slavko.Kosovel@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:56:50', NULL, 'aktiven','r1a'),

-- Učenci - r1b (ID 32 do 41)
(32, 'Ana', 'Novak', 'anovak', 'Ana.Novak@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r1b'),
(33, 'Bojan', 'Horvat', 'bhorvat', 'Bojan.Horvat@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r1b'),
(34, 'Cvetka', 'Kovač', 'ckovac', 'Cvetka.Kovac@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r1b'),
(35, 'David', 'Zupan', 'dzupan', 'David.Zupan@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r1b'),
(36, 'Eva', 'Petek', 'epetek', 'Eva.Petek@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r1b'),
(37, 'Franc', 'Mlakar', 'fmlakar', 'Franc.Mlakar@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r1b'),
(38, 'Gregor', 'Vidmar', 'gvidmar', 'Gregor.Vidmar@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r1b'),
(39, 'Helena', 'Kos', 'hkos', 'Helena.Kos@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r1b'),
(40, 'Igor', 'Jerman', 'ijerman', 'Igor.Jerman@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r1b'),
(41, 'Jana', 'Zupančič', 'jzupancic', 'Jana.Zupancic@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r1b'),

-- Učenci - r2a (ID 42 do 51)
(42, 'Klemen', 'Rozman', 'krozman', 'Klemen.Rozman@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2a'),
(43, 'Lara', 'Krajnc', 'lkrajnc', 'Lara.Krajnc@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2a'),
(44, 'Marko', 'Potočnik', 'mpotocnik', 'Marko.Potocnik@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2a'),
(45, 'Nina', 'Koren', 'nkoren', 'Nina.Koren@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2a'),
(46, 'Oskar', 'Hribar', 'ohribar', 'Oskar.Hribar@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2a'),
(47, 'Petra', 'Medved', 'pmedved', 'Petra.Medved@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2a'),
(48, 'Rok', 'Kavčič', 'rkavcic', 'Rok.Kavcic@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2a'),
(49, 'Sara', 'Žnidaršič', 'sznidarsic', 'Sara.Znidarsic@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2a'),
(50, 'Tadej', 'Kotnik', 'tkotnik', 'Tadej.Kotnik@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2a'),
(51, 'Urša', 'Lah', 'ulah', 'Ursa.Lah@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2a'),

-- Učenci - r2b (ID 52 do 61)
(52, 'Vid', 'Erjavec', 'verjavec', 'Vid.Erjavec@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2b'),
(53, 'Zala', 'Furlan', 'zfurlan', 'Zala.Furlan@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2b'),
(54, 'Alen', 'Krajnik', 'akrajnik', 'Alen.Krajnik@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2b'),
(55, 'Blaž', 'Kosir', 'bkosir', 'Blaz.Kosir@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2b'),
(56, 'Cilka', 'Miklavčič', 'cmiklavcic', 'Cilka.Miklavcic@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2b'),
(57, 'Domen', 'Pirc', 'dpirc', 'Domen.Pirc@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2b'),
(58, 'Ema', 'Rupnik', 'erupnik', 'Ema.Rupnik@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2b'),
(59, 'Filip', 'Štrukelj', 'fstrukelj', 'Filip.Strukelj@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2b'),
(60, 'Gašper', 'Tomažič', 'gtomazic', 'Gasper.Tomazic@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2b'),
(61, 'Hana', 'Vidmar', 'hvidmar', 'Hana.Vidmar@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r2b'),

-- Učenci - r3a (ID 62 do 71)
(62, 'Ivan', 'Zajc', 'izajc', 'Ivan.Zajc@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3a'),
(63, 'Jasna', 'Kolar', 'jkolar', 'Jasna.Kolar@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3a'),
(64, 'Kaja', 'Pavlič', 'kpavlic', 'Kaja.Pavlic@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3a'),
(65, 'Luka', 'Kos', 'lkos', 'Luka.Kos@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3a'),
(66, 'Maja', 'Kranjc', 'mkranjc', 'Maja.Kranjc@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3a'),
(67, 'Nejc', 'Kos', 'nkos', 'Nejc.Kos@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3a'),
(68, 'Oskar', 'Krajnc', 'okrajnc', 'Oskar.Krajnc@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3a'),
(69, 'Pia', 'Lah', 'plah', 'Pia.Lah@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3a'),
(70, 'Rene', 'Mlakar', 'rmlakar', 'Rene.Mlakar@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3a'),
(71, 'Sara', 'Novak', 'snovak', 'Sara.Novak@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3a'),

-- Učenci - r3b (ID 72 do 81)
(72, 'Tilen', 'Horvat', 'thorvat', 'Tilen.Horvat@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3b'),
(73, 'Urša', 'Kovač', 'ukovac', 'Ursa.Kovac@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3b'),
(74, 'Vid', 'Zupan', 'vzupan', 'Vid.Zupan@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3b'),
(75, 'Zala', 'Petek', 'zpetek', 'Zala.Petek@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3b'),
(76, 'Aljaž', 'Vidmar', 'avidmar', 'Aljaz.Vidmar@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3b'),
(77, 'Bojan', 'Kos', 'bkos', 'Bojan.Kos@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3b'),
(78, 'Cvetka', 'Jerman', 'cjerman', 'Cvetka.Jerman@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3b'),
(79, 'Domen', 'Zupančič', 'dzupancic', 'Domen.Zupancic@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3b'),
(80, 'Eva', 'Rozman', 'erozman', 'Eva.Rozman@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3b'),
(81, 'Filip', 'Krajnc', 'fkrajnc', 'Filip.Krajnc@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r3b'),

-- Učenci - r4a (ID 82 do 91)
(82, 'Gašper', 'Potočnik', 'gpotocnik', 'Gasper.Potocnik@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4a'),
(83, 'Hana', 'Koren', 'hkoren', 'Hana.Koren@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4a'),
(84, 'Ivan', 'Hribar', 'ihribar', 'Ivan.Hribar@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4a'),
(85, 'Jana', 'Medved', 'jmedved', 'Jana.Medved@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4a'),
(86, 'Kaja', 'Kavčič', 'kkavcic', 'Kaja.Kavcic@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4a'),
(87, 'Luka', 'Žnidaršič', 'lznidarsic', 'Luka.Znidarsic@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4a'),
(88, 'Maja', 'Kotnik', 'mkotnik', 'Maja.Kotnik@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4a'),
(89, 'Nejc', 'Lah', 'nlah', 'Nejc.Lah@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4a'),
(90, 'Oskar', 'Erjavec', 'oerjavec', 'Oskar.Erjavec@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4a'),
(91, 'Pia', 'Furlan', 'pfurlan', 'Pia.Furlan@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4a'),

-- Učenci - r4b (ID 92 do 101)
(92, 'Rene', 'Krajnik', 'rkrajnik', 'Rene.Krajnik@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4b'),
(93, 'Sara', 'Kosir', 'skosir', 'Sara.Kosir@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4b'),
(94, 'Tilen', 'Miklavčič', 'tmiklavcic', 'Tilen.Miklavcic@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4b'),
(95, 'Urša', 'Pirc', 'upirc', 'Ursa.Pirc@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4b'),
(96, 'Vid', 'Rupnik', 'vrupnik', 'Vid.Rupnik@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4b'),
(97, 'Zala', 'Štrukelj', 'zstrukelj', 'Zala.Strukelj@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4b'),
(98, 'Alen', 'Tomažič', 'atomazic', 'Alen.Tomazic@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4b'),
(99, 'Blaž', 'Vidmar', 'bvidmar', 'Blaz.Vidmar@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4b'),
(100, 'Cilka', 'Zajc', 'czajc', 'Cilka.Zajc@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4b'),
(101, 'Domen', 'Kolar', 'dkolar', 'Domen.Kolar@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','r4b'),

-- Učenci - Izredni programi (e1a do e4a)
(102, 'Ema', 'Pavlič', 'epavlic', 'Ema.Pavlic@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e1a'),
(103, 'Filip', 'Kos', 'fkos', 'Filip.Kos@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e1a'),
(104, 'Gašper', 'Kranjc', 'gkranjc', 'Gasper.Kranjc@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e1a'),
(105, 'Hana', 'Kos', 'hkos2', 'Hana.Kos2@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e1a'),
(106, 'Ivan', 'Krajnc', 'ikrajnc', 'Ivan.Krajnc@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e2a'),
(107, 'Jasna', 'Lah', 'jlah', 'Jasna.Lah@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e2a'),
(108, 'Kaja', 'Mlakar', 'kmlakar', 'Kaja.Mlakar@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e2a'),
(109, 'Luka', 'Novak', 'lnovak', 'Luka.Novak@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e2a'),
(110, 'Maja', 'Horvat', 'mhorvat', 'Maja.Horvat@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e3a'),
(111, 'Nejc', 'Kovač', 'nkovac', 'Nejc.Kovac@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e3a'),
(112, 'Oskar', 'Zupan', 'ozupan', 'Oskar.Zupan@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e3a'),
(113, 'Pia', 'Petek', 'ppetek', 'Pia.Petek@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e3a'),
(114, 'Rene', 'Vidmar', 'rvidmar', 'Rene.Vidmar@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e4a'),
(115, 'Sara', 'Kos', 'skos2', 'Sara.Kos2@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e4a'),
(116, 'Tilen', 'Jerman', 'tjerman', 'Tilen.Jerman@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e4a'),
(117, 'Urša', 'Zupančič', 'uzupancic', 'Ursa.Zupancic@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e4a'),
(118, 'Vid', 'Rozman', 'vrozman', 'Vid.Rozman@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e4a'),
(119, 'Zala', 'Krajnc', 'zkrajnc', 'Zala.Krajnc@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e4a'),
(120, 'Aljaž', 'Potočnik', 'apotocnik', 'Aljaz.Potocnik@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e4a'),
(121, 'Bojan', 'Koren', 'bkoren', 'Bojan.Koren@dijak.si', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-10 23:57:15', NULL, 'aktiven','e4a'),
(122, 'tera', 'fiausch', 'tfia', 'squeak.kissy76@gmail.com', '$2y$10$VMxg9nTOlIARHKvRPhNiWOKsTMHqB7JBO2FBP3Km0AtMgFcDM1wJi', 'ucenec', '2025-11-11 00:01:06', '2025-11-06', 'aktiven','e4a');
--
-- Indexes for dumped tables
--

--
-- Indexes for table `gradiva`
--
ALTER TABLE `gradiva`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_predmeta` (`id_predmeta`),
  ADD KEY `id_avtorja` (`id_avtorja`);

--
-- Indexes for table `naloge`
--
ALTER TABLE `naloge`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_predmeta` (`id_predmeta`),
  ADD KEY `id_avtorja` (`id_avtorja`);

--
-- Indexes for table `ocene`
--
ALTER TABLE `ocene`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ucenca` (`id_ucenca`),
  ADD KEY `id_naloge` (`id_naloge`);

--
-- Indexes for table `oddaje`
--
ALTER TABLE `oddaje`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_naloge` (`id_naloge`),
  ADD KEY `id_ucenca` (`id_ucenca`);

--
-- Indexes for table `pravice`
--
ALTER TABLE `pravice`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_uporabnika` (`id_uporabnika`);

--
-- Indexes for table `predmeti`
--
ALTER TABLE `predmeti`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `koda` (`koda`);

--
-- Indexes for table `ucenci_predmeti`
--
ALTER TABLE `ucenci_predmeti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ucenca` (`id_ucenca`),
  ADD KEY `id_predmeta` (`id_predmeta`);

--
-- Indexes for table `ucitelji_predmeti`
--
ALTER TABLE `ucitelji_predmeti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ucitelja` (`id_ucitelja`),
  ADD KEY `id_predmeta` (`id_predmeta`);

--
-- Indexes for table `uporabniki`
--
ALTER TABLE `uporabniki`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uporabnisko_ime` (`uporabnisko_ime`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gradiva`
--
ALTER TABLE `gradiva`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `naloge`
--
ALTER TABLE `naloge`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ocene`
--
ALTER TABLE `ocene`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `oddaje`
--
ALTER TABLE `oddaje`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pravice`
--
ALTER TABLE `pravice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `predmeti`
--
ALTER TABLE `predmeti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ucenci_predmeti`
--
ALTER TABLE `ucenci_predmeti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=331;

--
-- AUTO_INCREMENT for table `ucitelji_predmeti`
--
ALTER TABLE `ucitelji_predmeti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `uporabniki`
--
ALTER TABLE `uporabniki`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gradiva`
--
ALTER TABLE `gradiva`
  ADD CONSTRAINT `gradiva_ibfk_1` FOREIGN KEY (`id_predmeta`) REFERENCES `predmeti` (`id`),
  ADD CONSTRAINT `gradiva_ibfk_2` FOREIGN KEY (`id_avtorja`) REFERENCES `uporabniki` (`id`);

--
-- Constraints for table `naloge`
--
ALTER TABLE `naloge`
  ADD CONSTRAINT `naloge_ibfk_1` FOREIGN KEY (`id_predmeta`) REFERENCES `predmeti` (`id`),
  ADD CONSTRAINT `naloge_ibfk_2` FOREIGN KEY (`id_avtorja`) REFERENCES `uporabniki` (`id`);

--
-- Constraints for table `oddaje`
--
ALTER TABLE `oddaje`
  ADD CONSTRAINT `oddaje_ibfk_1` FOREIGN KEY (`id_naloge`) REFERENCES `naloge` (`id`),
  ADD CONSTRAINT `oddaje_ibfk_2` FOREIGN KEY (`id_ucenca`) REFERENCES `uporabniki` (`id`);

--
-- Constraints for table `ucenci_predmeti`
--
ALTER TABLE `ucenci_predmeti`
  ADD CONSTRAINT `ucenci_predmeti_ibfk_1` FOREIGN KEY (`id_ucenca`) REFERENCES `uporabniki` (`id`),
  ADD CONSTRAINT `ucenci_predmeti_ibfk_2` FOREIGN KEY (`id_predmeta`) REFERENCES `predmeti` (`id`);

--
-- Constraints for table `ucitelji_predmeti`
--
ALTER TABLE `ucitelji_predmeti`
  ADD CONSTRAINT `ucitelji_predmeti_ibfk_1` FOREIGN KEY (`id_ucitelja`) REFERENCES `uporabniki` (`id`),
  ADD CONSTRAINT `ucitelji_predmeti_ibfk_2` FOREIGN KEY (`id_predmeta`) REFERENCES `predmeti` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
