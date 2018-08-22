-- Adminer 4.3.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `cms_login`;
CREATE TABLE `cms_login` (
  `login_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `success` enum('N','Y') NOT NULL DEFAULT 'N',
  `ip` varchar(15) DEFAULT NULL,
  `inserted` datetime DEFAULT NULL,
  PRIMARY KEY (`login_id`),
  KEY `user_id` (`user_id`),
  KEY `success` (`success`),
  KEY `inserted` (`inserted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `cms_login` (`login_id`, `user_id`, `username`, `success`, `ip`, `inserted`) VALUES
(1,	1,	'masters',	'Y',	'::1',	'2018-08-22 20:46:53');

DROP TABLE IF EXISTS `cms_user`;
CREATE TABLE `cms_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fname` varchar(100) DEFAULT NULL,
  `lname` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `passwd` varchar(128) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `forgot` varchar(32) DEFAULT NULL,
  `forgot_timestamp` datetime DEFAULT NULL,
  `admin` enum('N','Y') NOT NULL DEFAULT 'N',
  `status` enum('N','Y','W') NOT NULL DEFAULT 'N',
  `inserted` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `admin` (`admin`),
  KEY `status` (`status`),
  KEY `inserted` (`inserted`),
  KEY `email` (`email`),
  KEY `forgot` (`forgot`),
  KEY `forgot_timestamp` (`forgot_timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `cms_user` (`user_id`, `fname`, `lname`, `username`, `passwd`, `email`, `phone`, `forgot`, `forgot_timestamp`, `admin`, `status`, `inserted`) VALUES
(1,	'Lukáš',	'Dohnal',	'lukas',	'7726c119952b18e8bd0612867267eef2f42b27982882862b50a03febd2af0245b216a08c517cb3adf8e3bd2c2a74fa84f2d06f82ec2c6d5c712d2d7a43a0211c',	'lukas.dohnal@victoriatech.cz',	'+420605102405',	NULL,	NULL,	'Y',	'Y',	'2018-08-22 18:09:57'),
(2,	'Martin',	'Krajňák',	'martin',	'ffb372d2aed93463fd162d5fb3a9f836e8c8e1d7737abe601864bce26d202ccd5813e556aa8311f624a95c6189428cb7fcc35b40a19257f6ac1600b8d4423ec8',	'martin.krajnak@victoriatech.cz',	'+420777735194',	NULL,	NULL,	'Y',	'Y',	'2018-08-22 21:05:51');

-- 2018-08-22 21:18:36
