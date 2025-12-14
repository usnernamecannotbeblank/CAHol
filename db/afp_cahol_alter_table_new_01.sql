-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2025. Nov 04. 21:18
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `afp_cahol`
--

-- --------------------------------------------------------

INSERT INTO `felhasznalok` (`dolg_id`, `nev`, `jelszo`, `jogosultsag`, `osztaly_id`, `foto_d_url`, `email`) VALUES
(6, 'Nagy Gergő', '5ea01af357a91827b995e24ae7a8baa8', 'user', '', '', 'nagy.g3rg0@gmail.com'),
(7, 'Teszt Elek', 'd95151107f8886dcef1fe2f0963a681f', 'user', '', '', 'teszt@gmail.com');

INSERT INTO `kinel_van` (`id`, `dolg_id`, `rendszam`) VALUES
(7, 2, 'NVS-540');

--
-- AUTO_INCREMENT for table `felhasznalok`
--
ALTER TABLE `felhasznalok`
  MODIFY `dolg_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;