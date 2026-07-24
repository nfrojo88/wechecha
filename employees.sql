-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 16, 2026 at 08:48 AM
-- Server version: 11.4.12-MariaDB
-- PHP Version: 8.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wecheccc_store_mgmt`
--

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `employee_id_number` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `base_salary` text DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `salary` decimal(15,2) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `employment_date` date DEFAULT NULL,
  `educational_background` text DEFAULT NULL,
  `educational_file` text DEFAULT NULL,
  `experience_years` int(11) DEFAULT 0,
  `experience_file` text DEFAULT NULL,
  `application_letter_file` varchar(255) DEFAULT NULL,
  `id_card_file` varchar(255) DEFAULT NULL,
  `license_file` varchar(255) DEFAULT NULL,
  `phone_number_2` varchar(50) DEFAULT NULL,
  `guarantee_letter_file` varchar(255) DEFAULT NULL,
  `contract_type` varchar(50) DEFAULT 'Full-Time',
  `subcontractor_id` int(11) DEFAULT NULL,
  `site_id` int(11) DEFAULT NULL,
  `bank_info` text DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `transport_allowance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `house_allowance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `position_allowance` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_id_number`, `user_id`, `full_name`, `department`, `designation`, `phone_number`, `base_salary`, `position`, `joining_date`, `salary`, `status`, `created_at`, `employment_date`, `educational_background`, `educational_file`, `experience_years`, `experience_file`, `application_letter_file`, `id_card_file`, `license_file`, `phone_number_2`, `guarantee_letter_file`, `contract_type`, `subcontractor_id`, `site_id`, `bank_info`, `rating`, `transport_allowance`, `house_allowance`, `position_allowance`) VALUES
(3, 'EMP-26', 21, 'Kibrom Hailu', 'Engineering', 'planning', '0953134955', 'FZiAYIab2zZPX52xYsPvnuzjay2LOswXIsQtK3kpM442ii9xjZZbhxNH+DVbVeDo7j/jhwwV6QRRwVKGqJg0j5/pCSibLwVxhI0hiCeBMuoxcePUML6cTILCEoeu9nbyGvKBbLt9kSonBMzaavvDpg3cA+pDdm3CKSYgrclVI5+VbGD5TgFQ/a2cikfcJ1pEMwCuXgEgbB6mM7lIAKY7qQ/ZHIbYu7t3nz/fQvDLYsNljN1V001TlgD8XX9EP9Nsrwqn+/kz70W6adzqxb/Mv/1sJb2cpbnIm4fXQusf8nP4UgrplbhWTw+48tFKs5Ekraw55u/IZlp4viwtbdy/2Q==', NULL, NULL, NULL, 'active', '2026-06-22 12:52:57', '2026-02-09', 'BSC DEGREE IN CIVIL ENGINEERING ADDIS  COLLEGE', 'educational_file_6a3932294ed68.jpg', 3, 'experience_file_6a3932294ef49.jpg', 'application_letter_file_6a3932294f04a.jpg', 'id_card_file_6a3932294f150.jpg', 'license_file_6a3930296c1d8.jpg', '0946417461', 'guarantee_letter_file_6a3932294f24f.jpg', 'Full-Time', NULL, NULL, '[{\"bank_name\":\"CBE\",\"account_number\":\"1000431891526\"}]', NULL, 1515.00, 3500.00, 3700.00),
(4, 'EMP-18', NULL, 'Mengstu Fente', 'Machine Operator', 'Driver', '0929284504', 'jnNYVf1uCsBUtUd/oiThKL0mu6au6JaixmLDhAVN1v4qbBkYzvHnASljA2CvATvstT08da70fexmXvE2Cthbg4R+z0zWp+nIEVShFgH4qvh0lRZJ/S7mIGjK6Jxps2KvYkhfozY/2Dw+y4rRG/pxIeTUyui5nce4Dcsz+9b8IM9VPjZDFfGFqwseNyWt97pSzcAUuHhypWJ7oYv0WYHLJObRY1LtNVOQvosv+b5oQ244ggi8cdvdm6qLUfSE4HRDbB9YSKrZ+dHg7AdEWM6IkrebEXHoKXL+pMNMEe2WVON0ZXYAwfnm9My6F80nZZroAQ262mH2yA5ARHLArDBqqw==', NULL, NULL, NULL, 'active', '2026-06-22 13:17:13', '2025-01-28', 'GRADE 10', 'educational_file_6a3935d94399f.jpg', 0, NULL, 'application_letter_file_6a3935d943b8e.jpg', 'id_card_file_6a3935d943c57.jpg', NULL, '0925048276', 'guarantee_letter_file_6a3935d943d43.jpg', 'Full-Time', NULL, NULL, '[{\"bank_name\":\"CBE\",\"account_number\":\"1000100180581\"}]', NULL, 1160.00, 2200.00, 2200.00),
(5, 'EMP-07', 19, 'Keder Mohammed', 'Engineering', 'planning', '0913940566', 'mr6t/GciAQi5/b2tubAvgpAD+b8EvgW9IH7hZDmCvfMNkyrWeRJMEB/KhbaSraoMvE1rOOW5EmDDcvGn4IHyTaQJEF4bZbd5hP4yMmjl2OXla6eE2hPEA5V6hoiuO054F8HLL6YK497CFAbnz3b0HvumvnrLDcbixK81Dlh61LP2EJmh7M1jrdYqKxkbz0xPk4/5N80WgpH6bAPqI5rCaKZJqVlpgQ923vKSfZ3Vcmo4qscuPrP1N58Cja+VAnO6EEjberPS09M4oFjteXyzJL3/SeNf2MUB/9Y5ggjPti8uSM0fbbhmX4pS4SwX78knMrwLdr48bLhISMCbRvU4Pg==', NULL, NULL, NULL, 'active', '2026-06-22 13:42:14', '2025-08-11', 'BSc degree Civil Engineering Haramaya University', 'educational_file_6a393bb6b2420.jpg', 0, NULL, 'application_letter_file_6a393bb6b25d7.jpg', 'id_card_file_6a393bb6b26f5.jpg', 'license_file_6a393bb6b27e7.jpg', '0910446161', 'guarantee_letter_file_6a393bb6b291d.jpg', 'Full-Time', NULL, NULL, '[{\"bank_name\":\"CBE\",\"account_number\":\"1000246451242\"}]', NULL, 1160.00, 3500.00, 2700.00),
(6, 'EMP-31', 17, 'Merge Adane', 'Engineering', 'planning_manager', '0921918100', 'jRMFHnNW6imTfxa7xcsNKtfZpUdnw4wCH+UoUsk5JztBEqaIPC6F0sfbN4wyejuuLcf/Ng4mOfvZUMUGi7XgcBfYZhD95/zU7ueNc8zFh8JVYpzhFbccdRwlmzwyNqrY6aWmqJ+Cv0fA6O44jpBb+tp6zaTOdvdgUZ3kqEef7Z6znbW7saYMj/MilJ3Zg4nJ8R0hVp7sD20+TiSZMoJgo6nKRZQOJ2ArtK553/mb+RQaLcrYuRQHJe+0bWRvM9N1uruFrTJYqf/+qtJq0z1DktzJo7O5vfhdiuau1FmdaLjrs6Jz1dndta2F83UHMPsurPRA7wRt3gmBuBjgZlE8jA==', NULL, NULL, NULL, 'active', '2026-06-22 13:53:29', '2026-06-22', 'BSc civil Engineering Madda Walabu University', 'educational_file_6a393e5988d6c.jpg', 3, 'experience_file_6a393e5988f64.jpg', 'application_letter_file_6a393e59890fc.jpg', 'id_card_file_6a393e5989254.jpg', 'license_file_6a393e59893ac.jpg', '0911128689', 'guarantee_letter_file_6a393e59895b9.jpg', 'Full-Time', NULL, NULL, '[{\"bank_name\":\"CBE\",\"account_number\":\"1000091874273\"}]', NULL, 1800.00, 4000.00, 8000.00),
(7, 'EMP-13', 11, 'Dinaol Miressa', 'ICT / Technology', 'store_manager', '0917812107', 'JxSKfQkK2+op7VkK6MblgLjlt09uLxHeqxamjifwgLLj6LVeRF0PmCIlonc8ItV/KNjcI5udIykKIbdScLUCYxQtbS3yLKtdlqrXK7AJnVTfgZ1t9jGZ/GUAWCUFHnUdogKgN6sRmRKIapoxwQMl9uHeenmyZligCYmmHeARoSNTWqccFxIqtuFO+uHX/1nUzM1p1KkgIkz/dlQEd15phea04ZGtPuDTOVHvc6ojJHm97csQkbQAbbpHCMXYsr2P9cgXUEQQa7efjYg/pgmGJYJvbme4eZjNgZqalxGejo2JvDMPPZJD1j97sy9nkvdLGdl1A9u+w2INxJUvGpf/kw==', NULL, NULL, NULL, 'active', '2026-06-23 09:14:57', '2024-02-15', '[{\"background\":\"BSc Degree in Computer Science Mizan Tepi University\",\"file\":\"educational_file_6a3a4e33534c3.jpg\"}]', 'MULTIPLE', 0, '[]', 'application_letter_file_6a3a4e9129517.jpg', 'id_card_file_6a3a4e91297ec.jpg', NULL, '0947020572', 'guarantee_letter_file_6a3a4e9129b81.jpg', 'Full-Time', NULL, NULL, '[{\"bank_name\":\"CBE\",\"account_number\":\"1000301427674\"}]', NULL, 1725.00, 3000.00, 3000.00),
(8, 'EMP-0014', 13, 'Nebeyu Engedashet', 'Administration', 'hr', '0912280433', 'hh0qqzeBxPOVtobPrmaZF61GB2v5QYrS86pWrG/H+hK4iiVivawFYy6Yl9JUYcIRqlQJ/KW3kgILmGKr8tVecj/vg/lQLmiY5hR15hmJ+Coe6uNr3BiyII/B1HgiQBAhVAteQaDo4I9w6Nxn43uCDsXaQhiL7vmLgLmd/OpSkeClspkauZHjYyR7Aem1QEEBv55K/RK/O7CLM4B+x9A8ZfctWBNHCVMwiiu5kxKRBx24iJjlTqtoUsoTMp5K3VRF1iErYaw2JciJ2gcklxt/zu1huYYc7DQD+7rZ0gJ2TpvIyGTgDJl9eg/bWZzixXDuWK1d7dD63kMWwu7E7S6AKg==', NULL, NULL, NULL, 'active', '2026-06-24 10:37:47', '2023-10-12', '[{\"background\":\"BSc Degree in Building Construction Technology in FDRE TVT INSTITUTE\",\"file\":\"educational_file_6a3bb25cdef68.jpg\"}]', 'MULTIPLE', 0, '[]', 'application_letter_file_6a3bb37b92dbd.jpg', 'id_card_file_6a3bb37b92f8e.jpg', NULL, '0900486455', 'guarantee_letter_file_6a3bb37b930ba.jpg', 'Full-Time', NULL, NULL, '[{\"bank_name\":\"CBE\",\"account_number\":\"100079345192\"}]', NULL, 1990.00, 5400.00, 5000.00),
(9, 'EMP-08', 20, 'Alebachew Worku', 'Procurement', 'purchase', '0919511842', 'eK1UcUZX93XhbNwDJjKUyifXEy6dqbR57IRFBNqLL7Ygu3RCsgCU9aGB7zC1nK2t5azm5cZ2N+Cf066vD/HNcvZd8DxWmexz+mFK2jhoJEA+jYpVxtOBW89atkMkOQ25s/A/9p91odqU//zXnardL12XTVF5fQmmVjK7n/rWA/5UMJuW3+0mGHRJqJtZVfaTf7lvrqt7GpNunc7cA8grC/sWIyFzRlEiaFf6aSBOyasRvBpJySlhAswGsOvrCkrclU1jSNCr2v7uF6IQ/7nzwvUPxY1DULYp0F96YNAIX6Q2lrTnS/87eZA19F6uu27UthrF5mbFDjoI3S0dPfAiPw==', NULL, NULL, NULL, 'active', '2026-06-24 10:53:48', '2026-06-24', '[{\"background\":\"BSc Degree Science in Information Systems In AKSUM University\",\"file\":\"educational_file_6a3bb73c0947b.jpg\"}]', 'MULTIPLE', 0, '[]', 'application_letter_file_6a3bb73c09872.jpg', 'id_card_file_6a3bb73c09a22.jpg', NULL, '0930273146', 'guarantee_letter_file_6a3bb73c09b8c.jpg', 'Full-Time', NULL, NULL, '[{\"bank_name\":\"CBE\",\"account_number\":\"1000362823874\"}]', NULL, 1515.00, 3700.00, 3500.00),
(10, 'EMP-12', 18, 'Natanaem Tefera', 'Engineering', 'foreman', '0911478230', 'Djm4b1vL+FApSGbb93FNJMrRAj61G54c6iutyxUI+mPGvsCywI27usG+mN7BdnWY6QrKEfwbRhLZr1WKVivPHp6tcQIbrXM0AXRnQSDZZuTG+naBP5AjiLv4Zw9fXVrEhB1gbPImUBbPeDDGMsEKIJEPE2UE7kZkB7KjxS6vjAgILa3JY/e3tlE5RM+ZD+foPZdHd2Nwps1IY/1bqgfspt4h2+OoNU+cAvsAmPIDQ3hDcuFWQywAtCT68yqUaT5XOr24ipIHH+MPoMQ6pYBJrl9VTrqsPjx0nXhDdnsuiXXWkvdDjv9O0qyWsZs7yqtWGe9aaWOQrVXFD2W+0XRbMg==', NULL, NULL, NULL, 'active', '2026-06-24 11:52:04', '2024-02-07', '[{\"background\":\"Diploma Addis Ababa Tegebare-id college\",\"file\":\"educational_file_6a3bc48e51ea1.png\"}]', 'MULTIPLE', 12, '[{\"years\":3,\"file\":\"experience_file_6a3bc48e52269.png\"},{\"years\":3,\"file\":\"experience_file_6a3bc48e52668.png\"},{\"years\":1,\"file\":\"experience_file_6a3bc48e52a0f.png\"},{\"years\":2,\"file\":\"experience_file_6a3bc48e52d79.png\"},{\"years\":1,\"file\":\"experience_file_6a3bc48e53514.png\"},{\"years\":2,\"file\":\"experience_file_6a3bc48e53956.png\"}]', 'application_letter_file_6a3bc4e45cea1.jpg', 'id_card_file_6a3bc4e45d0a3.jpg', NULL, '0913063057', 'guarantee_letter_file_6a3bc4e45d501.jpg', 'Full-Time', NULL, NULL, '[{\"bank_name\":\"CBE\",\"account_number\":\"1000110390584\"}]', NULL, 1340.00, 3500.00, 3500.00),
(11, 'EMP-09', 30, 'Birtukan Azmeraw', 'Finance & Accounting', 'finance', '0928158900', 'jZi4Y9kHsEqXfBTkYqfYZUlb9sYjIxjk06yU8N9DsNE3nUReEbSZy/k+8MYcKCEigoFbk3CmnD1fPCSepA8QYYe5g7CIyChmjx9rm5lbZUBBIb1jSJ5Ssyff1MGf4uamM8jDwd5TLVqh7PkyiVe4l79wBEDKCYi1E7uQPQO/Ub54Bcvth8iVJ2YYbNe/Zc7Cq9JYUBioKCd17EXgaYkFtNixbVRIn6pVX94UntvxVtNfXyQnSTbafqhtVKN3XOjKmIWW2FkeA6BBIDg1EBhllZ/EiwIVzpiPHH75g2jthIIpOkoA2aNIXXbJSAYqCYm8geDv28hUZxcomhUmZBaeLA==', NULL, NULL, NULL, 'active', '2026-06-24 12:07:58', '2024-06-21', '[{\"background\":\"LEVEL 4 RIFT VALLEY UNIVERSITY\",\"file\":\"educational_file_6a3bc85965a2a.jpg\"}]', 'MULTIPLE', 0, '[]', 'application_letter_file_6a3bc89e9df51.jpg', 'id_card_file_6a3bc89e9e128.jpg', NULL, '0928158900', 'guarantee_letter_file_6a3bc89e9e282.jpg', 'Full-Time', NULL, NULL, '[{\"bank_name\":\"CBE\",\"account_number\":\"1000461323457\"}]', NULL, 820.00, 1200.00, 1200.00),
(12, 'EMP-05', 29, 'Chernet Tadesse', 'Finance & Accounting', 'finance_head', '0930760032', 'UuW1Zw9mYuO1w7C1j+AesthqFihb1jIbCJ2ulkYRNnOhi8vxu3x98zXTsRa6FNbtMPy6/RlTJJL4xswB/dZWdBuzBvxqbjaF4BLcmzijfwXPrAraH/4A82qfC2HHqico1eBywFGPOchw2sL//yHUCF49WGTKUGvKSZDWGzhA8fymcJp9MB353DXB/B3bUXbPwtZBN6F5jPwFQQulsOcsoOz9I0W9XeqnJTFP/knN1FC0v67GMbeBzIllI/ZWnt2ELkIPGePgVWRpDpxvn+fVO5M8IWV5Cc2Kb8VrlzjxUammHLI5CuvzwE72pcz8sv6UZp67Rc5kXxzBWuc7fcYoPA==', NULL, NULL, NULL, 'active', '2026-06-24 12:30:47', '2026-06-24', '[{\"background\":\"BSc RIFT VALLEY University Accounting and Finance\",\"file\":\"educational_file_6a3bcdbb8bd7c.jpg\"},{\"background\":\"Masters Of Business Administration in CUP Collage\",\"file\":\"educational_file_6a3bcdbb8bfdb.jpg\"}]', 'MULTIPLE', 4, '[{\"years\":4,\"file\":\"experience_file_6a3bcdbb8c281.jpg\"}]', 'application_letter_file_6a3bcdf709f9a.jpg', 'id_card_file_6a3bcdf70a237.jpg', NULL, '0923624714', 'guarantee_letter_file_6a3bcdf70a461.jpg', 'Full-Time', NULL, NULL, '[{\"bank_name\":\"CBE\",\"account_number\":\"1000200135575\"}]', NULL, 2200.00, 10000.00, 13000.00),
(13, 'EMP-20', 22, 'Mekdes Alemu', 'Finance & Accounting', 'finance', '0974362820', 'Sq3SOnoi0N1cwbMPPPaoNoiqqW8srhoY3lpu6bv/ewnn6IJTKPqMxA0D6MNvckSjSqj68Fzk6aeP7GlIARkVPENlA9P1oC0tBgeRFbCIL0X+RcYW77sVPMkFgKos+6Aty8nmVZdIwWKgEBNFrCcRm8UDK23ljfzZT92iBN4ym1oK2yc+GmLPiG2S+xldTdtCATW6El3/b2Mu1WQ/EqmApPSlRqZvW4sBxDXsBguNl2MOiTFtvvVheEXIST3hU/ng4rIJaMVT+ZdqBDK6kBc0SunmWUiCslzK9eaxKnOrSBrZ4HnSNsx1h/OQhn5LspZFRED64BC0cyYykB9hWuIv3Q==', NULL, NULL, NULL, 'active', '2026-07-04 06:48:55', '2026-01-14', '[{\"background\":\"Diploma In accounting  Admas university\",\"file\":\"educational_file_6a48ac7c291f3.jpg\"}]', 'MULTIPLE', 2, '[{\"years\":2,\"file\":\"experience_file_6a48acc6c2b11.jpg\"}]', 'application_letter_file_6a48ac7c29610.jpg', 'id_card_file_6a48ac7c2974e.jpg', NULL, '0920019194', 'guarantee_letter_file_6a48acd7d000c.jpg', 'Full-Time', NULL, NULL, '[{\"bank_name\":\"CBE\",\"account_number\":\"1000593903609\"}]', NULL, 1498.00, 440.00, 0.00),
(14, 'EMP-16', NULL, 'Adisu Dabe', 'Driver', 'Driver', '0912877500', 'DRz2gOjOYXskil1eKRU9oNm36g2/bciJV1S1bRse3hmcgY9bODzjRkvzkqb9mqdGUKgP7E35h9C9yDKu+mPg8oS09ns3YSB9xi1i4/xVdZxrBrjQRnYo2m77Ok/rUXo2XdvxY/c0V4K2+NN46qGmrB2BlGLKTgJnYvbO68MSwnWDi1fMo9G5l6q2Zq7B3esZRbomTwUywNnoULvc2PYgdvVQjeagIAaD/HNKlbUr0D6eMANHirAQQ0DL1nNiPZbGu3WUnazVih9E2LmhCxuoLA0UhcwrrdiHsYEA9yioxIR7M4/DUo9LGULfPLs4Qr6NR/6hgi4GQcTjGp79jqpaFw==', NULL, NULL, NULL, 'active', '2026-07-04 07:12:37', '2024-06-11', '[{\"background\":\"LICENSE\",\"file\":\"educational_file_6a48b265af174.jpg\"},{\"background\":\"Grade 10\",\"file\":\"educational_file_6a48b265af349.jpg\"}]', 'MULTIPLE', 0, '[]', 'application_letter_file_6a48b206d106a.jpg', 'id_card_file_6a48b206d1250.jpg', NULL, '0911394905', 'guarantee_letter_file_6a48b206d1365.jpg', 'Full-Time', NULL, NULL, '[{\"bank_name\":\"CBE\",\"account_number\":\"1000327811111\"}]', NULL, 1510.00, 0.00, 3000.00),
(15, 'EMP-23', 33, 'Mahlet Kibebew', 'Finance & Accounting', 'finance', '0943815197', 'OqzSMf37NsJ5hm4AlYIehiPtz1H42rA2mQqTjOeXhJIMeYZYjJLR3vhDb9Y0SWnRBJIP921nob7tdUS3JfGnLcoyBru7e1Ypd+JQnoFlTR2vQF6mX/Abj4xCVmd5SQnAdSXR1Xrouh6jbtxa54QY0PIvVA3DsHEYWbtcuOHLStoJQjFItnYL2liO1F18B9OZ5qUYW0Xc//X4NXKRyprU46yX9Y4aGw2z0825D0/M/x4lyZEpK5BE00r3VzkQvdJ+0wx9UcBkM4TmxgqPo+VVVGiuB4RL1fexP4HFP/Y4MnUhzp8MDkR77DQPgvWN9Y1mRzC3EAeXJ4gurC3V6ywFMQ==', NULL, NULL, NULL, 'active', '2026-07-04 07:20:13', '2025-03-26', '[{\"background\":\"BSc Accounting & Finance at Unity University\",\"file\":\"educational_file_6a48b40f19cc1.jpg\"}]', 'MULTIPLE', 0, '[]', 'application_letter_file_6a48b42dcf7f2.jpg', 'id_card_file_6a48b42dcf9dc.jpg', NULL, '0946756285', 'guarantee_letter_file_6a48b42dcfb2c.jpg', 'Full-Time', NULL, NULL, '[{\"bank_name\":\"CBE\",\"account_number\":\"1000297641917\"}]', NULL, 960.00, 1100.00, 1100.00),
(16, 'EMP-17', NULL, 'Habtamu Kebede', 'Driver', 'Driver', '0911124319', 'dDWswFQwT2fMV2diMz/NQl10IF2gN6QiI2Bsupk55qWkdbT0L2CuemVNSIVnwJfZxgyVMiJV7gs4QLiHbsAtbD4+rGLtYJwcbHyELB/zqP4GsL/1ZeNRXIJnS+4eGwTknY3bFOl2YlToOP69uPd2+5vXbqdU+d8dyntYL32OeVIIjGmqyGLI9cmGBmVSrOaYa8ucrjIHCX2gYG4LwXxPFme1tFYfnx62ILlcEvKQ1pfPTwKa0jMjpTCk6OnsGUbD2zSU7ocU0+EfHTMh06929CQwQtakezJmhxZF35vsE0lXur747nGyyXveVrPb3kbYRTIoumh3QrLEKR1vnSB+gQ==', NULL, NULL, NULL, 'active', '2026-07-04 07:27:29', '2026-03-04', '[{\"background\":\"LICENSE\",\"file\":\"educational_file_6a48b5ade0a68.jpg\"},{\"background\":\"Grade 10\",\"file\":\"educational_file_6a48b5ade0dd5.jpg\"}]', 'MULTIPLE', 0, '[]', 'application_letter_file_6a48b5e15fcf4.jpg', 'id_card_file_6a48b5e15fea0.jpg', NULL, '0911394905', 'guarantee_letter_file_6a48b5e16006e.jpg', 'Full-Time', NULL, NULL, '[{\"bank_name\":\"CBE\",\"account_number\":\"1000322436347\"}]', NULL, 1510.00, 0.00, 3000.00),
(17, 'EMP-11', NULL, 'Yosef Regassa', 'Driver', 'Driver', '0911393809', 'LxB8KnwbP83i/xv+hAN4AeBBVeXYFAHNzzireVabidjHRbWeS5MIKHLFTDjlNiATLHlgMAxqBdX+YCp0p6LLRbX8+BdN0awMo7vyzrrlFAiY5GrfoN855jlib3RbKhOp/mHY99FXKsniQkRByg80Sq1T8Sja6RV45vMytAOycP8EjwULsPIjYbM4YZjfBeIpB4NcMRIoWFVwRBBpDxZqewQfPOd71nIpgXn3LKDZY+GhzTbH6qLO5W58kO4t529eRlFr/Nb3YUjQbDx8ZtcKzARwiGf0MFMsRpmkENs6ax8Hu5kuiRsl491wrDMHgwsq4RQAEghEydPWZVf/8MpPJQ==', NULL, NULL, NULL, 'active', '2026-07-04 07:50:32', '2024-02-06', '[{\"background\":\"LICENSE\",\"file\":\"educational_file_6a48bb4821a77.jpg\"}]', 'MULTIPLE', 0, '[]', 'application_letter_file_6a48bb4821ba8.jpg', 'id_card_file_6a48bb4821ce9.jpg', NULL, '0921404438', 'guarantee_letter_file_6a48bb4821dd3.jpg', 'Full-Time', NULL, NULL, '[{\"bank_name\":\"CBE\",\"account_number\":\"1000522072293\"}]', NULL, 1214.00, 3500.00, 3500.00),
(18, 'EMP-25', 28, 'Natnael Tesfu', 'Human Resources', 'hr_officer', '0961253077', 'B5bJS836z5bBfogdRpbyvqkysGQ6xITQRgJjT5PYaX35u4Z8oCVC/COoPVAoPZsPKPoNOC2IIwbP11WnLoY2IlbwVvyPuEe+3JrBJxndnmC+2NEyDus9qUs+Lv5IKup/iQC7r9JxCVs+gT+NCD6n5HStcpiLswMZVhk6v3cZnAW1sQirEYSt/L8xgJq/IxpegrxLar9wCXoTELYl59op/Eloz0i/Xb5qbq9btPvGEllK2IifyJCUkQyTsBoJ04O9ZrwwLISUvMQ6SCqSnJcuRYSvYf4PjoHYlSy2BjN2far29lwlqbgwgJDtASjakulNqMJvOUoCF1KUtTJc/ga1nQ==', NULL, NULL, NULL, 'active', '2026-07-06 11:21:47', '2025-06-03', '[{\"background\":\"Addis Ababa University Fourth Year\",\"file\":\"educational_file_6a4b8fcb0d69f.jpg\"}]', 'MULTIPLE', 0, '[]', 'application_letter_file_6a4ba0533dea2.jpg', 'id_card_file_6a4b8fcb0db33.jpg', NULL, '0912732747', 'guarantee_letter_file_6a4b8fcb0dc13.jpg', 'Full-Time', NULL, NULL, '[{\"bank_name\":\"CBE\",\"account_number\":\"1000338601542\"}]', NULL, 1000.00, 2500.00, 0.00),
(19, 'EMP-24', NULL, 'Befikadu Nigussie', 'Driver', 'Driver', '0912345161', '0', NULL, NULL, NULL, 'active', '2026-07-06 13:19:11', '2026-07-02', '[{\"background\":\"LICENSE\",\"file\":\"educational_file_6a4bab448be2c.jpg\"}]', 'MULTIPLE', 0, '[]', 'application_letter_file_6a4bab448c039.jpg', 'id_card_file_6a4bab448c191.jpg', NULL, '0911394905', 'guarantee_letter_file_6a4bab448c24b.jpg', 'Full-Time', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00),
(20, 'EMP-0025', NULL, 'Meron Engedashet', 'Site Operations', 'store_keeper', '0913006116', '0', NULL, NULL, NULL, 'active', '2026-07-15 09:29:08', '2026-07-15', '[{\"background\":\"Arada Sub City TVET Certificate\",\"file\":\"educational_file_6a5752e4ccd22.jpg\"}]', 'MULTIPLE', 0, '[]', 'application_letter_file_6a5752e4cd1bd.jpg', 'id_card_file_6a5752e4cd4d7.jpg', NULL, '0912280433', 'guarantee_letter_file_6a5752e4cd6d2.jpg', 'Full-Time', NULL, NULL, '[{\"bank_name\":\"CBE\",\"account_number\":\"1000095027001\"}]', NULL, 875.00, 700.00, 0.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
