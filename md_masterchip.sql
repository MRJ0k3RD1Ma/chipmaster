-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 14, 2026 at 05:54 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `md_masterchip`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
                         `id` int(11) NOT NULL,
                         `name` varchar(255) NOT NULL DEFAULT '',
                         `username` varchar(255) NOT NULL DEFAULT '',
                         `password` varchar(500) DEFAULT NULL,
                         `phone` varchar(255) DEFAULT NULL,
                         `role_id` int(11) NOT NULL,
                         `status` int(11) DEFAULT 1,
                         `created` datetime DEFAULT current_timestamp(),
                         `updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `username`, `password`, `phone`, `role_id`, `status`, `created`, `updated`) VALUES
                                                                                                                   (1, 'Adminbek', 'admin', '$2y$13$u0QV0cVlVIya1TbQ9ncHS.8lnq4ApCBuMqDPQiQB6BXWCVOF0D5xO', '+998901234567', 1, 1, '2026-02-10 22:16:25', '2026-02-14 19:39:08'),
                                                                                                                   (2, 'Adminbek', 'adminbek', '$2y$13$2spzrrmqDIuUsVzSTR.YZeN5tfkwkX3rIL5HoOxlgvobPjMDyrgse', '+998901234567', 1, 1, '2026-02-12 18:49:01', '2026-02-12 18:49:01');

-- --------------------------------------------------------

--
-- Table structure for table `admin_role`
--

CREATE TABLE `admin_role` (
                              `id` int(11) NOT NULL,
                              `name` varchar(255) NOT NULL DEFAULT '',
                              `status` int(11) DEFAULT 1,
                              `created` datetime DEFAULT current_timestamp(),
                              `updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_role`
--

INSERT INTO `admin_role` (`id`, `name`, `status`, `created`, `updated`) VALUES
    (1, 'admin', 1, '2026-02-10 22:12:15', '2026-02-12 18:48:10');

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
                         `id` int(11) NOT NULL,
                         `name` varchar(50) DEFAULT NULL,
                         `slug` varchar(255) DEFAULT NULL,
                         `logo_id` int(11) DEFAULT NULL,
                         `status` int(11) DEFAULT 1,
                         `created` datetime DEFAULT current_timestamp(),
                         `updated` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`id`, `name`, `slug`, `logo_id`, `status`, `created`, `updated`) VALUES
                                                                                          (1, 'Microsoft', 'microsoft', NULL, 1, '2026-02-13 23:29:55', '2026-02-13 23:29:55'),
                                                                                          (2, 'slugged', 'slugged', 2, 0, '2026-02-13 23:31:32', '2026-02-14 19:45:01'),
                                                                                          (3, 'logo test', 'logo-test', 1, 1, '2026-02-14 19:44:10', '2026-02-14 19:44:10');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
                              `id` int(11) NOT NULL,
                              `user_id` int(11) NOT NULL,
                              `product_id` int(11) NOT NULL,
                              `quantity` int(11) DEFAULT 1,
                              `added_at` datetime DEFAULT current_timestamp(),
                              `status` int(11) DEFAULT 1,
                              `created` datetime DEFAULT current_timestamp(),
                              `updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
                            `id` int(11) NOT NULL,
                            `parent_id` int(11) DEFAULT NULL,
                            `name_uz` varchar(255) DEFAULT NULL,
                            `name_ru` varchar(255) DEFAULT NULL,
                            `slug` varchar(255) DEFAULT NULL,
                            `icon` varchar(255) DEFAULT NULL,
                            `image_id` int(11) DEFAULT NULL,
                            `spec_template` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`spec_template`)),
                            `sort_order` int(11) DEFAULT 0,
                            `status` int(11) DEFAULT 1,
                            `created` datetime DEFAULT current_timestamp(),
                            `updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `parent_id`, `name_uz`, `name_ru`, `slug`, `icon`, `image_id`, `spec_template`, `sort_order`, `status`, `created`, `updated`) VALUES
                                                                                                                                                                (1, NULL, 'Test', 'test', 'test', 'icon', NULL, NULL, 1, 1, '2026-02-14 00:11:44', '2026-02-14 00:11:44'),
                                                                                                                                                                (2, 1, 'TestChild2', 'TestChild2', 'TestChild2', 'icon', NULL, NULL, 1, 1, '2026-02-14 00:12:14', '2026-02-14 00:13:25'),
                                                                                                                                                                (3, NULL, 'testnasme', 'testnasme', NULL, '', 1, '\"{\\\"a\\\":\\\"b\\\"}\"', 1, 1, '2026-02-14 20:58:56', '2026-02-14 20:58:56');

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
                         `id` int(11) NOT NULL,
                         `name` varchar(255) DEFAULT NULL,
                         `slug` varchar(255) NOT NULL,
                         `exts` varchar(255) DEFAULT NULL,
                         `url` varchar(255) DEFAULT NULL,
                         `status` int(11) DEFAULT 1,
                         `created` datetime DEFAULT current_timestamp(),
                         `updated` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `name`, `slug`, `exts`, `url`, `status`, `created`, `updated`) VALUES
                                                                                              (1, 'ilovepdf_pages-to-jpg (7)', '', 'zip', '{\"original\":\"/upload/files/1771070188_jKOCE-qN.zip\"}', 1, '2026-02-14 16:56:28', '2026-02-14 16:56:28'),
                                                                                              (2, 'Insomnia.Core-12.0.0', '', 'exe', '{\"original\":\"/upload/files/1771070536_z3hE11vS.exe\"}', 1, '2026-02-14 17:02:22', '2026-02-14 17:02:22');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
                          `id` int(11) NOT NULL,
                          `user_id` int(11) NOT NULL,
                          `order_number` varchar(255) NOT NULL DEFAULT '',
                          `state` enum('PENDING','PAID','PROCESSING','SHIPPED','DELIVERED','CANCELLED','REFOUNDED') DEFAULT 'PENDING',
                          `subtotal` int(11) DEFAULT 0,
                          `delivery_fee` int(11) DEFAULT 0,
                          `discount_amount` int(11) DEFAULT NULL,
                          `total` int(11) DEFAULT NULL,
                          `delivery_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`delivery_address`)),
                          `delivery_method` varchar(255) DEFAULT NULL,
                          `payment_method_id` int(11) DEFAULT NULL,
                          `payment_id` varchar(255) DEFAULT NULL,
                          `paid_at` datetime DEFAULT NULL,
                          `notes` text DEFAULT NULL,
                          `created` datetime DEFAULT current_timestamp(),
                          `updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_deliveries`
--

CREATE TABLE `order_deliveries` (
                                    `id` int(11) NOT NULL,
                                    `order_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
                              `id` int(11) NOT NULL,
                              `order_id` int(11) DEFAULT NULL,
                              `product_id` int(11) DEFAULT NULL,
                              `name` varchar(255) DEFAULT NULL,
                              `image` varchar(255) DEFAULT NULL,
                              `price` int(11) DEFAULT NULL,
                              `quantity` int(11) DEFAULT NULL,
                              `subtotal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_state_history`
--

CREATE TABLE `order_state_history` (
                                       `id` int(11) NOT NULL,
                                       `order_id` int(11) NOT NULL,
                                       `from_status` varchar(255) NOT NULL DEFAULT '',
                                       `to_status` varchar(255) NOT NULL DEFAULT '',
                                       `changed_by` varchar(255) NOT NULL DEFAULT '',
                                       `notes` text DEFAULT NULL,
                                       `created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
                            `id` int(11) NOT NULL,
                            `order_id` int(11) DEFAULT NULL,
                            `payment_method_id` int(11) DEFAULT NULL,
                            `payment_transaction_id` varchar(255) DEFAULT NULL,
                            `amount` double DEFAULT 0,
                            `state` enum('PENDING','PROCESSING','COMPLETED','FAILED','CANCELLED','REFOUNDED') DEFAULT 'PENDING',
                            `raw_request` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`raw_request`)),
                            `raw_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`raw_response`)),
                            `paid_at` varchar(255) DEFAULT NULL,
                            `error_message` text DEFAULT NULL,
                            `created` datetime DEFAULT current_timestamp(),
                            `updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_method`
--

CREATE TABLE `payment_method` (
                                  `id` int(11) NOT NULL,
                                  `name` varchar(255) NOT NULL DEFAULT '',
                                  `status` int(11) DEFAULT NULL,
                                  `created` datetime DEFAULT current_timestamp(),
                                  `updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
                           `id` int(11) NOT NULL,
                           `category_id` int(11) NOT NULL,
                           `brand_id` varchar(255) DEFAULT NULL,
                           `name_uz` varchar(255) NOT NULL DEFAULT '',
                           `name_ru` varchar(255) NOT NULL DEFAULT '',
                           `slug` varchar(255) NOT NULL DEFAULT '',
                           `description_uz` longtext DEFAULT NULL,
                           `description_ru` longtext DEFAULT NULL,
                           `sku` varchar(255) NOT NULL DEFAULT '',
                           `price` int(11) NOT NULL,
                           `discount_price` int(11) DEFAULT 0,
                           `discount_expires` datetime DEFAULT NULL,
                           `specifecations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specifecations`)),
                           `stock_quantity` int(11) DEFAULT 0,
                           `status` int(11) DEFAULT 1,
                           `featured` int(11) DEFAULT 1,
                           `seo_title` varchar(255) NOT NULL DEFAULT '',
                           `seo_description` text DEFAULT NULL,
                           `created` datetime DEFAULT current_timestamp(),
                           `updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                           `image_id` int(11) DEFAULT NULL,
                           `is_device` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_guides`
--

CREATE TABLE `product_guides` (
                                  `id` int(11) NOT NULL,
                                  `product_id` int(11) NOT NULL,
                                  `has_video` int(11) NOT NULL DEFAULT 1,
                                  `title_uz` varchar(255) NOT NULL DEFAULT '',
                                  `title_ru` varchar(255) NOT NULL DEFAULT '',
                                  `content_uz` text DEFAULT NULL,
                                  `content_ru` text DEFAULT NULL,
                                  `video_id` int(11) DEFAULT NULL,
                                  `sort_order` int(11) DEFAULT 0,
                                  `status` int(11) DEFAULT 1,
                                  `created` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                                  `updated` datetime DEFAULT current_timestamp(),
                                  `slug` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_image`
--

CREATE TABLE `product_image` (
                                 `id` int(11) NOT NULL,
                                 `product_id` int(11) NOT NULL,
                                 `image_id` int(11) NOT NULL,
                                 `alt_text` varchar(255) DEFAULT NULL,
                                 `sort_order` int(11) DEFAULT 0,
                                 `is_primary` int(11) DEFAULT 0,
                                 `created` datetime DEFAULT current_timestamp(),
                                 `updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                                 `status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `soft`
--

CREATE TABLE `soft` (
                        `id` int(11) NOT NULL,
                        `user_id` int(11) NOT NULL,
                        `product_id` int(11) NOT NULL,
                        `expires` datetime DEFAULT NULL,
                        `status` int(11) DEFAULT NULL,
                        `created` datetime DEFAULT current_timestamp(),
                        `updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
                        `id` int(11) NOT NULL,
                        `phone` varchar(255) NOT NULL DEFAULT '',
                        `name` varchar(255) DEFAULT NULL,
                        `is_verified` int(11) DEFAULT 0,
                        `status` int(11) DEFAULT 1,
                        `created` datetime DEFAULT current_timestamp(),
                        `updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_session`
--

CREATE TABLE `user_session` (
                                `id` int(11) NOT NULL,
                                `user_id` int(11) NOT NULL,
                                `refresh_token` varchar(500) NOT NULL DEFAULT '',
                                `device_info` varchar(255) DEFAULT NULL,
                                `ip_address` varchar(255) NOT NULL DEFAULT '',
                                `expires_at` varchar(255) NOT NULL DEFAULT '',
                                `created` datetime DEFAULT current_timestamp(),
                                `updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_verification`
--

CREATE TABLE `user_verification` (
                                     `id` int(11) NOT NULL,
                                     `phone` varchar(255) DEFAULT NULL,
                                     `code` varchar(255) DEFAULT NULL,
                                     `attempts` int(11) DEFAULT 0,
                                     `expires_at` datetime DEFAULT NULL,
                                     `created` datetime DEFAULT current_timestamp(),
                                     `is_user` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
                            `id` int(11) NOT NULL,
                            `user_id` int(11) NOT NULL,
                            `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `FK_admin_role_id` (`role_id`);

--
-- Indexes for table `admin_role`
--
ALTER TABLE `admin_role`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
    ADD PRIMARY KEY (`id`),
  ADD KEY `FK_brand_logo_id` (`logo_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `FK_category_image_id` (`image_id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `FK_orders_user_id` (`user_id`),
  ADD KEY `FK_orders_payment_method_id` (`payment_method_id`);

--
-- Indexes for table `order_deliveries`
--
ALTER TABLE `order_deliveries`
    ADD PRIMARY KEY (`id`),
  ADD KEY `FK_order_deliveries_order_id` (`order_id`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
    ADD PRIMARY KEY (`id`),
  ADD KEY `FK_order_item_order_id` (`order_id`),
  ADD KEY `FK_order_item_product_id` (`product_id`);

--
-- Indexes for table `order_state_history`
--
ALTER TABLE `order_state_history`
    ADD PRIMARY KEY (`id`),
  ADD KEY `FK_order_state_history_order_id` (`order_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_method`
--
ALTER TABLE `payment_method`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `FK_product_category_id` (`category_id`),
  ADD KEY `FK_product_image_id` (`image_id`);

--
-- Indexes for table `product_guides`
--
ALTER TABLE `product_guides`
    ADD PRIMARY KEY (`id`),
  ADD KEY `FK_product_guides_product_id` (`product_id`),
  ADD KEY `FK_product_guides_video_id` (`video_id`);

--
-- Indexes for table `product_image`
--
ALTER TABLE `product_image`
    ADD PRIMARY KEY (`id`),
  ADD KEY `FK_product_image_product_id` (`product_id`),
  ADD KEY `FK_product_image_image_id` (`image_id`);

--
-- Indexes for table `soft`
--
ALTER TABLE `soft`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `user_session`
--
ALTER TABLE `user_session`
    ADD PRIMARY KEY (`id`),
  ADD KEY `FK_user_session_user_id` (`user_id`);

--
-- Indexes for table `user_verification`
--
ALTER TABLE `user_verification`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
    ADD PRIMARY KEY (`id`),
  ADD KEY `FK_wishlist_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin_role`
--
ALTER TABLE `admin_role`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_deliveries`
--
ALTER TABLE `order_deliveries`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_state_history`
--
ALTER TABLE `order_state_history`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_method`
--
ALTER TABLE `payment_method`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_guides`
--
ALTER TABLE `product_guides`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_image`
--
ALTER TABLE `product_image`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `soft`
--
ALTER TABLE `soft`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_session`
--
ALTER TABLE `user_session`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_verification`
--
ALTER TABLE `user_verification`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
    ADD CONSTRAINT `FK_admin_role_id` FOREIGN KEY (`role_id`) REFERENCES `admin_role` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `brand`
--
ALTER TABLE `brand`
    ADD CONSTRAINT `FK_brand_logo_id` FOREIGN KEY (`logo_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `category`
--
ALTER TABLE `category`
    ADD CONSTRAINT `FK_category_image_id` FOREIGN KEY (`image_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
    ADD CONSTRAINT `FK_orders_payment_method_id` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_method` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_orders_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `order_deliveries`
--
ALTER TABLE `order_deliveries`
    ADD CONSTRAINT `FK_order_deliveries_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
    ADD CONSTRAINT `FK_order_item_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_order_item_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `order_state_history`
--
ALTER TABLE `order_state_history`
    ADD CONSTRAINT `FK_order_state_history_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
    ADD CONSTRAINT `FK_product_category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_product_image_id` FOREIGN KEY (`image_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `product_guides`
--
ALTER TABLE `product_guides`
    ADD CONSTRAINT `FK_product_guides_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_product_guides_video_id` FOREIGN KEY (`video_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `product_image`
--
ALTER TABLE `product_image`
    ADD CONSTRAINT `FK_product_image_image_id` FOREIGN KEY (`image_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_product_image_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user_session`
--
ALTER TABLE `user_session`
    ADD CONSTRAINT `FK_user_session_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
    ADD CONSTRAINT `FK_wishlist_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
