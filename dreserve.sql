-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2026 at 01:55 PM
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
-- Database: `dreserve`
--

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `category` enum('makanan','minuman') NOT NULL DEFAULT 'makanan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `name`, `description`, `price`, `image_url`, `is_available`, `category`) VALUES
(1, 'Wagyu A5 Steak', 'Premium Japanese Wagyu Beef', 550000.00, 'public/img/wagyua5.webp', 1, 'makanan'),
(2, 'Truffle Pasta', 'Creamy pasta with black truffle', 120000.00, 'img/pasta.jpg', 1, 'makanan'),
(3, 'Golden Mojito', 'Signature mocktail with gold flakes', 65000.00, 'img/mojito.jpg', 1, 'minuman'),
(4, 'Lobster Thermidor', 'Lobster panggang mewah dengan saus krim keju parmesan dan jamur.', 450000.00, 'img/lobster.jpg', 1, 'makanan'),
(5, 'Foie Gras PoÛlÚ', 'Hati angsa premium pan-seared dengan karamel apel dan reduksi balsamic.', 380000.00, 'img/foie.jpg', 1, 'makanan'),
(6, 'Duck Confit', 'Paha bebek panggang lambat khas Prancis dengan kentang purÚe dan saus beri.', 250000.00, 'img/duckconfit.jpg', 1, 'makanan'),
(7, 'Black Truffle Risotto', 'Nasi risotto Italia autentik dimasak dengan jamur truffle hitam dan kaldu.', 210000.00, 'img/blacktruffle.jpg', 1, 'makanan'),
(8, 'Signature Old Fashioned', 'Minuman klasik dengan bourbon premium, bitters, dan sentuhan kulit jeruk.', 150000.00, 'img/signature.jpg', 1, 'minuman'),
(9, 'Matcha Espresso Fusion', 'Perpaduan unik matcha Jepang premium dengan espresso arabica murni.', 85000.00, 'img/matcha.jpg', 1, 'minuman'),
(10, 'Artisan Kombucha', 'Teh fermentasi organik dengan pilihan rasa beri liar atau markisa segar.', 55000.00, 'img/uploads/menu_69f504c6728893.53565161.jpg', 1, 'minuman'),
(11, 'Blue Ocean Drink', 'Minuman segar non-alkohol dari curacao biru, soda lemon, dan daun mint.', 65000.00, 'img/blueocean.jpg', 1, 'minuman'),
(12, 'Espresso Martini', 'Koktail berkelas dengan vodka, kahlua, dan ekstrak kopi espresso.', 180000.00, 'img/espreso.png', 1, 'minuman');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('bank_transfer','e_wallet') NOT NULL,
  `status` enum('pending','success','failed','refunded') DEFAULT 'pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `reservation_id`, `amount`, `payment_method`, `status`, `payment_date`) VALUES
(1, 1, 100000.00, 'e_wallet', 'success', '2026-04-30 13:17:14'),
(2, 2, 100000.00, 'bank_transfer', 'success', '2026-04-30 13:56:33'),
(9, 12, 610000.00, 'e_wallet', 'refunded', '2026-05-01 19:41:39'),
(10, 13, 730000.00, 'e_wallet', 'refunded', '2026-05-03 11:37:54');

-- --------------------------------------------------------

--
-- Table structure for table `pre_orders`
--

CREATE TABLE `pre_orders` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_order` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pre_orders`
--

INSERT INTO `pre_orders` (`id`, `reservation_id`, `menu_id`, `quantity`, `price_at_order`) VALUES
(1, 1, 2, 1, 120000.00),
(2, 2, 3, 2, 65000.00),
(11, 11, 3, 2, 65000.00),
(12, 12, 11, 2, 65000.00),
(13, 12, 5, 1, 380000.00),
(14, 13, 12, 1, 180000.00),
(15, 13, 4, 1, 450000.00);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `reservation_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `table_id`, `reservation_date`, `start_time`, `end_time`, `status`, `created_at`) VALUES
(1, 2, 1, '2026-04-30', '22:00:00', '00:00:00', 'pending', '2026-04-30 13:17:00'),
(2, 3, 4, '2026-04-30', '23:00:00', '02:00:00', 'completed', '2026-04-30 13:56:01'),
(11, 3, 1, '2026-05-01', '18:00:00', '20:00:00', 'pending', '2026-05-01 07:39:15'),
(12, 3, 2, '2026-05-01', '18:00:00', '20:00:00', 'cancelled', '2026-05-01 19:41:08'),
(13, 3, 1, '2026-05-03', '18:00:00', '20:00:00', 'cancelled', '2026-05-03 11:36:35');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `id` int(11) NOT NULL,
  `table_number` varchar(20) NOT NULL,
  `capacity` int(11) NOT NULL,
  `status` enum('available','occupied','reserved') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`id`, `table_number`, `capacity`, `status`) VALUES
(1, 'T01', 2, 'available'),
(2, 'T02', 2, 'available'),
(3, 'T03', 4, 'available'),
(4, 'T04', 4, 'available'),
(5, 'V01', 6, 'available'),
(6, 'V02', 8, 'available'),
(7, 'V03', 10, 'available');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `phone`) VALUES
(1, 'Admin Default', 'admin@dreserve.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2026-04-30 13:12:04', NULL),
(2, 'Budi Santoso', 'budi@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '2026-04-30 13:12:04', NULL),
(3, 'Mirza Fajrianshah', 'mrz@gmail.com', '$2y$10$/s2t9Els7lcFnIaSSzPFxul.fTskx91VdK6.7opavFMD0Jb5Ywlc.', 'customer', '2026-04-30 13:55:19', '08123423424');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `pre_orders`
--
ALTER TABLE `pre_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `table_id` (`table_id`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `table_number` (`table_number`);

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
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pre_orders`
--
ALTER TABLE `pre_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pre_orders`
--
ALTER TABLE `pre_orders`
  ADD CONSTRAINT `pre_orders_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pre_orders_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
