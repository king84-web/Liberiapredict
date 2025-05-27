-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2025 at 02:36 PM
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
-- Database: `buying_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `buy`
--

CREATE TABLE `buy` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buy`
--

INSERT INTO `buy` (`id`, `customer_id`, `product_id`) VALUES
(1, 1, 1),
(2, 1, 3),
(3, 1, 5),
(4, 2, 2),
(5, 2, 4),
(6, 2, 10),
(7, 3, 6),
(8, 3, 8),
(9, 4, 7),
(10, 4, 9),
(11, 4, 11),
(12, 4, 12),
(13, 5, 13),
(14, 5, 14),
(15, 6, 1),
(16, 6, 15),
(17, 7, 2),
(18, 7, 3),
(19, 7, 4),
(20, 7, 5),
(21, 8, 6),
(22, 8, 7),
(23, 8, 8);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `user_name`, `email`, `phone`, `password`) VALUES
(1, 'Aimecol', 'aimecol@gmail.com', '0789375245', '$2y$10$se2Ja/2fWCuMKnbv043d9eiWWxyDnaQCwPjiHhkML0ysg1HPyB.uq'),
(2, 'jane_smith', 'jane.smith@email.com', '555-0102', 'hashed_password_2'),
(3, 'mike_johnson', 'mike.johnson@email.com', '555-0103', 'hashed_password_3'),
(4, 'sarah_wilson', 'sarah.wilson@email.com', '555-0104', 'hashed_password_4'),
(5, 'david_brown', 'david.brown@email.com', '555-0105', 'hashed_password_5'),
(6, 'lisa_davis', 'lisa.davis@email.com', '555-0106', 'hashed_password_6'),
(7, 'tom_miller', 'tom.miller@email.com', '555-0107', 'hashed_password_7'),
(8, 'emma_garcia', 'emma.garcia@email.com', '555-0108', 'hashed_password_8'),
(9, 'Eugene', 'eugenendayisaba33@gmail.com', '0789375249', '$2y$10$yVdqlmuRF8VjLBciVMfXeuJyxEgniX/rKIuCMHLOxFKRUAmr.10k.');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `quantity`) VALUES
(1, 'Wireless Bluetooth Headphones', 79.99, 150),
(2, 'Smartphone Case', 24.99, 300),
(3, 'USB-C Charging Cable', 12.99, 500),
(4, 'Portable Power Bank', 39.99, 200),
(5, 'Wireless Mouse', 29.99, 180),
(6, 'Mechanical Keyboard', 89.99, 75),
(7, '4K Webcam', 129.99, 50),
(8, 'Gaming Monitor', 299.99, 25),
(9, 'Laptop Stand', 49.99, 100),
(10, 'Blue Light Glasses', 19.99, 250),
(11, 'Phone Ring Holder', 8.99, 400),
(12, 'Tablet Screen Protector', 14.99, 350),
(13, 'Car Phone Mount', 22.99, 175),
(14, 'Fitness Tracker', 119.99, 80),
(15, 'Smart Water Bottle', 34.99, 120);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buy`
--
ALTER TABLE `buy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buy`
--
ALTER TABLE `buy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `buy`
--
ALTER TABLE `buy`
  ADD CONSTRAINT `buy_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `buy_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
