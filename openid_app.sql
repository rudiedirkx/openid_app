-- phpMyAdmin SQL Dump
-- version 3.4.3.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 17, 2011 at 10:48 AM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `openid_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_login` int(10) unsigned NOT NULL,
  `last_login` int(10) unsigned NOT NULL,
  `last_access` int(10) unsigned NOT NULL,
  `unicheck` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_login`, `last_login`, `last_access`, `unicheck`) VALUES
(2, 1308909699, 1308910509, 1308910520, '474754792'),
(3, 1308919941, 1308921372, 1308921739, '816054365'),
(4, 1308921785, 1308921904, 1308921942, '14877826'),
(5, 1308921790, 1308922053, 1308922054, '144622865'),
(6, 1308921922, 1316221758, 1316221864, '912630383'),
(7, 1309472749, 1316220301, 1316220303, '713987279');

-- --------------------------------------------------------

--
-- Table structure for table `user_facebook_connects`
--

CREATE TABLE IF NOT EXISTS `user_facebook_connects` (
  `user_id` int(10) unsigned NOT NULL,
  `facebook_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`facebook_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_facebook_connects`
--

INSERT INTO `user_facebook_connects` (`user_id`, `facebook_id`) VALUES
(3, 100001496091573);

-- --------------------------------------------------------

--
-- Table structure for table `user_openids`
--

CREATE TABLE IF NOT EXISTS `user_openids` (
  `user_id` int(10) unsigned NOT NULL,
  `identity` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`,`identity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_openids`
--

INSERT INTO `user_openids` (`user_id`, `identity`) VALUES
(2, 'https://www.google.com/accounts/o8/id?id=AItOawkEtgPOWX_59vd6w09AFvqtM6lEcDRIN7k'),
(4, 'https://www.google.com/accounts/o8/id?id=AItOawl6M_3t595xnn0HUko221ZsCIhHekC9CUU'),
(5, 'https://www.google.com/accounts/o8/id?id=AItOawm5aTZUkdrYIzGmNL7V-4okbZ4zAgaLh-4'),
(6, 'http://rudiedirkx.myopenid.com/'),
(7, 'https://www.google.com/accounts/o8/id?id=AItOawkJ6DNKCc40PBwpglYKOJn-Ccibs8c5Y3U');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
