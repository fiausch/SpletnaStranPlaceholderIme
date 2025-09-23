-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2025 at 05:20 PM
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
-- Database: `placeholderime`
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

-- --------------------------------------------------------

--
-- Table structure for table `ucitelji_predmeti`
--

CREATE TABLE `ucitelji_predmeti` (
  `id` int(11) NOT NULL,
  `id_ucitelja` int(11) NOT NULL,
  `id_predmeta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `status` enum('aktiven','neaktiven') DEFAULT 'aktiven'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indexes for table `oddaje`
--
ALTER TABLE `oddaje`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_naloge` (`id_naloge`),
  ADD KEY `id_ucenca` (`id_ucenca`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `naloge`
--
ALTER TABLE `naloge`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `oddaje`
--
ALTER TABLE `oddaje`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `predmeti`
--
ALTER TABLE `predmeti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ucenci_predmeti`
--
ALTER TABLE `ucenci_predmeti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ucitelji_predmeti`
--
ALTER TABLE `ucitelji_predmeti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `uporabniki`
--
ALTER TABLE `uporabniki`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
