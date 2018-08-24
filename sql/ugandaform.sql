-- Adminer 4.3.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `cms_currency_balance`;
CREATE TABLE `cms_currency_balance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `date` date DEFAULT NULL,
  `particulars` tinyint(3) unsigned NOT NULL,
  `cf_name` tinyint(3) unsigned NOT NULL,
  `shop` tinyint(3) unsigned NOT NULL,
  `division` tinyint(3) unsigned NOT NULL,
  `payin` int(10) unsigned NOT NULL,
  `payout` int(10) unsigned NOT NULL,
  `note` text NOT NULL,
  `currency` enum('UGX','USD') DEFAULT NULL,
  `inserted` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `currency` (`currency`),
  KEY `inserted` (`inserted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cms_daily_data`;
CREATE TABLE `cms_daily_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `date` date DEFAULT NULL,
  `game` tinyint(3) unsigned NOT NULL,
  `shop` tinyint(3) unsigned NOT NULL,
  `division` tinyint(3) unsigned NOT NULL,
  `n_machines` tinyint(3) unsigned NOT NULL,
  `n_tickets` int(10) unsigned NOT NULL,
  `payin` int(10) unsigned NOT NULL,
  `payout` int(10) unsigned NOT NULL,
  `inserted` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `inserted` (`inserted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cms_form_log`;
CREATE TABLE `cms_form_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `agenda` tinyint(3) unsigned NOT NULL,
  `form_id` int(10) unsigned NOT NULL,
  `old_value` text CHARACTER SET utf8 NOT NULL,
  `new_value` text CHARACTER SET utf8 NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `inserted` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `agenda` (`agenda`),
  KEY `form_id` (`form_id`),
  KEY `user_id` (`user_id`),
  KEY `inserted` (`inserted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


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

-- 2018-08-24 03:04:30
