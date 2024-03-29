/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table boostack_api_request
# ------------------------------------------------------------

DROP TABLE IF EXISTS `boostack_api_request`;

CREATE TABLE `boostack_api_request` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `method` varchar(255) DEFAULT NULL,
  `endpoint` varchar(255) DEFAULT '',
  `verb` varchar(255) DEFAULT '',
  `get_args` text,
  `post_args` text,
  `remote_address` varchar(255) DEFAULT '',
  `remote_user_agent` varchar(255) DEFAULT '',
  `error` tinyint unsigned DEFAULT NULL,
  `code` smallint DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `output` text,
  `created_at` bigint DEFAULT NULL,
  `last_update` bigint DEFAULT NULL,
  `last_access` bigint DEFAULT NULL,
  `file_args` longblob,
  `created_datetime` datetime DEFAULT NULL,
  `client_code` varchar(225) DEFAULT NULL,
  `app_code` varchar(225) DEFAULT NULL,
  `user_code` varchar(225) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table boostack_cache
# ------------------------------------------------------------

DROP TABLE IF EXISTS `boostack_cache`;

CREATE TABLE `boostack_cache` (
  `key` varchar(255) NOT NULL DEFAULT '',
  `value` longtext,
  `created_at` bigint NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table boostack_http_session
# ------------------------------------------------------------

DROP TABLE IF EXISTS `boostack_http_session`;

CREATE TABLE `boostack_http_session` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ascii_session_id` varchar(32) NOT NULL,
  `logged_in` varchar(1) NOT NULL,
  `user_id` int NOT NULL,
  `last_impression` int NOT NULL DEFAULT '0',
  `created` int NOT NULL DEFAULT '0',
  `user_agent` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `http_session_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table boostack_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `boostack_log`;

CREATE TABLE `boostack_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `level` enum('error','failure','information','success','warning','user','cronjob') DEFAULT NULL,
  `datetime` int NOT NULL,
  `username` varchar(60) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `useragent` varchar(255) NOT NULL,
  `referrer` varchar(255) NOT NULL,
  `query` varchar(255) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table boostack_session_variable
# ------------------------------------------------------------

DROP TABLE IF EXISTS `boostack_session_variable`;

CREATE TABLE `boostack_session_variable` (
  `id` int NOT NULL AUTO_INCREMENT,
  `session_id` int NOT NULL,
  `variable_name` varchar(64) NOT NULL,
  `variable_value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  CONSTRAINT `session_variable_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `boostack_http_session` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Dump of table boostack_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `boostack_user`;

CREATE TABLE `boostack_user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `active` varchar(1) NOT NULL,
  `privilege` int DEFAULT NULL,
  `name` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `pwd` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL,
  `pic_square` varchar(255) NOT NULL,
  `last_access` int NOT NULL DEFAULT '0',
  `session_cookie` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Email` (`email`),
  UNIQUE KEY `Username` (`username`),
  KEY `privilege2` (`privilege`),
  CONSTRAINT `boostack_user_ibfk_1` FOREIGN KEY (`privilege`) REFERENCES `boostack_user_privilege` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Dump of table boostack_user_registration
# ------------------------------------------------------------

DROP TABLE IF EXISTS `boostack_user_registration`;

CREATE TABLE `boostack_user_registration` (
  `id` int NOT NULL,
  `activation_date` int NOT NULL DEFAULT '0',
  `access_code` varchar(10) DEFAULT NULL,
  `ip` varchar(16) NOT NULL,
  `join_date` int NOT NULL,
  `join_idconfirm` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `user_registration_ibfk_1` FOREIGN KEY (`id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Dump of table boostack_user_info
# ------------------------------------------------------------

DROP TABLE IF EXISTS `boostack_user_info`;

CREATE TABLE `boostack_user_info` (
  `id` int NOT NULL,
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
  `company` varchar(25) DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table boostack_user_privilege
# ------------------------------------------------------------

DROP TABLE IF EXISTS `boostack_user_privilege`;

CREATE TABLE `boostack_user_privilege` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `boostack_user_privilege` VALUES
(0,'SYSTEM','only \"boostack\" user'),
(1,'SUPERADMIN',''),
(2,'ADMIN',''),
(3,'USER','');


# Dump of table boostack_user_social
# ------------------------------------------------------------

DROP TABLE IF EXISTS `boostack_user_social`;

CREATE TABLE `boostack_user_social` (
  `id` int NOT NULL,
  `type` varchar(2) NOT NULL,
  `uid` varchar(90) NOT NULL,
  `uid_token` varchar(90) NOT NULL,
  `uid_token_secret` varchar(90) NOT NULL,
  `autosharing` varchar(1) NOT NULL DEFAULT '1',
  `website` varchar(255) NOT NULL,
  `extra` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  CONSTRAINT `user_social_ibfk_1` FOREIGN KEY (`id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
