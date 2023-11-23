-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2023 at 01:18 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(100) NOT NULL,
  `quantity` int(100) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `pid`, `name`, `price`, `quantity`, `image`) VALUES
(221, 116, 24, 'banana', 50, 1, 'banana.png');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `courier_id` int(11) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `document_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `courier_id`, `document_name`, `document_path`, `uploaded_at`) VALUES
(35, 0, '1700222403_UpdatedSignedAgreementForm (1).pdf', 'document_uploads/1700222403_UpdatedSignedAgreementForm (1).pdf', '2023-11-17 12:00:04'),
(36, 0, '1700222403_UpdatedSignedAgreementForm (1).pdf', 'document_uploads/1700222403_UpdatedSignedAgreementForm (1).pdf', '2023-11-17 12:00:04'),
(37, 159, '1700222500_UpdatedSignedAgreementForm (1).pdf', 'document_uploads/1700222500_UpdatedSignedAgreementForm (1).pdf', '2023-11-17 12:01:41'),
(38, 159, '1700222500_UpdatedSignedAgreementForm (1).pdf', 'document_uploads/1700222500_UpdatedSignedAgreementForm (1).pdf', '2023-11-17 12:01:41'),
(55, 160, 'AgreementForm.pdf', 'document_uploads/AgreementForm.pdf', '2023-11-17 18:32:25');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `number` varchar(12) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `message_content` text DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `order_id`, `sender_id`, `receiver_id`, `message_content`, `timestamp`, `is_read`, `is_deleted`) VALUES
(313, 63, 125, 116, 'Your order has been accepted by the courier. \nAddress: 15-15A Sitio Adelfa, Cebu City, Philippines - 123\nTotal Products: banana ( 1 )meat ( 1 )\nTime of Order: 2023-11-10 02:40:15\n', '2023-11-10 02:40:19', 1, 0),
(314, 64, 134, 116, 'Your order has been accepted by the courier. \nAddress: 15-15A Sitio Adelfa, Cebu City, Philippines - 123123\nTotal Products: meat ( 1 )\nTime of Order: 2023-11-10 02:41:33\n', '2023-11-10 02:41:42', 1, 0),
(315, 64, 134, 116, 'hello', '2023-11-10 02:41:46', 1, 0),
(316, 64, 116, 134, 'hello', '2023-11-10 02:41:53', 1, 0),
(317, 64, 134, 116, 'okay, wait for me', '2023-11-10 02:43:14', 1, 0),
(318, 64, 116, 134, 'ok', '2023-11-10 03:45:18', 1, 0),
(319, 63, 116, 125, 'ok', '2023-11-10 03:45:21', 1, 0),
(320, 65, 125, 116, 'Your order has been accepted by the courier. \nAddress: 15-15A Sitio Adelfa, Cebu City, Philippines - 12312\nTotal Products: Kwekkwek ( 1 )spatula ( 1 )\nTime of Order: 2023-11-12 17:11:09\n', '2023-11-12 17:11:38', 1, 0),
(321, 65, 116, 125, 'okay', '2023-11-17 20:06:27', 1, 0),
(322, 63, 116, 125, 'hey', '2023-11-17 20:06:35', 0, 0),
(323, 65, 125, 116, 'hello', '2023-11-17 20:19:32', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `courier_id` int(11) DEFAULT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `number` varchar(12) NOT NULL,
  `email` varchar(100) NOT NULL,
  `method` varchar(50) NOT NULL,
  `address` varchar(500) NOT NULL,
  `total_products` varchar(1000) NOT NULL,
  `total_price` int(100) NOT NULL,
  `placed_on` varchar(50) NOT NULL,
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending',
  `time_of_order` timestamp NULL DEFAULT current_timestamp(),
  `status_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `courier_id`, `transaction_id`, `name`, `number`, `email`, `method`, `address`, `total_products`, `total_price`, `placed_on`, `payment_status`, `time_of_order`, `status_updated_at`) VALUES
(63, 116, 125, 'ESM5HCRA17EK', 'Jane Doe', '09224336828', 'admin@gga.com', 'cash on delivery', '15-15A Sitio Adelfa, Cebu City, Philippines - 123', 'banana ( 1 )meat ( 1 )', 100, '11-09-2023', 'completed', '2023-11-09 18:40:15', '2023-11-12 08:58:31'),
(64, 116, 134, 'ESMCOGEJ2FTV', 'Jane Doe', '09224336828', 'admin@gga.com', 'cash on delivery', '15-15A Sitio Adelfa, Cebu City, Philippines - 123123', 'meat ( 1 )', 50, '11-09-2023', 'preparing order', '2023-11-09 18:41:33', '2023-11-09 18:43:09'),
(65, 116, 125, 'ESMAPW4LOR81', 'Jane Doe', '09224336828', 'admin@gga.com', 'cash on delivery', '15-15A Sitio Adelfa, Cebu City, Philippines - 12312', 'Kwekkwek ( 1 )spatula ( 1 )', 520, '11-12-2023', 'pending', '2023-11-12 09:11:09', '2023-11-12 09:11:38'),
(66, 116, NULL, 'ESMBXG9RWNJU', 'Jane Doe', '09224336828', 'admin@gga.com', 'cash on delivery', '15-15A Sitio Adelfa, Cebu City, Philippines - 123', 'meat ( 1 )banana ( 1 )', 100, '11-12-2023', 'pending', '2023-11-12 09:12:40', '2023-11-12 09:15:20'),
(67, 116, NULL, 'ESM40QM3B7SR', 'Jane Doe', '09224336828', 'admin@gga.com', 'cash on delivery', '15-15A Sitio Adelfa, Cebu City, Philippines - 1231', 'banana ( 1 )', 50, '11-12-2023', 'pending', '2023-11-12 09:31:44', '2023-11-12 09:31:44');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `details` varchar(500) NOT NULL,
  `price` int(100) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `details`, `price`, `image`) VALUES
(24, 'banana', 'fruits and vegetables', 'banana', 50, 'banana.png'),
(25, 'meat', 'poultry and meat', 'hind quarter \r\n50 per kilogram', 50, 'beaf steak.png'),
(28, 'fish', 'fresh seafood', 'fish ', 100, 'cat-4.png'),
(29, 'clothes', 'clothing and apparel', 'asd', 50, 'clothing.png'),
(30, 'shoes', 'footwear and accessories', 'shoes', 500, 'footwear.png'),
(31, 'Spices', 'spices and condiments', 'spice', 50, 'spices.png'),
(32, 'Kwekkwek', 'local snacks and street food', 'quail', 20, 'street food.png'),
(33, 'Grapes', 'fruits and vegetables', 'asdas', 100, 'blue grapes.png'),
(34, 'spatula', 'kitchen stuff ', 'spatula asdbaasdasd', 500, 'utensils.png'),
(35, 'broccoli', 'fruits and vegetables', 'fresh broccoli', 75, 'broccoli.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `number` varchar(100) NOT NULL,
  `user_type` varchar(20) NOT NULL DEFAULT 'user',
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `number`, `user_type`, `image`) VALUES
(115, 'John Doe', 'admin@ggd.com', '$2y$10$US6o0XT33Ziiz7UNME5ZQumPXx5IOU9QRjEm2w9XUDsMl37mKCbGG', '09221234567', 'admin', '1067014.jpg'),
(116, 'Jane Doe', 'admin@gga.com', '$2y$10$AQP4zPXOVrblOR.1odQ/ie8gFmxaeCOubYKCcx4A1KZwcNzkP2jdW', '09224336828', 'user', 'aNwMzMA_700b_4.jpg'),
(125, 'Rider 1', 'admin@ggr1.com', '$2y$10$4D5bs.wwx73mbKa2gC.qX.5fSHF..gSu2nTXrmjWdtLYMM1Wm3PcG', '09224336890', 'cour', 'images (4)_1.jpeg'),
(134, 'rider 2', 'admin@ggr2.com', '$2y$10$awPt8nRaN80.uRQ3DsF06uK5UGUyp5wLbR4iRQSKa0G9dIwkZCnSe', '09224333333', 'cour', 'images (10).jpeg'),
(150, 'rider 3', 'admin@ggr3.com', '$2y$10$gs6gxrYOY7IpGBG76/fSZ.qUbytQZzOXsrKxMQ9RPtv/SBonChw9y', '09221234560', 'cour', 'default.png'),
(151, 'Jojo Doe', 'admin@ggj.com', '$2y$10$qXU6gEQEUH69mhI.YPwgr.OtKm3awqS4LnzdESbonuZpoGOLRPOX6', '09221234111', 'user', 'adPAqwZ_700b_3.jpg'),
(156, 'rider 4 ', 'admin@ggr4.com', '$2y$10$VTYEFAPJK5DIGZEIBWit9.c4ZvKrrOvdbH/3T/2bcPmXTg18Bu4aq', '09121321311', 'cour', 'default.png'),
(160, 'rider 57', 'admin@ggr5.com', '$2y$10$OgtqkelOjkRfnXNJMY8Mv.KTTVG67000WVJsCwC6pUjgmrH87nOGi', '09123123123', 'ucour', 'default.png');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(100) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=222;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=324;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
