-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 03, 2016 at 07:18 PM
-- Server version: 5.5.47-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `a8559206_chatDB`
--

-- --------------------------------------------------------

--
-- Table structure for table `Meetings`
--

CREATE TABLE IF NOT EXISTS `Meetings` (
  `Id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `FirstPerson` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `SecondPerson` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `DateTime` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `PreviousDateTime` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Creator` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `State` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'current meeting  is active or has been canceled',
  `CheckedF` tinyint(1) NOT NULL DEFAULT '0',
  `CheckedS` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `Id` (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE IF NOT EXISTS `Users` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `oftherecord` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `UserName` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `UserAvatar` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `UserEmail` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `UserPass` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Contacts` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `OnlineFriends` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `isMsgWith` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `FriendsYWantTtalkTo` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `MeetingAragment` longtext CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=32 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;