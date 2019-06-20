-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 20, 2019 at 12:25 PM
-- Server version: 5.7.24
-- PHP Version: 7.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `top_dollar_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(155) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category`, `parent_id`, `status`) VALUES
(1, 'Jewelry', 0, 1),
(2, 'Electronics', 0, 1),
(3, 'Tool', 0, 1),
(4, 'Bag', 0, 1),
(5, 'Purse', 0, 1),
(6, 'Computer', 0, 1),
(7, 'Other', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `filters`
--

DROP TABLE IF EXISTS `filters`;
CREATE TABLE IF NOT EXISTS `filters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_id` int(11) NOT NULL,
  `filter_name` varchar(155) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `filters`
--

INSERT INTO `filters` (`id`, `cat_id`, `filter_name`) VALUES
(1, 1, 'Jewelry Type'),
(2, 1, 'Metal Type');

-- --------------------------------------------------------

--
-- Table structure for table `filter_values`
--

DROP TABLE IF EXISTS `filter_values`;
CREATE TABLE IF NOT EXISTS `filter_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filter_id` int(11) NOT NULL,
  `filter_value` varchar(155) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `filter_values`
--

INSERT INTO `filter_values` (`id`, `filter_id`, `filter_value`) VALUES
(1, 1, 'Necklace'),
(2, 1, 'Bracelet'),
(3, 1, 'Ring'),
(4, 1, 'Chain'),
(5, 1, 'Other'),
(6, 2, 'Gold'),
(7, 2, 'Silver'),
(8, 2, 'Platinum'),
(9, 2, 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `quotes`
--

DROP TABLE IF EXISTS `quotes`;
CREATE TABLE IF NOT EXISTS `quotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `store_location` varchar(155) NOT NULL,
  `category_id` int(11) NOT NULL,
  `jewelry_type` varchar(55) DEFAULT NULL,
  `metal_type` varchar(55) DEFAULT NULL,
  `model_number` varchar(55) DEFAULT NULL,
  `brand` varchar(55) DEFAULT NULL,
  `description` text NOT NULL,
  `device_token` varchar(155) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `quotes`
--

INSERT INTO `quotes` (`id`, `name`, `phone`, `store_location`, `category_id`, `jewelry_type`, `metal_type`, `model_number`, `brand`, `description`, `device_token`, `created_at`) VALUES
(1, 'Sooraj', '898989898987', 'kollam', 2, NULL, NULL, '889M', 'Zbrand', 'Ths is the decsription fo the university amenju', 'adyg6hskmkl&2skolou09ss6sbbsh223', '2019-06-20 05:48:42');

-- --------------------------------------------------------

--
-- Table structure for table `quote_images`
--

DROP TABLE IF EXISTS `quote_images`;
CREATE TABLE IF NOT EXISTS `quote_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) NOT NULL,
  `image_path` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `quote_images`
--

INSERT INTO `quote_images` (`id`, `quote_id`, `image_path`) VALUES
(1, 1, '02-may-OUT.jpg'),
(2, 1, '03-may-IN.jpg'),
(3, 1, '03-may-OUT.jpg'),
(4, 1, '06-may-IN.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `store_locations`
--

DROP TABLE IF EXISTS `store_locations`;
CREATE TABLE IF NOT EXISTS `store_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_name` varchar(155) NOT NULL,
  `location` varchar(155) NOT NULL,
  `address` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `store_locations`
--

INSERT INTO `store_locations` (`id`, `store_name`, `location`, `address`, `status`) VALUES
(1, 'Test store', 'Test location', 'This is TS addr\r\n89plaza 88 20', 1),
(2, 'Test store1', 'Test location2', 'Enu iii eiddr\r\n89plaza 88 20', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  `email` varchar(155) NOT NULL,
  `password` text NOT NULL,
  `type` varchar(15) NOT NULL DEFAULT 'admin',
  `device_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
