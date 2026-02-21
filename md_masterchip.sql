-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 21, 2026 at 05:38 PM
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
(1, 'admin', 1, '2026-02-10 22:12:15', '2026-02-17 16:18:15');

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
(1, 'Microsoft', 'microsoft', 19, 1, '2026-02-13 23:29:55', '2026-02-17 16:05:16'),
(2, 'slugged', 'slugged', 2, 0, '2026-02-13 23:31:32', '2026-02-14 19:45:01'),
(3, 'slugged', 'slugged-2', 17, 1, '2026-02-14 19:44:10', '2026-02-17 14:38:03'),
(4, 'Yangi Brand2', 'yangi-brand2', 18, 1, '2026-02-17 16:04:58', '2026-02-17 16:05:05');

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
(1, NULL, 'Test', 'test', 'test', 'icon', 5, NULL, 1, 0, '2026-02-14 00:11:44', '2026-02-17 13:11:22'),
(2, NULL, 'testnasmeasa', 'testnasmeasda', 'testnasmeasa', '', 1, '\"{\\\"a\\\":\\\"b\\\"}\"', 1, 0, '2026-02-14 00:12:14', '2026-02-17 16:19:23'),
(3, NULL, 'testnasme', 'testnasme', NULL, '', 1, '\"{\\\"a\\\":\\\"b\\\"}\"', 1, 0, '2026-02-14 20:58:56', '2026-02-16 17:56:24'),
(4, NULL, 'testnasme', 'testnasme', 'testnasme', 'icon', 2, '\"{\\\"a\\\":\\\"b\\\"}\"', 1, 0, '2026-02-15 22:14:20', '2026-02-16 17:56:27'),
(5, NULL, 'Protsessor111', 'Protsessor Ru', 'protsessor111', 'Icon Name', 15, '\"{\\\"fields\\\":[{\\\"key\\\":\\\"key\\\",\\\"label_uz\\\":\\\"Yadro\\\",\\\"label_ru\\\":\\\"Yadro\\\",\\\"type\\\":\\\"number\\\"}]}\"', 0, 1, '2026-02-17 11:54:02', '2026-02-17 13:06:23'),
(6, 1, 'Extiyot Qismlar', 'Extiyot Qismlar Ru', 'extiyot-qismlar', 'icon', 16, '\"{\\\"fields\\\":[]}\"', 0, 0, '2026-02-17 11:54:47', '2026-02-17 13:10:35'),
(7, 1, 'Extiyot Qismlar', 'Extiyot Qismlar Ru', 'extiyot-qismlar-2', 'icon', 16, '\"{\\\"fields\\\":[]}\"', 0, 0, '2026-02-17 11:54:55', '2026-02-17 13:09:33'),
(8, NULL, 'new', 'new', 'new', 'new', 17, '\"{\\\"fields\\\":[]}\"', 0, 0, '2026-02-17 12:56:22', '2026-02-17 13:11:03'),
(9, 5, 'new', 'new', 'new-2', 'new', NULL, '\"{\\\"fields\\\":[]}\"', 0, 0, '2026-02-17 13:12:13', '2026-02-17 13:12:21');

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
(1, 'ilovepdf_pages-to-jpg (7)', '1a', 'zip', '{\"original\":\"/upload/files/1771070188_jKOCE-qN.zip\"}', 1, '2026-02-14 16:56:28', '2026-02-14 16:56:28'),
(2, 'Insomnia.Core-12.0.0', '2a', 'exe', '{\"original\":\"/upload/files/1771070536_z3hE11vS.exe\"}', 1, '2026-02-14 17:02:22', '2026-02-14 17:02:22'),
(3, 'test-file', 'test-file', 'png', '{\"original\":\"/upload/files/1771234416_Yk2SywnN.png\",\"small\":\"/upload/files/1771234416_Yk2SywnN_small.png\",\"medium\":\"/upload/files/1771234416_Yk2SywnN_medium.png\"}', 1, '2026-02-16 14:33:37', '2026-02-16 14:33:37'),
(4, 'test-file', 'test-file-2', 'png', '{\"original\":\"/upload/files/1771234519_96CqNkZE.png\",\"small\":\"/upload/files/1771234519_96CqNkZE_small.png\",\"medium\":\"/upload/files/1771234519_96CqNkZE_medium.png\"}', 1, '2026-02-16 14:35:19', '2026-02-16 14:35:19'),
(5, 'test-file', 'test-file-3', 'png', '{\"original\":\"/upload/files/1771247300_PnEiRvJH.png\",\"small\":\"/upload/files/1771247300_PnEiRvJH_small.png\",\"medium\":\"/upload/files/1771247300_PnEiRvJH_medium.png\"}', 1, '2026-02-16 18:08:20', '2026-02-16 18:08:20'),
(6, 'test-file', 'test-file-4', 'png', '{\"original\":\"/upload/files/1771307724_7kdzm7OM.png\",\"small\":\"/upload/files/1771307724_7kdzm7OM_small.png\",\"medium\":\"/upload/files/1771307724_7kdzm7OM_medium.png\"}', 1, '2026-02-17 10:55:25', '2026-02-17 10:55:25'),
(7, 'ChatGPT Image 21 окт. 2025 г., 15_37_43', 'chatgpt-image-21-2025-153743', 'png', '{\"original\":\"/upload/files/1771308537_ZuBums92.png\",\"small\":\"/upload/files/1771308537_ZuBums92_small.png\",\"medium\":\"/upload/files/1771308537_ZuBums92_medium.png\"}', 1, '2026-02-17 11:08:58', '2026-02-17 11:08:58'),
(8, 'ChatGPT Image 21 окт. 2025 г., 15_37_43', 'chatgpt-image-21-2025-153743-2', 'png', '{\"original\":\"/upload/files/1771308970_6dtoU8a5.png\",\"small\":\"/upload/files/1771308970_6dtoU8a5_small.png\",\"medium\":\"/upload/files/1771308970_6dtoU8a5_medium.png\"}', 1, '2026-02-17 11:16:11', '2026-02-17 11:16:11'),
(9, 'ChatGPT Image 21 окт. 2025 г., 15_37_43', 'chatgpt-image-21-2025-153743-3', 'png', '{\"original\":\"/upload/files/1771309111_GT0WaYT9.png\",\"small\":\"/upload/files/1771309111_GT0WaYT9_small.png\",\"medium\":\"/upload/files/1771309111_GT0WaYT9_medium.png\"}', 1, '2026-02-17 11:18:32', '2026-02-17 11:18:32'),
(10, 'ChatGPT Image 21 окт. 2025 г., 15_37_43', 'chatgpt-image-21-2025-153743-4', 'png', '{\"original\":\"/upload/files/1771309983_wlHGN9RI.png\",\"small\":\"/upload/files/1771309983_wlHGN9RI_small.png\",\"medium\":\"/upload/files/1771309983_wlHGN9RI_medium.png\"}', 1, '2026-02-17 11:33:04', '2026-02-17 11:33:04'),
(11, 'ChatGPT Image 21 окт. 2025 г., 15_45_33', 'chatgpt-image-21-2025-154533', 'png', '{\"original\":\"/upload/files/1771309993_koF7A18O.png\",\"small\":\"/upload/files/1771309993_koF7A18O_small.png\",\"medium\":\"/upload/files/1771309993_koF7A18O_medium.png\"}', 1, '2026-02-17 11:33:14', '2026-02-17 11:33:14'),
(12, 'ChatGPT Image 21 окт. 2025 г., 15_45_33', 'chatgpt-image-21-2025-154533-2', 'png', '{\"original\":\"/upload/files/1771310205_9Cn6Uf8i.png\",\"small\":\"/upload/files/1771310205_9Cn6Uf8i_small.png\",\"medium\":\"/upload/files/1771310205_9Cn6Uf8i_medium.png\"}', 1, '2026-02-17 11:36:45', '2026-02-17 11:36:45'),
(13, 'Dentify_Texnik_Kontseptsiya', 'dentifytexnikkontseptsiya', 'pdf', '{\"original\":\"/upload/files/1771310254_mIpL4WA5.pdf\"}', 1, '2026-02-17 11:37:34', '2026-02-17 11:37:34'),
(14, 'departments', 'departments', 'pdf', '{\"original\":\"/upload/files/1771310419_8sRRSRv0.pdf\"}', 1, '2026-02-17 11:40:19', '2026-02-17 11:40:19'),
(15, 'ChatGPT Image 21 окт. 2025 г., 15_37_43', 'chatgpt-image-21-2025-153743-5', 'png', '{\"original\":\"/upload/files/1771311239_JOEgtlZg.png\",\"small\":\"/upload/files/1771311239_JOEgtlZg_small.png\",\"medium\":\"/upload/files/1771311239_JOEgtlZg_medium.png\"}', 1, '2026-02-17 11:54:00', '2026-02-17 11:54:00'),
(16, 'ChatGPT Image 21 окт. 2025 г., 15_37_43', 'chatgpt-image-21-2025-153743-6', 'png', '{\"original\":\"/upload/files/1771311285_qD_cB_uk.png\",\"small\":\"/upload/files/1771311285_qD_cB_uk_small.png\",\"medium\":\"/upload/files/1771311285_qD_cB_uk_medium.png\"}', 1, '2026-02-17 11:54:46', '2026-02-17 11:54:46'),
(17, 'ChatGPT Image 21 окт. 2025 г., 15_37_43', 'chatgpt-image-21-2025-153743-7', 'png', '{\"original\":\"/upload/files/1771314972_TE8qPqy9.png\",\"small\":\"/upload/files/1771314972_TE8qPqy9_small.png\",\"medium\":\"/upload/files/1771314972_TE8qPqy9_medium.png\"}', 1, '2026-02-17 12:56:13', '2026-02-17 12:56:13'),
(18, 'ghost-modern-2560x1440-10953', 'ghost-modern-2560x1440-10953', 'jpg', '{\"original\":\"/upload/files/1771326296_WaJFArPf.jpg\",\"small\":\"/upload/files/1771326296_WaJFArPf_small.jpg\",\"medium\":\"/upload/files/1771326296_WaJFArPf_medium.jpg\"}', 1, '2026-02-17 16:04:56', '2026-02-17 16:04:56'),
(19, 'windows-11-stock-red-abstract-black-background-amoled-2560x1440-9058', 'windows-11-stock-red-abstract-black-background-amoled-2560x1440-9058', 'jpg', '{\"original\":\"/upload/files/1771326314_ElY32Uu7.jpg\",\"small\":\"/upload/files/1771326314_ElY32Uu7_small.jpg\",\"medium\":\"/upload/files/1771326314_ElY32Uu7_medium.jpg\"}', 1, '2026-02-17 16:05:14', '2026-02-17 16:05:14');

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
  `image_id` int(11) DEFAULT NULL,
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
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specifications`)),
  `stock_quantity` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 1,
  `featured` int(11) DEFAULT 1,
  `seo_title` varchar(255) NOT NULL DEFAULT '',
  `seo_description` text DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `image_id` int(11) DEFAULT NULL,
  `is_device` int(11) DEFAULT 1,
  `rating` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `category_id`, `brand_id`, `name_uz`, `name_ru`, `slug`, `description_uz`, `description_ru`, `sku`, `price`, `discount_price`, `discount_expires`, `specifications`, `stock_quantity`, `status`, `featured`, `seo_title`, `seo_description`, `created`, `updated`, `image_id`, `is_device`, `rating`) VALUES
(5, 1, '1', 'iPhone 15 Pro Max (Yangilangan)', 'iPhone 15 Pro Max (Обновленный)', 'iphone-15-pro-max-yangilangan', 'Apple kompaniyasining eng so\'nggi smartfoni - yangilangan versiya', 'Новейший смартфон компании Apple - обновленная версия', 'IPHONE-15-PRO-MAX-256', 14000000, 13500000, '2025-06-30 23:59:59', '\"{\\\"display\\\":\\\"6.7 inch OLED\\\",\\\"memory\\\":\\\"256GB\\\",\\\"ram\\\":\\\"8GB\\\",\\\"color\\\":\\\"Natural Titanium\\\"}\"', 100, 1, 1, 'iPhone 15 Pro Max arzon narxda', 'iPhone 15 Pro Max eng yaxshi narxda sotib oling', '2026-02-17 16:04:51', '2026-02-17 16:08:30', 17, 1, 5),
(6, 1, '1', 'iPhone 18 Pro Max (Yangilangan)', 'iPhone 18 Pro Max (Обновленный)', 'iphone-18-pro-max-yangilangan', 'Apple kompaniyasining eng so\'nggi smartfoni - yangilangan versiya', 'Новейший смартфон компании Apple - обновленная версия', 'IPHONE-18-PRO-MAX-256', 14000000, 13500000, '2025-06-30 23:59:59', '\"{\\\"display\\\":\\\"6.7 inch OLED\\\",\\\"memory\\\":\\\"256GB\\\",\\\"ram\\\":\\\"8GB\\\",\\\"color\\\":\\\"Natural Titanium\\\"}\"', 100, 1, 1, 'iPhone 18 Pro Max arzon narxda', 'iPhone 18 Pro Max eng yaxshi narxda sotib oling', '2026-02-21 20:36:05', '2026-02-21 20:38:02', 17, 1, 5),
(7, 1, '1', 'iPhone 16 Pro Max', 'iPhone 15 Pro Max', 'iphone-16-pro-max', 'Apple kompaniyasining eng so\'nggi smartfoni', 'Новейший смартфон компании Apple', '7374717637', 15000000, 14500000, '2024-12-31 23:59:59', '\"{\\\"fields\\\":[{\\\"display\\\":\\\"6.7 inch\\\",\\\"memory\\\":\\\"256GB\\\",\\\"ram\\\":\\\"8GB\\\",\\\"color\\\":\\\"Titanium Black\\\"}]}\"', 50, 1, 1, 'iPhone 15 Pro Max sotib olish', 'iPhone 15 Pro Max eng arzon narxda', '2026-02-21 21:01:01', '2026-02-21 21:01:01', 17, 1, 5);

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

--
-- Dumping data for table `product_guides`
--

INSERT INTO `product_guides` (`id`, `product_id`, `has_video`, `title_uz`, `title_ru`, `content_uz`, `content_ru`, `video_id`, `sort_order`, `status`, `created`, `updated`, `slug`) VALUES
(1, 5, 1, 'Qutidan chiqarish (yangilangan)', 'Распаковка (обновлено)', 'Telefonni qutidan ehtiyotkorlik bilan chiqaring', 'Аккуратно извлеките телефон из коробки', 1, 1, 1, '2026-02-21 20:41:22', '2026-02-17 16:08:30', 'qutidan-chiqarish-yangilangan'),
(2, 5, 0, 'Dastlabki sozlash', 'Первоначальная настройка', 'Telefonni yoqing va Apple ID bilan kiring', 'Включите телефон и войдите с Apple ID', NULL, 2, 1, '2026-02-21 20:41:23', '2026-02-17 16:04:51', 'dastlabki-sozlash'),
(3, 5, 1, 'Qutidan chiqarish', 'Распаковка', 'Telefonni qutidan ehtiyotkorlik bilan chiqaring', 'Аккуратно извлеките телефон из коробки', 1, 1, 1, '2026-02-21 20:41:25', '2026-02-17 16:07:05', 'qutidan-chiqarish-2'),
(4, 5, 0, 'Dastlabki sozlash', 'Первоначальная настройка', 'Telefonni yoqing va Apple ID bilan kiring', 'Включите телефон и войдите с Apple ID', NULL, 2, 1, '2026-02-21 20:41:26', '2026-02-17 16:07:05', 'dastlabki-sozlash-2'),
(5, 5, 1, 'Qutidan chiqarish', 'Распаковка', 'Telefonni qutidan ehtiyotkorlik bilan chiqaring', 'Аккуратно извлеките телефон из коробки', 1, 1, 1, '2026-02-21 20:41:28', '2026-02-17 16:07:16', 'qutidan-chiqarish-3'),
(6, 5, 0, 'Dastlabki sozlash', 'Первоначальная настройка', 'Telefonni yoqing va Apple ID bilan kiring', 'Включите телефон и войдите с Apple ID', NULL, 2, 1, '2026-02-21 20:41:29', '2026-02-17 16:07:16', 'dastlabki-sozlash-3'),
(7, 5, 0, 'Yangi qo\'llanma', 'Новое руководство', 'Bu yangi qo\'shilgan qo\'llanma', 'Это новое добавленное руководство', NULL, 3, 1, '2026-02-17 16:08:30', '2026-02-17 16:08:30', 'yangi-qollanma'),
(8, 6, 1, 'Qutidan chiqarisasdasdasdah', 'Распаковка', 'Telefonni qutidan ehtiyotkorlik bilan chiqaring', 'Аккуратно извлеките телефон из коробки', 1, 1, 1, '2026-02-21 20:41:20', '2026-02-21 20:41:14', 'qutidan-chiqarisasdasdasdah'),
(9, 7, 1, 'Qutidan chiqarish', 'Распаковка', 'Telefonni qutidan ehtiyotkorlik bilan chiqaring', 'Аккуратно извлеките телефон из коробки', 1, 1, 1, '2026-02-21 21:01:01', '2026-02-21 21:01:01', 'qutidan-chiqarish'),
(10, 7, 0, 'Dastlabki sozlash', 'Первоначальная настройка', 'Telefonni yoqing va Apple ID bilan kiring', 'Включите телефон и войдите с Apple ID', NULL, 2, 1, '2026-02-21 21:01:01', '2026-02-21 21:01:01', 'dastlabki-sozlash-4');

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

--
-- Dumping data for table `product_image`
--

INSERT INTO `product_image` (`id`, `product_id`, `image_id`, `alt_text`, `sort_order`, `is_primary`, `created`, `updated`, `status`) VALUES
(1, 5, 17, 'iPhone 15 Pro Max old tomoni (yangilangan)', 1, 1, '2026-02-17 16:04:51', '2026-02-21 20:45:52', 1),
(2, 5, 16, 'iPhone 15 Pro Max orqa tomoni', 2, 0, '2026-02-17 16:04:51', '2026-02-21 20:45:53', 1),
(3, 5, 17, 'iPhone 15 Pro Max old tomoni', 1, 0, '2026-02-17 16:07:05', '2026-02-21 20:45:54', 1),
(4, 5, 16, 'iPhone 15 Pro Max orqa tomoni', 2, 0, '2026-02-17 16:07:05', '2026-02-21 20:45:55', 1),
(5, 5, 17, 'iPhone 15 Pro Max old tomoni', 1, 0, '2026-02-17 16:07:16', '2026-02-21 20:45:56', 1),
(6, 5, 16, 'iPhone 15 Pro Max orqa tomoni', 2, 0, '2026-02-17 16:07:16', '2026-02-21 20:45:58', 1),
(7, 5, 18, 'Yangi rasm', 3, 0, '2026-02-17 16:08:30', '2026-02-17 16:08:30', 1),
(8, 6, 17, 'iPhone 15 Pro Max yangi tomoni', 1, 1, '2026-02-21 20:42:41', '2026-02-21 20:46:04', 1),
(9, 7, 17, 'iPhone 15 Pro Max old tomoni', 1, 1, '2026-02-21 21:01:01', '2026-02-21 21:01:01', 1),
(10, 7, 16, 'iPhone 15 Pro Max orqa tomoni', 2, 0, '2026-02-21 21:01:01', '2026-02-21 21:01:01', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_soft`
--

CREATE TABLE `product_soft` (
  `id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `created` datetime DEFAULT current_timestamp(),
  `updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_soft`
--

INSERT INTO `product_soft` (`id`, `file_id`, `product_id`, `name`, `status`, `created`, `updated`) VALUES
(1, 2, 5, 'iPhone Driver v2.0', 0, '2026-02-17 16:04:51', '2026-02-17 16:08:30'),
(2, 1, 5, 'iTunes Setup', 0, '2026-02-17 16:04:51', '2026-02-17 16:07:05'),
(3, 2, 5, 'iPhone Driver v1.0', 0, '2026-02-17 16:07:05', '2026-02-17 16:07:16'),
(4, 1, 5, 'iTunes Setup', 0, '2026-02-17 16:07:05', '2026-02-17 16:07:16'),
(5, 2, 5, 'iPhone Driver v1.0', 0, '2026-02-17 16:07:16', '2026-02-17 16:08:30'),
(6, 1, 5, 'iTunes Setup', 0, '2026-02-17 16:07:16', '2026-02-17 16:08:30'),
(7, 3, 5, 'Yangi dastur', 1, '2026-02-17 16:08:30', '2026-02-17 16:08:30'),
(8, 2, 5, 'iPhone Driver v1.0', 1, '2026-02-21 20:46:59', '2026-02-21 20:47:40'),
(9, 2, 7, 'iPhone Driver v1.0', 1, '2026-02-21 21:01:01', '2026-02-21 21:01:01'),
(10, 1, 7, 'iTunes Setup', 1, '2026-02-21 21:01:01', '2026-02-21 21:01:01');

-- --------------------------------------------------------

--
-- Table structure for table `rating`
--

CREATE TABLE `rating` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rate` int(11) NOT NULL DEFAULT 5,
  `order_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `created` datetime DEFAULT current_timestamp(),
  `updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_payment_method_image_id` (`image_id`);

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
-- Indexes for table `product_soft`
--
ALTER TABLE `product_soft`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_product_soft_file_id` (`file_id`),
  ADD KEY `FK_product_soft_product_id` (`product_id`);

--
-- Indexes for table `rating`
--
ALTER TABLE `rating`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_rating_user_id` (`user_id`),
  ADD KEY `FK_rating_product_id` (`product_id`),
  ADD KEY `FK_rating_order_id` (`order_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `product_guides`
--
ALTER TABLE `product_guides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product_image`
--
ALTER TABLE `product_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product_soft`
--
ALTER TABLE `product_soft`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `rating`
--
ALTER TABLE `rating`
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
-- Constraints for table `payment_method`
--
ALTER TABLE `payment_method`
  ADD CONSTRAINT `FK_payment_method_image_id` FOREIGN KEY (`image_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

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
-- Constraints for table `product_soft`
--
ALTER TABLE `product_soft`
  ADD CONSTRAINT `FK_product_soft_file_id` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_product_soft_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `rating`
--
ALTER TABLE `rating`
  ADD CONSTRAINT `FK_rating_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_rating_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_rating_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

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
