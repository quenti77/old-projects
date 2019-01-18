-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 25, 2014 at 07:45 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `rattrapage`
--

-- --------------------------------------------------------

--
-- Table structure for table `plugins`
--

CREATE TABLE IF NOT EXISTS `plugins` (
  `p_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `p_name` varchar(250) NOT NULL,
  `p_description` text NOT NULL,
  `p_author` bigint(20) NOT NULL,
  `p_version` varchar(50) NOT NULL,
  `p_link` varchar(250) NOT NULL,
  PRIMARY KEY (`p_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `plugin_list`
--

CREATE TABLE IF NOT EXISTS `plugin_list` (
  `pl_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pl_userId` bigint(20) NOT NULL,
  `pl_pluginId` bigint(20) NOT NULL,
  `pl_version` varchar(50) NOT NULL,
  `pl_data` text NOT NULL,
  `pl_action` varchar(25) NOT NULL,
  PRIMARY KEY (`pl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `u_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `u_pseudo` varchar(30) NOT NULL,
  `u_passwd` varchar(100) NOT NULL,
  `u_mail` varchar(100) NOT NULL,
  `u_last` datetime NOT NULL,
  `u_check` varchar(250) NOT NULL,
  PRIMARY KEY (`u_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
