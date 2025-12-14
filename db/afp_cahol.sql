-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2025 at 08:45 PM
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
-- Database: `afp_cahol`
--

-- --------------------------------------------------------

--
-- Table structure for table `autok`
--

CREATE TABLE `autok` (
  `rendszam` varchar(15) NOT NULL,
  `tip_id` int(10) NOT NULL,
  `uzemanyag` varchar(10) NOT NULL,
  `szin` varchar(20) NOT NULL,
  `beszerzes` date NOT NULL,
  `foto_url` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `autok`
--

INSERT INTO `autok` (`rendszam`, `tip_id`, `uzemanyag`, `szin`, `beszerzes`, `foto_url`) VALUES
('HTP - 142', 13, 'Dízel', 'Piros', '2020-08-05', '693c63a13ffd0_ford_red.jpg'),
('IRU - 740', 12, 'Benzin', 'Szürke', '2002-05-10', '693c62045ead6_skoda_grey.jpg'),
('LDP - 858', 14, 'Benzin', 'Piros', '2008-11-09', '693c63ed48891_suzuki_red.jpg'),
('LTG - 637', 12, 'Benzin', 'Kék', '2017-11-06', '693c628ecf939_skoda_blue.jpg'),
('PHP - 008', 16, 'Benzin', 'Fekete', '2009-02-28', '693c65929ee70_volkswagen_black.jpg'),
('RGS - 786', 15, 'Benzin', 'Szürke', '2005-10-07', '693c64feccc9b_renault_grey.jpg'),
('USG - 692', 1, 'Benzin', 'Fekete', '2015-04-01', '693c62291e9be_opel_black.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `auto_tipus`
--

CREATE TABLE `auto_tipus` (
  `tip_id` int(10) NOT NULL,
  `marka` varchar(50) NOT NULL,
  `tipus` varchar(50) NOT NULL,
  `felepitmeny` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auto_tipus`
--

INSERT INTO `auto_tipus` (`tip_id`, `marka`, `tipus`, `felepitmeny`) VALUES
(1, 'Opel', 'Corsa', '5 ajtós'),
(12, 'Skoda', 'Octavia', '5 ajtós'),
(13, 'Ford', 'Fiesta', '3 ajtós'),
(14, 'Suzuki', 'Swift', '5 ajtós'),
(15, 'Renault', 'Clio', '3 ajtós'),
(16, 'Volkswagen', 'Golf', '5 ajtós');

-- --------------------------------------------------------

--
-- Table structure for table `felhasznalok`
--

CREATE TABLE `felhasznalok` (
  `dolg_id` int(10) NOT NULL,
  `nev` varchar(50) NOT NULL,
  `jelszo` varchar(32) NOT NULL,
  `jogosultsag` varchar(5) NOT NULL DEFAULT 'user',
  `osztaly_id` varchar(10) NOT NULL,
  `foto_d_url` varchar(200) NOT NULL,
  `email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `felhasznalok`
--

INSERT INTO `felhasznalok` (`dolg_id`, `nev`, `jelszo`, `jogosultsag`, `osztaly_id`, `foto_d_url`, `email`) VALUES
(1, 'Admin', '7fd4e0ed6c1c2bad7e4802576f2141e0', 'admin', 'gazd', '', ''),
(2, 'Tóth László', '7fd4e0ed6c1c2bad7e4802576f2141e0', 'user', 'gazd', '', 'latohu@hotmail.com'),
(5, 'Tóth Ádám', '7fd4e0ed6c1c2bad7e4802576f2141e0', 'user', 'term', '', ''),
(8, 'Kovács László', '7fd4e0ed6c1c2bad7e4802576f2141e0', 'user', 'igaz', '', 'laszlo.kovacs@gmail.com'),
(9, 'Nagy Ágnes', '7fd4e0ed6c1c2bad7e4802576f2141e0', 'user', '', '', 'agnes.nagy@gmail.com'),
(10, 'Szabó Péter', '7fd4e0ed6c1c2bad7e4802576f2141e0', 'user', '', '', 'peter.szabo@gmail.com'),
(11, 'Tóth Katalin', '7fd4e0ed6c1c2bad7e4802576f2141e0', 'user', '', '', 'katalin.toth@gmail.com'),
(12, 'Varga Gábor', '7fd4e0ed6c1c2bad7e4802576f2141e0', 'user', '', '', 'gabor.varga@gmail.com'),
(13, 'Horváth Zsuzsanna', '7fd4e0ed6c1c2bad7e4802576f2141e0', 'user', '', '', 'zsuzsanna.horvath@gmail.com'),
(14, 'Nagy Gergő', '7fd4e0ed6c1c2bad7e4802576f2141e0', 'user', 'igaz', '', 'nagy.g3rg0@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `kinel_van`
--

CREATE TABLE `kinel_van` (
  `id` int(10) NOT NULL,
  `dolg_id` int(10) NOT NULL,
  `rendszam` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `osztalyok`
--

CREATE TABLE `osztalyok` (
  `osztaly_id` varchar(10) NOT NULL,
  `osztaly_nev` varchar(50) NOT NULL,
  `leiras` varchar(200) DEFAULT NULL,
  `vezeto` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `osztalyok`
--

INSERT INTO `osztalyok` (`osztaly_id`, `osztaly_nev`, `leiras`, `vezeto`) VALUES
('besz', 'Beszerzési osztály', NULL, NULL),
('gazd', 'Gazdasági osztály', NULL, NULL),
('igaz', 'Igazgatósági osztály', '', 'Rejtő Jenő'),
('kont', 'Kontrolling osztály', '', ''),
('term', 'Termelési osztály', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `telephely`
--

CREATE TABLE `telephely` (
  `tlph_id` int(10) NOT NULL,
  `telephely_nev` varchar(50) NOT NULL,
  `cim` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `telephely`
--

INSERT INTO `telephely` (`tlph_id`, `telephely_nev`, `cim`) VALUES
(1, 'Huszti', '1032 Budapest\r\nHuszti út 60'),
(2, 'Szada', 'Szada\r\nIpari park 1'),
(3, 'Polgár', '1033 Budapest, Polgár u. 6-10. '),
(4, 'Bécsi', '1032 Budapest, Bécsi út 166-168. ');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `autok`
--
ALTER TABLE `autok`
  ADD PRIMARY KEY (`rendszam`),
  ADD KEY `tip_id` (`tip_id`);

--
-- Indexes for table `auto_tipus`
--
ALTER TABLE `auto_tipus`
  ADD PRIMARY KEY (`tip_id`);

--
-- Indexes for table `felhasznalok`
--
ALTER TABLE `felhasznalok`
  ADD PRIMARY KEY (`dolg_id`);

--
-- Indexes for table `kinel_van`
--
ALTER TABLE `kinel_van`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dolg_id` (`dolg_id`),
  ADD KEY `rendszam` (`rendszam`);

--
-- Indexes for table `osztalyok`
--
ALTER TABLE `osztalyok`
  ADD PRIMARY KEY (`osztaly_id`);

--
-- Indexes for table `telephely`
--
ALTER TABLE `telephely`
  ADD PRIMARY KEY (`tlph_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auto_tipus`
--
ALTER TABLE `auto_tipus`
  MODIFY `tip_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `felhasznalok`
--
ALTER TABLE `felhasznalok`
  MODIFY `dolg_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `kinel_van`
--
ALTER TABLE `kinel_van`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `telephely`
--
ALTER TABLE `telephely`
  MODIFY `tlph_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `autok`
--
ALTER TABLE `autok`
  ADD CONSTRAINT `autok_ibfk_1` FOREIGN KEY (`tip_id`) REFERENCES `auto_tipus` (`tip_id`) ON UPDATE CASCADE;

--
-- Constraints for table `kinel_van`
--
ALTER TABLE `kinel_van`
  ADD CONSTRAINT `kinel_van_ibfk_1` FOREIGN KEY (`dolg_id`) REFERENCES `felhasznalok` (`dolg_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kinel_van_ibfk_2` FOREIGN KEY (`rendszam`) REFERENCES `autok` (`rendszam`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
