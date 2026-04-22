-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2026 at 11:17 PM
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
-- Database: `solirestaurant`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$Rp5i9Jr1ycU5tAG91c6MeejFl2MIcIQROhGlpi1XeuxJG/yDz4Mu.');

-- --------------------------------------------------------

--
-- Table structure for table `dishes`
--

CREATE TABLE `dishes` (
  `dish_id` int(11) NOT NULL,
  `dish_name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `cuisine_type` varchar(250) NOT NULL,
  `price` decimal(6,2) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `dietary_labels` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dishes`
--

INSERT INTO `dishes` (`dish_id`, `dish_name`, `category`, `cuisine_type`, `price`, `image_url`, `dietary_labels`) VALUES
(1, 'Couscous', 'main course', 'Moroccan', 20.00, 'https://www.munatycooking.com/wp-content/uploads/2023/03/Lamb-Shanks-Couscous-feature-image.jpg', 'halal'),
(2, 'Chicken Tagine', 'main course', 'Moroccan', 21.00, 'https://hilltoprecipes.com/wp-content/uploads/2025/03/Chicken-tagine-3.jpg', 'halal,gluten-free'),
(3, 'Zaalouk - Eggplant Salad', 'starter', 'Moroccan', 7.00, 'https://www.themediterraneandish.com/wp-content/uploads/2022/12/zaalouk-FINAL-17.jpg', 'vegan,halal,gluten-free'),
(4, 'Taktouka - Tomato and Roasted Bell Salad', 'starter', 'Moroccan', 6.00, 'https://www.themediterraneandish.com/wp-content/uploads/2023/10/taktouka-recipe-2.jpg', 'vegan,halal,gluten-free'),
(5, 'Gazelle', 'dessert', 'Moroccan', 20.00, 'https://dingonuts.com/wp-content/uploads/2025/10/Moroccan-date-Kaab-el-Ghazal.jpeg', 'halal'),
(6, 'Almond Cigars', 'dessert', 'Moroccan', 8.00, 'https://i.pinimg.com/736x/a6/71/30/a6713005e7cd399c4fb220ab3915b494.jpg', 'halal'),
(7, 'Steak Pizzaiola', 'main course', 'Italian', 30.00, 'https://www.sipandfeast.com/wp-content/uploads/2023/09/steak-pizzaiola-recipe-snippet-2.jpg', NULL),
(8, 'Chicken Cutlets', 'main course', 'Italian', 25.00, 'https://iamhomesteader.com/wp-content/uploads/2024/05/easy-chicken-cutlets-3.jpg', 'halal'),
(9, 'Bruschetta', 'starter', 'Italian', 12.00, 'https://kjsfoodjournal.com/wp-content/uploads/2020/07/tomato-bruschetta.jpg', 'vegan'),
(10, 'Carpaccio', 'starter', 'Italian', 14.00, 'https://i1.vrs.gd/gladkokken/uploads/images/DSC_2227.jpg?width=700&format=jpg&quality=80', NULL),
(11, 'Tiramisu Dessert', 'dessert', 'Italian', 10.00, 'https://www.kingarthurbaking.com/sites/default/files/2023-03/Tiramisu_1426.jpg', 'halal'),
(12, 'Biscoff Affogato', 'dessert', 'Italian', 9.00, 'https://heinstirred.com/wp-content/uploads/2022/04/Affogato-3-480x270.jpg', 'halal,gluten-free'),
(13, 'Kung Pao Chicken', 'main course', 'Chinese', 10.00, 'https://cdn.cleaneatingmag.com/wp-content/uploads/2013/04/kung-pao-chicken.jpg', 'halal'),
(14, 'Sweet and Sour Chicken', 'main course', 'Chinese', 8.00, 'https://assets.epicurious.com/photos/5995cb8f4ac63114bc4a09c9/1:1/w_2560%2Cc_limit/_22-Minute-Sweet-and-Sour-Chicken-recipe-14082017.jpg', 'halal'),
(15, 'Spring Rolls', 'starter', 'Chinese', 5.00, 'https://www.elmundoeats.com/wp-content/uploads/2024/02/Crispy-spring-rolls.jpg', 'vegan'),
(16, 'Shumai', 'starter', 'Chinese', 6.00, 'https://twoplaidaprons.com/wp-content/uploads/2023/11/shumai-steamed-and-topped-with-smelt-thumbnail.jpg', NULL),
(17, 'Sesame Balls', 'dessert', 'Chinese', 7.00, 'https://redhousespice.com/wp-content/uploads/2023/10/Chinese-sesame-balls-a.jpg', 'vegan,halal,gluten-free'),
(18, 'Egg Tarts', 'dessert', 'Chinese', 3.00, 'https://admin.misstamchiak.com/wp-content/uploads/2016/01/DSCF5920-1.jpg', 'halal'),
(19, 'Seafood Paella', 'main course', 'Spanish', 26.00, 'https://www.foodandwine.com/thmb/Qy4Bgev5F_TZAfQk-Cy_bsiWq1c=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/FAW-recipes-seafood-and-chicken-paella-chorizo-hero-06-47a924fbdc534b6f937f4991a7b4cafc.jpg', 'halal,gluten-free'),
(20, 'Croquetas', 'starter', 'Spanish', 8.00, 'https://assets.tmecosys.com/image/upload/t_web_rdp_recipe_584x480/img/recipe/ras/Assets/D70D4414-A027-439D-9F8A-D9C5C7243E73/Derivates/708A6D05-7988-4920-B36D-E6B5BC645B7A.jpg', NULL),
(21, 'Garlic Shrimp', 'starter', 'Spanish', 11.00, 'https://www.allrecipes.com/thmb/B2u5-9BgtvF7BRvfOOT2oRfNO7E=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/266085-spanish-garlic-shrimp-DDMFS-beauty-4x3-e4e199450d704150b787a0eb59640add.jpg', 'halal,gluten-free'),
(22, 'Flan', 'dessert', 'Spanish', 12.00, 'https://www.incredibleegg.org/wp-content/uploads/2015/06/classic-flan.jpeg', 'halal,gluten-free'),
(23, 'Arroz con Leche', 'dessert', 'Spanish', 6.00, 'https://gourmet.iprospect.cl/wp-content/uploads/2016/09/Arroz-con-leche.jpg', 'halal,gluten-free'),
(24, 'Crema Catalana', 'dessert', 'Spanish', 7.50, 'https://licor43.com/wp-content/uploads/2025/10/crema-catalana-43-900x900.webp', 'halal,gluten-free'),
(25, 'Steak Frites', 'main course', 'French', 33.00, 'https://mealpractice.b-cdn.net/50684711927943168/steak-frites-with-garlic-butter-bqb9XgY44u.webp', NULL),
(26, 'Chicken Cassoulet', 'main course', 'French', 25.00, 'https://diethood.com/wp-content/uploads/2021/12/chicken-cassoulet-5.jpg', 'halal,gluten-free'),
(27, 'Steak Tartare', 'starter', 'French', 11.00, 'https://upload.wikimedia.org/wikipedia/commons/d/db/Classic_steak_tartare.jpg', NULL),
(28, 'French Onion Soup', 'starter', 'French', 8.00, 'https://www.recipetineats.com/tachyon/2018/11/French-Onion-Soup_1.jpg', NULL),
(29, 'Crème Brûlée', 'dessert', 'French', 5.85, 'https://barefeetinthekitchen.com/wp-content/uploads/2025/04/Creme-Brulee-BFK-9-1-of-1.jpg', 'halal,gluten-free'),
(30, 'Macarons', 'dessert', 'French', 12.50, 'https://assets.tmecosys.com/image/upload/t_web_rdp_recipe_584x480/img/recipe/ras/Assets/B328847A-522B-4987-BCDC-7E99AC83320B/Derivates/5452D393-45A1-4D35-920F-4D48D3BA5DB6.jpg', 'gluten-free,halal'),
(31, 'Mango Pomelo Sago', 'dessert', 'Chinese', 10.00, 'https://assets.tmecosys.com/video/upload/t_web_rdp_recipe_584x480/videos/Malaysia/920636_mango_pomelo_sago_video.jpg', 'vegan,halal,gluten-free');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `user_id`, `rating`, `message`, `created_at`) VALUES
(3, 1, 5, 'Hello', '2026-04-15 17:49:37'),
(4, 6, 3, 'Hello test order from Bob', '2026-04-22 18:49:21');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` char(4) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` varchar(100) DEFAULT 'en attente',
  `user_id` int(11) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT 'unpaid',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `payment_currency` char(3) DEFAULT 'GBP',
  `stripe_checkout_session_id` varchar(255) DEFAULT NULL,
  `stripe_payment_intent_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_date`, `status`, `user_id`, `payment_status`, `payment_method`, `payment_amount`, `payment_currency`, `stripe_checkout_session_id`, `stripe_payment_intent_id`) VALUES
('C001', '2025-02-18 12:30:00', 'pending', 1, 'unpaid', NULL, NULL, 'GBP', NULL, NULL),
('C002', '2025-02-17 14:15:00', 'in progress', 2, 'unpaid', NULL, NULL, 'GBP', NULL, NULL),
('C003', '2025-02-16 19:45:00', 'shipped', 3, 'unpaid', NULL, NULL, 'GBP', NULL, NULL),
('C004', '2025-02-15 11:20:00', 'delivered', 4, 'unpaid', NULL, NULL, 'GBP', NULL, NULL),
('C006', '2026-02-03 21:45:27', 'pending', 1, 'unpaid', NULL, NULL, 'GBP', NULL, NULL),
('C007', '2026-02-03 21:45:32', 'pending', 1, 'unpaid', NULL, NULL, 'GBP', NULL, NULL),
('C010', '2026-02-03 23:40:21', 'pending', 2, 'unpaid', NULL, NULL, 'GBP', NULL, NULL),
('C011', '2026-02-04 00:47:17', 'delivered', 1, 'unpaid', NULL, NULL, 'GBP', NULL, NULL),
('C015', '2026-04-13 13:42:29', 'pending', 4, 'unpaid', NULL, NULL, 'GBP', NULL, NULL),
('C016', '2026-04-22 18:48:49', 'delivered', 6, 'paid', 'stripe', 80.00, 'GBP', 'cs_test_b1HKtT0x6ZbUTJkQuFZsDtl4M2Lwq4bvfPFAF6HZEATpyBlo9yRkWeaY0E', 'pi_3TP4tBIE0LgVu08i0lwtJSQu'),
('C017', '2026-04-22 19:34:48', 'pending', 7, 'paid', 'stripe', 21.00, 'GBP', 'cs_test_a177Rbe7fZlFxqL5povVGvMXnagidSGLhAX1FXbe6XprDMtq9Ob7UfaP0E', 'pi_3TP5bgIE0LgVu08i1ErcPYZ1');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `dish_id` int(11) NOT NULL,
  `order_id` char(4) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`dish_id`, `order_id`, `quantity`) VALUES
(1, 'C001', 2),
(1, 'C006', 1),
(2, 'C001', 1),
(2, 'C015', 2),
(2, 'C017', 1),
(3, 'C002', 3),
(3, 'C007', 1),
(3, 'C011', 1),
(3, 'C015', 1),
(4, 'C003', 1),
(4, 'C010', 1),
(5, 'C004', 2),
(7, 'C016', 1),
(26, 'C016', 2);

-- --------------------------------------------------------

--
-- Table structure for table `payment_sessions`
--

CREATE TABLE `payment_sessions` (
  `stripe_checkout_session_id` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `items_json` longtext NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'GBP',
  `processed_order_id` char(4) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `processed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_sessions`
--

INSERT INTO `payment_sessions` (`stripe_checkout_session_id`, `user_id`, `items_json`, `total_amount`, `currency`, `processed_order_id`, `created_at`, `processed_at`) VALUES
('cs_test_a177Rbe7fZlFxqL5povVGvMXnagidSGLhAX1FXbe6XprDMtq9Ob7UfaP0E', 7, '[{\"id\":2,\"dish_name\":\"Chicken Tagine\",\"image_url\":\"https:\\/\\/hilltoprecipes.com\\/wp-content\\/uploads\\/2025\\/03\\/Chicken-tagine-3.jpg\",\"price\":21,\"quantity\":1,\"line_total\":21}]', 21.00, 'GBP', 'C017', '2026-04-22 19:33:59', '2026-04-22 19:34:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `last_name`, `first_name`, `email`, `phone`, `password_hash`) VALUES
(1, 'Jonny', 'Nguyen', 'w1977@gmail.com', '0695145678', '$2y$10$ZAkzxgjsikgpoMLWe06PyeDhwbvoQaxMPqY5z5eHB2xn7EhHH4SPi'),
(2, 'Bob', 'Charlton', NULL, '0243456789', NULL),
(3, 'Milly', 'Milton', NULL, '0931567291', NULL),
(4, 'James', 'Brown', NULL, '0111111111', NULL),
(5, 'Arron', 'Hello', NULL, '0826781017', NULL),
(6, 'Jelly', 'Bob', 'bob123@gmail.com', '987654321', '$2y$10$Ty47/t2WtKpTRlj7YOS5z.HrZ1Yj7cErH9bO0tLxJ8eQE7L.Npiqy'),
(7, 'sir', 'hello', 'sir1@gmail.com', '123459876', '$2y$10$CIBr.uCkFD9hCNfbZHmRRuLILMiosetmEvUH/6tDvCzGaKFnGX3Lu');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dishes`
--
ALTER TABLE `dishes`
  ADD PRIMARY KEY (`dish_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `feedback_user_fk` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `unique_stripe_checkout_session_id` (`stripe_checkout_session_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`dish_id`,`order_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `payment_sessions`
--
ALTER TABLE `payment_sessions`
  ADD PRIMARY KEY (`stripe_checkout_session_id`),
  ADD KEY `payment_sessions_user_fk` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dishes`
--
ALTER TABLE `dishes`
  MODIFY `dish_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`dish_id`) REFERENCES `dishes` (`dish_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `payment_sessions`
--
ALTER TABLE `payment_sessions`
  ADD CONSTRAINT `payment_sessions_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
