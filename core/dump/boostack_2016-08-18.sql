# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.11)
# Database: boostack
# Generation Time: 2016-08-18 13:12:27 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table boostack_http_session
# ------------------------------------------------------------

DROP TABLE IF EXISTS `boostack_http_session`;

CREATE TABLE `boostack_http_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ascii_session_id` varchar(32) NOT NULL,
  `logged_in` varchar(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_impression` int(11) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL DEFAULT '0',
  `user_agent` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `http_session_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table boostack_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `boostack_log`;

CREATE TABLE `boostack_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` enum('error','failure','information','success','warning','user','cronjob') DEFAULT NULL,
  `datetime` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `useragent` varchar(255) NOT NULL,
  `referrer` varchar(255) NOT NULL,
  `query` varchar(255) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table boostack_session_variable
# ------------------------------------------------------------

DROP TABLE IF EXISTS `boostack_session_variable`;

CREATE TABLE `boostack_session_variable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `variable_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `variable_value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  CONSTRAINT `session_variable_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `boostack_http_session` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table boostack_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `boostack_user`;

CREATE TABLE `boostack_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active` varchar(1) NOT NULL,
  `privilege` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `username` text,
  `pwd` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL,
  `pic_square` varchar(255) NOT NULL,
  `last_access` int(11) NOT NULL DEFAULT '0',
  `session_cookie` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `boostack_user` WRITE;
/*!40000 ALTER TABLE `boostack_user` DISABLE KEYS */;

INSERT INTO `boostack_user` (`id`, `active`, `privilege`, `name`, `username`, `pwd`, `email`, `pic_square`, `last_access`, `session_cookie`)
VALUES
	(0,'0',3,'','boostack','fbd5ee51bd4f9f23201396c9d9d58117d20fdb82c63f9ca8574b67461a1110ad03e3a0a1d9e000371ceb9211fb5676e1688ea060c47f31573465615e73039ab2','@','',522720000,''),
	(1,'1',3,'','testing@testing.com','521b9ccefbcd14d179e7a1bb877752870a6d620938b28a66a107eac6e6805b9d0989f45b5730508041aa5e710847d439ea74cd312c9355f1f2dae08d40e41d50','testing@testing.com','',1447868924,'');

/*!40000 ALTER TABLE `boostack_user` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table boostack_user_info
# ------------------------------------------------------------

DROP TABLE IF EXISTS `boostack_user_info`;

CREATE TABLE `boostack_user_info` (
  `id` int(11) NOT NULL,
  `first_name` varchar(70) NOT NULL,
  `last_name` varchar(70) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `locale` varchar(255) DEFAULT NULL,
  `city` varchar(200) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `about_me` text,
  `tel` varchar(20) DEFAULT NULL,
  `cell` varchar(20) DEFAULT NULL,
  `profession` varchar(25) DEFAULT NULL,
  `birthday` varchar(30) DEFAULT NULL,
  `movies` varchar(300) DEFAULT NULL,
  `music` varchar(300) DEFAULT NULL,
  `political` varchar(300) DEFAULT NULL,
  `interests` varchar(300) DEFAULT NULL,
  `tv` varchar(300) DEFAULT NULL,
  `religion` varchar(300) DEFAULT NULL,
  `pic_big` varchar(255) DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `user_info_ibfk_1` FOREIGN KEY (`id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table boostack_user_registration
# ------------------------------------------------------------

DROP TABLE IF EXISTS `boostack_user_registration`;

CREATE TABLE `boostack_user_registration` (
  `id` int(11) NOT NULL,
  `activation_date` int(11) NOT NULL DEFAULT '0',
  `access_code` varchar(10) DEFAULT NULL,
  `ip` varchar(16) NOT NULL,
  `join_date` int(11) NOT NULL,
  `join_idconfirm` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `user_registration_ibfk_1` FOREIGN KEY (`id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table boostack_user_social
# ------------------------------------------------------------

DROP TABLE IF EXISTS `boostack_user_social`;

CREATE TABLE `boostack_user_social` (
  `id` int(11) NOT NULL,
  `type` varchar(2) NOT NULL,
  `uid` varchar(90) NOT NULL,
  `uid_token` varchar(90) NOT NULL,
  `uid_token_secret` varchar(90) NOT NULL,
  `autosharing` varchar(1) NOT NULL DEFAULT '1',
  `website` varchar(255) NOT NULL,
  `extra` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`,`type`),
  KEY `id` (`id`),
  CONSTRAINT `user_social_ibfk_1` FOREIGN KEY (`id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
