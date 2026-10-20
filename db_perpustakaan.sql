-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 20, 2026 at 03:39 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_perpustakaan`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int NOT NULL,
  `code` varchar(30) NOT NULL,
  `title` varchar(150) NOT NULL,
  `author` varchar(100) NOT NULL,
  `publisher` varchar(100) DEFAULT '',
  `year_published` year DEFAULT NULL,
  `stock_total` int NOT NULL DEFAULT '1',
  `stock_available` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `code`, `title`, `author`, `publisher`, `year_published`, `stock_total`, `stock_available`, `created_at`) VALUES
(1, 'BK-001', 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', '2005', 5, 5, '2026-10-20 12:36:14'),
(2, 'BK-002', 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Lentera Dipantara', '1980', 3, 3, '2026-10-20 12:36:14'),
(3, 'BK-003', 'Filosofi Teras', 'Henry Manampiring', 'Kompas', '2018', 4, 4, '2026-10-20 12:36:14'),
(4, 'BK-004', 'Atomic Habits', 'James Clear', 'Gramedia', '2018', 2, 1, '2026-10-20 12:36:14');

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int NOT NULL,
  `transaction_code` varchar(40) NOT NULL,
  `book_id` int NOT NULL,
  `member_id` int NOT NULL,
  `loan_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('Dipinjam','Dikembalikan') NOT NULL DEFAULT 'Dipinjam',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `transaction_code`, `book_id`, `member_id`, `loan_date`, `due_date`, `return_date`, `status`, `created_at`) VALUES
(1, 'TRX-20261020132727-764', 4, 1, '2026-10-22', '2026-10-27', NULL, 'Dipinjam', '2026-10-20 13:27:27'),
(2, 'TRX-20261020150734-370', 3, 2, '2026-10-20', '2026-10-27', '2026-10-20', 'Dikembalikan', '2026-10-20 15:07:34');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int NOT NULL,
  `member_code` varchar(30) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(120) DEFAULT '',
  `phone` varchar(30) DEFAULT '',
  `address` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `member_code`, `name`, `email`, `phone`, `address`, `created_at`) VALUES
(1, 'AG-001', 'Nadia Putri', 'nadia@example.com', '081234567890', 'Bondowoso', '2026-10-20 12:36:14'),
(2, 'AG-002', 'Rizky Ramadhan', 'rizky@example.com', '081298765432', 'Jember', '2026-10-20 12:36:14'),
(3, 'AG-003', 'Siti Aisyah', 'siti@example.com', '081377788899', 'Bondowoso', '2026-10-20 12:36:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_code` (`transaction_code`),
  ADD KEY `fk_loan_book` (`book_id`),
  ADD KEY `fk_loan_member` (`member_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `member_code` (`member_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `fk_loan_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
  ADD CONSTRAINT `fk_loan_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
