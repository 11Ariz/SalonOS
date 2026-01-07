-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 07, 2026 at 06:51 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `salon_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `appointment_date` datetime DEFAULT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `client_id`, `user_id`, `service_id`, `appointment_date`, `status`) VALUES
(1, NULL, 3, 2, '2026-01-08 18:00:00', 'completed'),
(2, NULL, 5, 1, '2026-01-07 19:17:00', 'completed'),
(3, 1, NULL, 2, '2026-01-08 18:40:00', 'completed'),
(5, NULL, 6, 2, '2026-01-08 07:44:00', 'completed'),
(6, NULL, 8, 2, '2026-01-07 21:03:00', 'completed'),
(7, NULL, 8, 1, '2026-01-08 09:11:00', 'completed'),
(8, NULL, 4, 2, '2026-01-08 21:26:00', 'completed'),
(9, NULL, 4, 2, '2026-01-08 21:26:00', 'completed'),
(13, NULL, 4, 2, '2026-01-08 21:26:00', 'completed'),
(14, NULL, 4, 2, '2026-01-08 21:26:00', 'completed'),
(15, NULL, 4, 2, '2026-01-08 21:26:00', 'completed'),
(16, NULL, 4, 2, '2026-01-08 21:26:00', 'completed'),
(17, NULL, 4, 2, '2026-01-08 21:26:00', 'completed'),
(18, 3, 2, 1, '2026-01-07 22:29:00', 'completed'),
(19, NULL, 8, NULL, '2026-01-07 21:47:21', 'completed'),
(20, 3, 2, 1, '2026-01-08 22:13:00', 'scheduled'),
(21, 3, 4, 2, '2026-01-08 23:01:00', 'scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `first_name`, `last_name`, `phone`, `email`) VALUES
(1, 'cus1', '', '1234567890', 'cus1@gmail.com'),
(2, 'cus2', '', '7894561230', 'cus2@gmail.com'),
(3, 'cus3', '', '1254789630', 'arizmujawar.am@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `commissions`
--

CREATE TABLE `commissions` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `stylist_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `item_name` varchar(100) DEFAULT NULL,
  `stock_level` int(11) DEFAULT NULL,
  `min_threshold` int(11) DEFAULT NULL,
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `is_backbar` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `item_name`, `stock_level`, `min_threshold`, `unit_cost`, `is_backbar`) VALUES
(1, 'shampoo', 9, 2, 1200.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `service_name` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `price`) VALUES
(1, 'Facial', 1500.00),
(2, 'Haircut', 500.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `role` enum('admin','stylist','customer') DEFAULT 'customer',
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `role`, `email`, `phone`, `password`) VALUES
(1, 'Jane Doe', 'stylist', 'jane@salon.com', NULL, ''),
(2, 'Ariz Mujawar', 'stylist', 'ariz@gmail.com', NULL, '$2y$10$ij8l/Kbw3jt5JtuK88v4Qeth3ZH0ilrRiuds/aKuEUgG.SSrlJpfK'),
(3, 'Ariz', 'customer', '11arizpc@gmail.com', '8830508219', '$2y$10$2gH0lrvCsKCFHioA7ANOpOkKoxtMhxDEOtQqqCSPOvzHVPJ8NIETC'),
(4, 'staff1', 'stylist', 'staff1@gmail.com', NULL, '$2y$10$bEYJHgyhwaHzYaH3OeAFEuI8004NoPI5vUrralbWypRvgv9UvBwE6'),
(5, 'cus1', 'customer', 'cus1@gmail.com', '1234567890', '$2y$10$bVM57gikOxxySCtUp4zkbu7k8T/YQpjgXZc3GngXCl87G3yce39nC'),
(6, 'cus2', 'customer', 'cus2@gmail.com', '7894561230', '$2y$10$Z5juKmbX61yIubK5kx68CuEAMYvaK8cTSbahyVkYBOvJpsm5c6tSS'),
(8, 'cus3', 'customer', 'arizmujawar.am@gmail.com', '1254789630', '$2y$10$qTbE99ybarUl8EONDAwbHOJp1gbJBv/OBlo68DPtPAeOMUZd4mKvi');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `commissions`
--
ALTER TABLE `commissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `stylist_id` (`stylist_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `commissions`
--
ALTER TABLE `commissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

--
-- Constraints for table `commissions`
--
ALTER TABLE `commissions`
  ADD CONSTRAINT `commissions_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`),
  ADD CONSTRAINT `commissions_ibfk_2` FOREIGN KEY (`stylist_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
