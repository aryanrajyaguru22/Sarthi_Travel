-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 06, 2025 at 10:12 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `travel_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `buses`
--

DROP TABLE IF EXISTS `buses`;
CREATE TABLE IF NOT EXISTS `buses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bus_no` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bus_no` (`bus_no`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

DROP TABLE IF EXISTS `ingredients`;
CREATE TABLE IF NOT EXISTS `ingredients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ingredient_units`
--

DROP TABLE IF EXISTS `ingredient_units`;
CREATE TABLE IF NOT EXISTS `ingredient_units` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ingredient_id` int NOT NULL,
  `unit` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ingredient_id` (`ingredient_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ingredient_units`
--

INSERT INTO `ingredient_units` (`id`, `ingredient_id`, `unit`) VALUES
(6, 4, 'Kg'),
(5, 3, 'Kg'),
(7, 5, 'Packet'),
(8, 6, 'Kg'),
(9, 6, 'Gram'),
(10, 7, 'Kg'),
(11, 7, 'Gram'),
(12, 8, 'Litre'),
(13, 9, 'Packet'),
(14, 10, 'Piece');

-- --------------------------------------------------------

--
-- Table structure for table `meal_ingredients`
--

DROP TABLE IF EXISTS `meal_ingredients`;
CREATE TABLE IF NOT EXISTS `meal_ingredients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `meal_id` int NOT NULL,
  `ingredient_id` int NOT NULL,
  `quantity` varchar(50) NOT NULL,
  `unit` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meal_id` (`meal_id`),
  KEY `ingredient_id` (`ingredient_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `meal_ingredients`
--

INSERT INTO `meal_ingredients` (`id`, `meal_id`, `ingredient_id`, `quantity`, `unit`) VALUES
(6, 4, 3, '5', NULL),
(5, 4, 4, '1', NULL),
(4, 3, 5, '20', NULL),
(7, 5, 10, '20', NULL),
(8, 5, 6, '10', NULL),
(9, 5, 7, '2', NULL),
(10, 6, 9, '80', NULL),
(11, 6, 8, '5', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `meal_items`
--

DROP TABLE IF EXISTS `meal_items`;
CREATE TABLE IF NOT EXISTS `meal_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trip_details`
--

DROP TABLE IF EXISTS `trip_details`;
CREATE TABLE IF NOT EXISTS `trip_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `source` varchar(100) DEFAULT NULL,
  `destination` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `bus_id` int DEFAULT NULL,
  `km` int DEFAULT NULL,
  `meal_items` text,
  `breakfast_meal_id` int DEFAULT NULL,
  `lunch_meal_id` int DEFAULT NULL,
  `dinner_meal_id` int DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT '0.00',
  `payment_status` enum('pending','completed') DEFAULT 'pending',
  `completed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `bus_id` (`bus_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `mobile`, `password`) VALUES
(2, 'Aryan', 'aryanrajyaguru22@gmail.com', '7600663667', '$2y$10$I2vPWOfpHHcGq9Cju5.c1OkYGmKFV0JN06d5vTyGpewjmIssorG1y');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
