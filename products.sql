-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : sql105.infinityfree.com
-- Généré le :  Dim 29 mars 2026 à 18:00
-- Version du serveur :  11.4.10-MariaDB
-- Version de PHP :  7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `if0_40174223_gpower_bd`
--

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `specifications` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `main_image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `specifications`, `price`, `location`, `quantity`, `main_image`, `status`, `featured`, `created_at`, `updated_at`) VALUES
(19, 'Siemens SST 400, 30MW', '-iemens-400-30-', 'Manufacturer: Siemens\nModel: SST 400\nYear of manufacturing: 2015\nCondition: New\nWattage: 30 MW\nHours: 00\nFrequency: 50 hz\nFuel type: Natural gas\nVoltage: 11500 V', '2300000.00', 'Asia', 8, 'prod_1766945452_695172ac3166b.png', 'active', 1, '2025-12-28 18:10:52', '2026-02-21 06:16:31'),
(20, 'Cummins GQSK60, 1500 KW', '-ummins-60-1500-', 'Manufacturer: Cummins\nModel: GQSK60\nYear of manufacturing: 2015\nCondition: Used\nWattage: 1500 KW\nHours: 20\nFrequency: 50 hz\nFuel type: Natural gas\nVoltage: 10.5 KV', '380994.00', 'China', 1, 'prod_1766992372_695229f4dc23b.jpg', 'active', 1, '2025-12-29 07:12:52', '2026-02-21 06:15:55'),
(21, 'MWM TCG2020V20', '-2020-20', 'Manufacturer: MWM\nModel: TCG2020V20\nYear of manufacturing: 2015\nCondition: Used\nWattage: 2000 KW\nHours: 60000-80000\nFrequency: 50 hz\nFuel type: Natural gas\nVoltage: 10.5 KV', '501.00', 'China', 5, 'prod_1766993024_69522c807a925.jpg', 'active', 1, '2025-12-29 07:23:44', '2025-12-29 07:23:44'),
(22, 'Caterpillar CG170-12, 1200 KW', '-aterpillar-170-12-1200-', 'Manufacturer: Caterpillar\nModel: CG170-12\nYear of manufacturing: 2017\nCondition: Used\nWattage: 1200 KW\nHours: 147\nFrequency: 50 hz\nFuel type: Natural Gas\nVoltage: 10.5 KV', '269005.00', 'China', 1, 'prod_1766993894_69522fe6a9a13.jpg', 'active', 1, '2025-12-29 07:38:14', '2026-02-21 06:15:20'),
(23, 'Janbacher J620, 3800 KW', '-anbacher-620-3800-', 'Manufacturer: Janbacher\nModel: J620\nYear of manufacturing: 2023-2024\nCondition: New\nWattage: 3800 KW\nHours: 0\nFrequency: 50 hz\nFuel type: Natural gas\nVoltage: 10500', '1386730.00', 'China', 2, 'prod_1767464360_69595da8b04ee.jpg', 'active', 1, '2026-01-03 18:19:20', '2026-02-21 06:14:47'),
(24, 'Rolls-Royce B33, 3600 KW', '-olls-oyce-33-3600-', 'Manufacturer: Rolls-Royce\nModel: B33\nYear of manufacturing: 2017\nCondition: New\nWattage: 3600 KW\nHours: O\nFrequency: 50 hz\nFuel type: Heavy oil, tire oil, diesel\nVoltage: 10000', '550000.00', 'China', 1, 'prod_1767464985_6959601949699.jpg', 'active', 1, '2026-01-03 18:29:45', '2026-02-21 06:14:21'),
(25, 'CAT 3516B, 997 KW', '-3516-997-', 'Manufacturer: Caterpillar\nModel: 3512B\nYear of manufacturing: 2016-2018\nCondition: New\nWattage: 997 KW\nHours: 0\nFrequency: 60 hz\nFuel type: Diesel\nVoltage: 600', '600200.00', 'China', 9, 'prod_1767928366_6960722ebc21e.jpg', 'active', 1, '2026-01-09 03:11:52', '2026-02-21 06:13:45'),
(26, 'CAT 3516B, 2000 KVA', '-3516-2000-', 'Manufacturer: Caterpillar\nModel: 3516B\nYear of manufacturing: 2018\nCondition: Used\nWattage: 2000 KVA\nHours: 369-1899\nFrequency: 50 hz\nFuel type: Diesel\nVoltage: 400 V', '340000.00', 'Japan', 80, 'prod_1767928703_6960737f6c77c.jpg', 'active', 1, '2026-01-09 03:18:23', '2026-02-21 06:09:57'),
(28, 'Jenbacher J624, 4400 KW', '-enbacher-624-4400-', 'Manufacturer: Jenbacher\nModel: J624\nYear of manufacturing: 2017\nCondition: New\nWattage: 4400 KW\nHours: 0\nFrequency: 50 hz\nFuel type: Natural Gas\nVoltage: 10,5 KV', '1478627.00', 'China', 2, 'prod_1768821502_696e12fe65151.jpg', 'active', 1, '2026-01-19 11:18:22', '2026-02-21 06:13:11'),
(34, 'Jenbacher J616, 2700 KW', '-enbacher-616-2700-', 'Manufacturer: Janbacher\nModel: J616\nYear of manufacturing: 2022\nCondition: Used\nWattage: 2700 KW\nHours: 12154\nFrequency: 50 hz\nFuel type: Natural Gas\nVoltage: 10,5 KV', '1480000.00', 'Pakistan', 2, 'prod_1768822134_696e1576f335c.jpg', 'active', 1, '2026-01-19 11:27:52', '2026-02-21 06:12:15'),
(35, 'MWM TCG 2020V16, 1500 KW', '-2020-16-1500-', 'Manufacturer: MWM\nModel: TCG 2020V16\nYear of manufacturing: 2015\nCondition: New\nWattage: 1500 KW\nHours: 0\nFrequency: 50 hz\nFuel type: Natural Gas\nVoltage: 10,5 KV', '267954.00', 'China', 1, 'prod_1768822504_696e16e80e14e.jpg', 'active', 1, '2026-01-19 11:35:04', '2026-02-21 06:12:44'),
(36, 'CAT G3516, 1094KW', '-3516-1094-', 'Manufacturer: Caterpillar\nModel: G3516\nYear of manufacturing: 2015\nCondition: Used\nWattage: 1094 KW\nHours: 30000-40000\nFrequency: 50 hz\nFuel type: Biogas\nVoltage: 10,5 KV', '145000.00', 'China', 4, 'prod_1769950693_697f4de5c0ecc.jpg', 'active', 1, '2026-02-01 12:58:13', '2026-02-21 06:11:30'),
(37, 'WARTSILA W20V32 9MW', '-20-32-9-', 'Manufacturer: WARTSILA\nModel: W20V32\nYear of manufacturing: 2011\nCondition: Used\nWattage: 9 MW\nHours: 30000-36000\nFrequency: 50 hz\nFuel type: Natural Gas\nVoltage: 10,5 KV', '1675000.00', 'Vietnam', 6, 'prod_1769980959_697fc41fb3f27.jpg', 'active', 1, '2026-02-01 21:22:39', '2026-02-01 21:22:39'),
(38, 'CAT G3520C, 1950 KW', '-3520-1950-', 'Manufacturer: Caterpillar\nModel: G3520C\nYear of manufacturing: 2007\nCondition: New\nWattage: 1950 KW\nHours: 0\nFrequency: 50 hz\nFuel type: Natural Gas\nVoltage: 11 KV', '341429.00', 'Pakistan', 3, 'prod_1769981306_697fc57a8e01b.jpg', 'active', 1, '2026-02-01 21:28:26', '2026-02-21 06:08:29'),
(39, 'MWM TCG2020V12, 1200 KW', '-2020-12-1200-', 'Manufacturer: MWM\nModel: TCG2020V12\nYear of manufacturing: 2014\nCondition: New\nWattage: 1200 KW\nHours: 0\nFrequency: 50 hz\nFuel type: Biogas/Natural Gas\nVoltage: 10,5 KV', '268617.00', 'China', 1, 'prod_1770148444_6982525c56c92.jpg', 'active', 1, '2026-02-03 19:54:04', '2026-02-21 06:07:53'),
(40, 'MTU 20V4000, 2000 KW', '-20-4000-2000-', 'Manufacturer: MTU\nModel: 20V4000\nYear of manufacturing: 2019\nCondition: Used\nWattage: 2000 KW\nHours: 20000\nFrequency: 50 hz\nFuel type: Natural Gas\nVoltage: 10,5 KV', '332000.00', 'China', 3, 'prod_1770273960_69843ca8cb6ab.jpg', 'active', 1, '2026-02-05 06:46:00', '2026-02-20 21:04:38'),
(41, 'MWM TCG2020 V16, 1560 kW', '-2020-16-1560-k-', 'Manufacturer: MWM\nModel: TCG2020V16\nYear of manufacturing: 2024\nCondition: New\nWattage: 1560\nHours: 0\nFrequency: 50 hz\nFuel type: Natural Gas\nVoltage: 400 V', '562925.00', 'Lithuania', 1, 'prod_1771619890_6998c6329aea0.jpeg', 'active', 1, '2026-02-20 20:38:10', '2026-02-20 20:38:10'),
(42, 'MWM TCG3020 V20,  2300 kW', '-3020-20-2300-k-', 'Manufacturer: MWM\nModel: TCG3020 V20\nYear of manufacturing: 2025\nCondition: New\nWattage: 2300 KW\nHours: 0\nFrequency: 50 hz\nFuel type: Natural Gas\nVoltage: 10500 V', '886000.00', 'Lithuania', 1, 'prod_1771621172_6998cb345a2ac.jpeg', 'active', 1, '2026-02-20 20:59:32', '2026-02-20 21:00:33'),
(43, 'Janbacher J320 1050KW', '-anbacher-320-1050-', 'Manufacturer: Janbacher\nModel: J320\nYear of manufacturing: 2010\nCondition: Used\nWattage: 1050 KW\nHours: 33000\nFrequency: 50 hz\nFuel type: biogas/natural gas\nVoltage: 10,5 KV', '133.33', 'China', 3, 'prod_1772483385_69a5f33994ef6.jpg', 'active', 1, '2026-03-02 20:29:45', '2026-03-02 20:29:45'),
(44, 'Janbacher J620 3300KW', '-anbacher-620-3300-', 'Manufacturer: Janbacher\nModel: J620\nYear of manufacturing: 2017\nCondition: Used\nWattage: 3300KW\nHours: 20000\nFrequency: 50 hz\nFuel type: Natural Gas\nVoltage: 10,5 KV', '539532.00', 'Pakistan', 4, 'prod_1772896752_69ac41f0d2590.jpg', 'active', 1, '2026-03-07 15:19:13', '2026-03-07 15:19:13'),
(45, 'Janbacher J320 1000KW', '-anbacher-320-1000-', 'Manufacturer: Janbacher\nModel: J320\nYear of manufacturing: 2020\nCondition: New\nWattage: 1000 KW\nHours: 0\nFrequency: 50 hz\nFuel type: Natural gas\nVoltage: 10,5 KV', '253652.00', 'China', 3, 'prod_1773205110_69b0f6761bbd8.jpg', 'active', 1, '2026-03-11 04:58:30', '2026-03-11 04:58:30'),
(46, 'Janbacher J620 3000KW', '-anbacher-620-3000-', 'Manufacturer: Janbacher\nModel: J620\nYear of manufacturing: 2016\nCondition: Used\nWattage: 3000 KW\nHours: 15000\nFrequency: 50 hz\nFuel type: Natural gas\nVoltage: 10,5 KV', '1229344.00', 'China', 2, 'prod_1773947835_69bc4bbb7c90a.jpg', 'active', 1, '2026-03-19 19:17:15', '2026-03-19 19:17:15'),
(47, 'MWM TCG2020V12, 4MW', '-2020-12-4-', 'Manufacturer: MWM\nModel: TCG2020V32\nYear of manufacturing: 2010\nCondition: Used\nWattage: 4 MW\nHours: 45000\nFuel type: Natural gas\nVoltage: 11000 V', '700000.00', 'Bangladesh', 14, '', 'active', 1, '2026-03-29 17:28:37', '2026-03-29 17:28:37');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_featured` (`featured`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
