-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 15, 2011 at 06:29 PM
-- Server version: 5.1.41
-- PHP Version: 5.2.10-2ubuntu6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ultimate_rdf2db`
--

-- --------------------------------------------------------

--
-- Table structure for table `06Languages`
--

DROP TABLE IF EXISTS `06Languages`;
CREATE TABLE IF NOT EXISTS `06Languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uri` (`uri`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=156 ;

-- --------------------------------------------------------

--
-- Table structure for table `06Languages_tr`
--

DROP TABLE IF EXISTS `06Languages_tr`;
CREATE TABLE IF NOT EXISTS `06Languages_tr` (
  `id` int(11) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `l_language` varchar(2) NOT NULL,
  `06label` longtext NOT NULL,
  `06comment` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `12Subject`
--

DROP TABLE IF EXISTS `12Subject`;
CREATE TABLE IF NOT EXISTS `12Subject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(255) NOT NULL,
  `07login` longtext NOT NULL,
  `07password` longtext NOT NULL,
  `07userUILg` int(11) NOT NULL,
  `07userDefLg` int(11) NOT NULL,
  `07userMail` longtext NOT NULL,
  `07userFirstName` longtext NOT NULL,
  `07userLastName` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uri` (`uri`),
  KEY `7#userUILg` (`07userUILg`),
  KEY `7#userDefLg` (`07userDefLg`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=100001 ;

-- --------------------------------------------------------

--
-- Table structure for table `12Subject_tr`
--

DROP TABLE IF EXISTS `12Subject_tr`;
CREATE TABLE IF NOT EXISTS `12Subject_tr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(255) NOT NULL,
  `l_language` varchar(2) NOT NULL,
  `06label` longtext NOT NULL,
  `06comment` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=900001 ;

-- --------------------------------------------------------

--
-- Table structure for table `class_to_table`
--

DROP TABLE IF EXISTS `class_to_table`;
CREATE TABLE IF NOT EXISTS `class_to_table` (
  `id` int(11) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `table` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uri` (`uri`,`table`),
  KEY `table` (`table`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ressource_has_class`
--

DROP TABLE IF EXISTS `ressource_has_class`;
CREATE TABLE IF NOT EXISTS `ressource_has_class` (
  `ressource_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  PRIMARY KEY (`ressource_id`,`class_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ressource_to_table`
--

DROP TABLE IF EXISTS `ressource_to_table`;
CREATE TABLE IF NOT EXISTS `ressource_to_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(255) NOT NULL,
  `table` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
