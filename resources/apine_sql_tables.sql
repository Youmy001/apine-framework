-- phpMyAdmin SQL Dump
-- version 4.2.12deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 04, 2015 at 05:52 PM
-- Server version: 5.5.43-0+deb8u1
-- PHP Version: 5.6.9-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Table structure for table `apine_images`
--

CREATE TABLE IF NOT EXISTS `apine_images` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `access_id` varchar(60) NOT NULL,
  `file` varchar(254) NOT NULL,
  `folder` varchar(20) DEFAULT NULL,
  `privacy` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `apine_images`
 ADD KEY `user_id` (`user_id`);

-- --------------------------------------------------------

--
-- Table structure for table `apine_users`
--

CREATE TABLE IF NOT EXISTS `apine_users` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `username` varchar(25) NOT NULL,
  `password` varchar(64) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '65',
  `email` varchar(100) NOT NULL,
  `register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `apine_users`
 ADD UNIQUE KEY `username` (`username`,`email`);

-- --------------------------------------------------------

--
-- Table structure for table `apine_user_groups`
--

CREATE TABLE IF NOT EXISTS `apine_user_groups` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(254) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `apine_user_groups`
--

INSERT INTO `apine_user_groups` (`id`, `name`) VALUES
(1, 'Normal'),
(2, 'Administrateur');

-- --------------------------------------------------------

--
-- Table structure for table `apine_users_user_groups`
--

CREATE TABLE IF NOT EXISTS `apine_users_user_groups` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `apine_users_user_groups`
 ADD PRIMARY KEY (`user_id`,`group_id`);

ALTER TABLE `apine_users_user_groups`
 ADD UNIQUE KEY `apine_users_user_groups_index` (`user_id`,`group_id`), ADD KEY `apine_users_user_groups_user_is` (`user_id`), ADD KEY `apine_users_user_groups_group_id` (`group_id`);

ALTER TABLE `apine_users_user_groups`
ADD CONSTRAINT `apine_users_user_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `apine_user_groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `apine_users_user_groups_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `apine_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
