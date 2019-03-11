-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2019 at 03:07 PM
-- Server version: 10.1.32-MariaDB
-- PHP Version: 7.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `myruble`
--

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

CREATE TABLE `game` (
  `time` int(11) NOT NULL,
  `task` int(11) NOT NULL,
  `price` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `game`
--

INSERT INTO `game` (`time`, `task`, `price`) VALUES
(300, 20, 0.25);

-- --------------------------------------------------------

--
-- Table structure for table `game_logs`
--

CREATE TABLE `game_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `task_success` int(11) NOT NULL,
  `task_fail` int(11) NOT NULL,
  `earn` float NOT NULL,
  `status` int(11) NOT NULL COMMENT '0 = lose, 1 = win',
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `game_logs`
--

INSERT INTO `game_logs` (`id`, `user_id`, `task_success`, `task_fail`, `earn`, `status`, `time`) VALUES
(1, 3, 0, 5, 0, 0, 1551775359),
(2, 3, 0, 5, 0, 0, 1551775359),
(3, 3, 0, 0, 0, 0, 1551775588),
(4, 3, 0, 0, 0, 0, 1551775614),
(5, 3, 0, 0, 0, 0, 1551775933),
(6, 3, 0, 0, 0, 0, 1551777311),
(7, 3, 1, 4, 0, 0, 1551777366),
(8, 3, 0, 1, 0, 0, 1551777430),
(9, 3, 1, 0, 0, 0, 1551777441),
(10, 3, 1, 1, 0, 0, 1551777550),
(11, 3, 0, 0, 0, 0, 1551782099),
(12, 3, 0, 0, 0, 0, 1551782269),
(13, 3, 0, 0, 0, 0, 1551782313),
(14, 3, 0, 0, 0, 0, 1551782390),
(15, 3, 0, 0, 0, 0, 1551782413),
(16, 3, 0, 0, 0, 0, 1551782465),
(17, 3, 0, 0, 0, 0, 1551782534),
(18, 3, 0, 0, 0, 0, 1551782615),
(19, 3, 0, 0, 0, 0, 1551782645),
(20, 1, 0, 0, 0, 0, 1551784147),
(21, 1, 0, 0, 0, 0, 1551785759),
(22, 1, 0, 0, 0, 0, 1551785946),
(23, 1, 0, 0, 0, 0, 1551786067),
(24, 1, 0, 0, 0, 0, 1551786091),
(25, 1, 0, 0, 0, 0, 1551786211),
(26, 1, 0, 0, 0, 0, 1551786222),
(27, 1, 0, 0, 0, 0, 1551786235),
(28, 1, 0, 0, 0, 0, 1551786493),
(29, 1, 0, 0, 0, 0, 1551786500),
(30, 1, 0, 0, 0, 0, 1551786879),
(31, 1, 0, 0, 0, 0, 1551786877),
(32, 1, 0, 0, 0, 0, 1551786907),
(33, 1, 0, 0, 0, 0, 1551786951),
(34, 1, 0, 0, 0, 0, 1551787034),
(35, 1, 0, 0, 0, 0, 1551787065),
(36, 1, 1, 0, 0, 0, 1551787154),
(37, 1, 0, 0, 0, 0, 1551787193),
(38, 1, 0, 0, 0, 0, 1551787364),
(39, 1, 0, 0, 0, 0, 1551788358),
(40, 1, 0, 0, 0, 0, 1551790235),
(41, 1, 0, 0, 0, 0, 1551790300),
(42, 1, 0, 0, 0, 0, 1551790331),
(43, 1, 0, 0, 0, 0, 1551790466),
(44, 1, 0, 0, 0, 0, 1551878963),
(45, 1, 0, 0, 0, 0, 1551879254),
(46, 1, 0, 0, 0, 0, 1551879265),
(47, 1, 1, 0, 0, 0, 1551879322),
(48, 1, 1, 0, 0, 0, 1551879399),
(49, 1, 0, 0, 0, 0, 1551879669),
(50, 1, 0, 0, 0, 0, 1551880723),
(51, 1, 0, 0, 0, 0, 1551880748),
(52, 1, 0, 0, 0, 0, 1551880802),
(53, 1, 0, 0, 0, 0, 1551880849),
(54, 1, 1, 0, 0, 0, 1551880945),
(55, 1, 0, 0, 0, 0, 1551881288),
(56, 1, 0, 0, 0, 0, 1551881345),
(57, 1, 1, 0, 0, 0, 1551881382),
(58, 1, 0, 0, 0, 0, 1551894635),
(59, 1, 0, 0, 0, 0, 1551948893),
(60, 1, 0, 0, 0, 0, 1551949132),
(61, 1, 0, 0, 0, 0, 1551949583),
(62, 1, 0, 0, 0, 0, 1551949997),
(63, 1, 0, 0, 0, 0, 1551950277),
(64, 1, 0, 0, 0, 0, 1551950738),
(65, 1, 0, 0, 0, 0, 1551950931),
(66, 1, 0, 0, 0, 0, 1551952002),
(67, 1, 1, 0, 0, 0, 1551952138),
(68, 1, 0, 0, 0, 0, 1551952368),
(69, 1, 0, 0, 0, 0, 1551952575),
(70, 1, 0, 0, 0, 0, 1551952966),
(71, 1, 0, 0, 0, 0, 1551953038),
(72, 1, 0, 0, 0, 0, 1551953192),
(73, 1, 0, 0, 0, 0, 1551953428),
(74, 1, 0, 0, 0, 0, 1551953511),
(75, 1, 0, 0, 0, 0, 1551953634),
(76, 1, 0, 0, 0, 0, 1551953709),
(77, 1, 0, 0, 0, 0, 1551953717),
(78, 1, 0, 0, 0, 0, 1551954118),
(79, 1, 0, 0, 0, 0, 1551954187),
(80, 1, 0, 0, 0, 0, 1551954256),
(81, 1, 1, 0, 0, 0, 1551954668),
(82, 1, 0, 0, 0, 0, 1551954790),
(83, 1, 0, 0, 0, 0, 1551955034),
(84, 1, 0, 0, 0, 0, 1551955193),
(85, 1, 0, 0, 0, 0, 1551955302),
(86, 1, 0, 0, 0, 0, 1551955485),
(87, 1, 0, 0, 0, 0, 1551955635),
(88, 1, 0, 0, 0, 0, 1551955940),
(89, 1, 0, 0, 0, 0, 1551956007),
(90, 1, 0, 0, 0, 0, 1551956266),
(91, 1, 0, 0, 0, 0, 1551956336),
(92, 1, 0, 0, 0, 0, 1551956418),
(93, 1, 0, 0, 0, 0, 1551956531),
(94, 1, 1, 0, 0, 0, 1551956770),
(95, 1, 0, 0, 0, 0, 1551956835),
(96, 1, 0, 0, 0, 0, 1551956901),
(97, 1, 0, 0, 0, 0, 1551957286),
(98, 1, 0, 0, 0, 0, 1551957381),
(99, 1, 0, 0, 0, 0, 1551957512),
(100, 1, 0, 0, 0, 0, 1551957583),
(101, 1, 0, 0, 0, 0, 1551957882),
(102, 1, 0, 0, 0, 0, 1551958063),
(103, 1, 0, 0, 0, 0, 1551958939),
(104, 1, 0, 0, 0, 0, 1551959064),
(105, 1, 0, 0, 0, 0, 1551959139),
(106, 1, 0, 0, 0, 0, 1551959351),
(107, 1, 0, 0, 0, 0, 1551959477),
(108, 1, 0, 0, 0, 0, 1551959575),
(109, 1, 0, 0, 0, 0, 1551959640),
(110, 1, 0, 0, 0, 0, 1551959695),
(111, 1, 0, 0, 0, 0, 1551959742),
(112, 1, 0, 0, 0, 0, 1551960053),
(113, 1, 0, 0, 0, 0, 1551960213),
(114, 1, 0, 0, 0, 0, 1551960217),
(115, 1, 0, 0, 0, 0, 1551960289),
(116, 1, 0, 0, 0, 0, 1551960295),
(117, 1, 0, 0, 0, 0, 1551960366),
(118, 1, 0, 0, 0, 0, 1551960453),
(119, 1, 0, 0, 0, 0, 1551960556),
(120, 1, 0, 0, 0, 0, 1551960672),
(121, 1, 0, 0, 0, 0, 1551960771),
(122, 1, 0, 0, 0, 0, 1551960837),
(123, 1, 0, 0, 0, 0, 1551960861),
(124, 1, 0, 4, 0, 0, 1551961642),
(125, 1, 0, 0, 0, 0, 1551961744),
(126, 1, 0, 0, 0, 0, 1552042359),
(127, 1, 0, 0, 0, 0, 1552043330),
(128, 1, 0, 0, 0, 0, 1552043879),
(129, 1, 0, 0, 0, 0, 1552310778),
(130, 1, 0, 0, 0, 0, 1552312975);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `surname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pass` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `balance` float NOT NULL,
  `heart` int(11) NOT NULL,
  `start_notify_heart` int(11) NOT NULL,
  `firebase_token` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `surname`, `username`, `email`, `pass`, `balance`, `heart`, `start_notify_heart`, `firebase_token`) VALUES
(1, 'Enver', 'Abbasov', 'enver435', 'abbasovenver1999@gmail.com', 'b6ffb8cb3fc96d5a259b36d103131d7d', 2.5, 0, 1552310780, 'cJnLmADAtFU:APA91bFWPzATUevbOXlYzMB0EtcpmNzG5nDDVJZauE_q3cKAAGaElDpA15H8aJpWINnawkI5q1rKyTUIIFOHfibG-iGcx0yRR3bJJj8hjUH-PWBw1KGRroVI_pC1-1TI1WHNF3NMZLDA'),
(2, '', '', 'enver555', 'abbasov-enver@mail.ru', 'b6ffb8cb3fc96d5a259b36d103131d7d', 0, 0, 0, ''),
(3, '', '', 'blackrast', 'babayevmanaf1995@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 0, 3, 0, 'eigai1-BOyM:APA91bHv6hNcumWnQBMApKQHfqfM-VehhHMELvF5R8vqSCE_TF7Y-ThnoS-tOA7CDt9N9NpQC1GGSLn8b8WNZE5LQP3-vx1_sCgRLGKT_9M4ujj263qTkzVd66OkdpcCFJxHS2tWorwh'),
(4, '', '', 'testuser', 'test@mail.com', 'e10adc3949ba59abbe56e057f20f883e', 0, 3, 0, ''),
(5, '', '', 'ttt435', 'gggg@gmail.com', 'b6ffb8cb3fc96d5a259b36d103131d7d', 0, 3, 0, 'eigai1-BOyM:APA91bHv6hNcumWnQBMApKQHfqfM-VehhHMELvF5R8vqSCE_TF7Y-ThnoS-tOA7CDt9N9NpQC1GGSLn8b8WNZE5LQP3-vx1_sCgRLGKT_9M4ujj263qTkzVd66OkdpcCFJxHS2tWorwh');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `game_logs`
--
ALTER TABLE `game_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `game_logs`
--
ALTER TABLE `game_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
