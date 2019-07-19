-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 18, 2019 at 09:29 AM
-- Server version: 5.7.24
-- PHP Version: 7.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


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
-- Table structure for table `chats`
--

DROP TABLE IF EXISTS `chats`;
CREATE TABLE IF NOT EXISTS `chats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `accepted_user_id` int(11) NOT NULL,
  `name` varchar(55) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `store_location_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `quote_id` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL ,
  `status` varchar(30) NOT NULL DEFAULT 'initiated' COMMENT '1. initiated, 2. accepted, 3. closed',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chats`
--

INSERT INTO `chats` (`id`, `user_id`, `accepted_user_id`, `name`, `phone`, `store_location_id`, `question`, `quote_id`, `created_at`, `status`) VALUES
(3, 3, 4, 'Sooraj', '898989893', 1, 'Hello, I want to know the price ', 0, '2019-06-27 12:15:26', 'accepted'),
(4, 3, 0, 'Sooraj', '898989893', 1, 'Hello, I want to know the price ', 0, '2019-06-27 12:16:03', 'initiated'),
(5, 3, 0, 'Sooraj', '898989893', 1, 'Hello, I want to know the price ', 0, '2019-06-27 12:17:17', 'initiated'),
(6, 3, 0, 'Rajin', '89998999887', 3, 'The descttd dopuyb jjs', 0, '2019-07-08 06:45:40', 'initiated'),
(7, 3, 0, 'Karthik', '89998999887', 3, 'The descttd dopuyb jjs', 3, '2019-07-08 07:05:28', 'initiated'),
(8, 3, 0, 'Karthik', '89998999887', 3, 'The descttd dopuyb jjs', 4, '2019-07-08 07:05:58', 'initiated');

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
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chat_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `media_file` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `chat_id`, `sender_id`, `receiver_id`, `message`, `media_file`, `created_at`) VALUES
(1, 3, 3, 1, 'Hello, I want to know the price ', '', '2019-07-02 08:19:39'),
(2, 3, 1, 3, 'Hello', '', '2019-07-03 10:10:37'),
(3, 3, 1, 3, 'Hello 345554', '', '2019-07-03 10:32:42');

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
  `created_at` datetime NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `quotes`
--

INSERT INTO `quotes` (`id`, `name`, `phone`, `store_location`, `category_id`, `jewelry_type`, `metal_type`, `model_number`, `brand`, `description`, `device_token`, `created_at`) VALUES
(2, 'Rajin', '89998999887', 'Both', 1, NULL, NULL, '89yyu', 'Bnio', 'The descttd dopuyb jjs', 'aUUY-8999iii-898jasa', '2019-07-08 06:45:40'),
(3, 'Karthik', '89998999887', 'Both', 1, NULL, NULL, '89yyu', 'Bnio', 'The descttd dopuyb jjs', 'aUUY-8999iii-898jasa', '2019-07-08 07:05:28'),
(4, 'Karthik', '89998999887', 'Both', 1, NULL, NULL, '89yyu', 'Bnio', 'The descttd dopuyb jjs', 'aUUY-8999iii-898jasa', '2019-07-08 07:05:58');

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `quote_images`
--

INSERT INTO `quote_images` (`id`, `quote_id`, `image_path`) VALUES
(1, 4, 'test.png');

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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `store_locations`
--

INSERT INTO `store_locations` (`id`, `store_name`, `location`, `address`, `status`) VALUES
(1, 'OXON HILL', 'OXON HILL', '5225-B Indian Head Hwy. Oxon Hill, MD 20745', 1),
(2, 'WALDORF', 'WALDORF', '3360 Crain Hwy Waldorf, MD 20603', 1),
(3, 'Both', '-', '-', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  `email` varchar(155) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` text NOT NULL,
  `type` varchar(15) NOT NULL DEFAULT 'user',
  `device_id` varchar(255) NOT NULL,
  `device_token` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL ,
  `updated_at` datetime NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `type`, `device_id`, `device_token`, `created_at`, `updated_at`) VALUES
(2, '', '', '0', '', 'admin', 'UUY-8999iii-898jasa', '787huys8ssuwwwwssuemlw90wsw08sxxssxj88w9sko', '2019-07-17 15:12:59', '2019-07-17 15:12:59'),
(3, 'Karthik', '', '89998999887', '', 'user', 'aUUY-8999iii-898jasa', 'a787huys8ssuwwwwssuemlw90wsw08sxxssxj88w9sko', '2019-07-17 15:12:59', '2019-07-17 15:12:59'),
(4, 'Test Admin', 'testadmin@topdollar.com', '8989898898', '$2a$10$CPs2dNAWyX24ED2YiFJz1uZsNE2y4cs6xVlhoAtxSkagp.v52RUaq', 'admin', 'fdYFCOQyFoWIkVG8rDSc', 'fdYFCOQyFoWIkVG8rDScfdYFCOQyFoWIkVG8rDScfdYFCOQyFoWIkVG8rDScfdYFCOQyFoWIkVG8rDScfdYFCOQyFoWIkVG8rDSc', '2019-07-17 15:12:59', '2019-07-17 15:12:59'),
(5, 'Sooraj Admin', 'sooraj.v@topdollar.com', '0', '$2a$10$CPs2dNAWyX24ED2YiFJz1uZsNE2y4cs6xVlhoAtxSkagp.v52RUaq', 'admin', 'casdasd-asdasd-asdasdasd', '', '2019-07-17 15:12:59', '2019-07-17 15:12:59'),
(8, 'Sooraj', 'sooraj@topdollar.com', '', '$2y$10$8H5uRv5XpkKyiPWpUTzC0OgyPEoXfJJ5BnMPI2XYCUb3U2x0QGI92', 'user', '', '', '2019-07-18 06:40:42', '2019-07-18 06:40:42');
COMMIT;

