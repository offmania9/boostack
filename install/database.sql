-- phpMyAdmin SQL Dump
-- version 2.11.11.3
-- http://www.phpmyadmin.net

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `boostack`
--
-- --------------------------------------------------------
--
-- Table `http_session`
--

CREATE TABLE `http_session` (
  `id` int(11) NOT NULL auto_increment,
  `ascii_session_id` varchar(32) NOT NULL,
  `logged_in` varchar(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_impression` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `user_agent` varchar(256) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Data dump for `http_session`
--
-- --------------------------------------------------------
--
-- Table `log`
--

CREATE TABLE `log` (
  `id` int(11) NOT NULL auto_increment,
  `datetime` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `useragent` varchar(255) NOT NULL,
  `referrer` varchar(255) NOT NULL,
  `query` varchar(255) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Data dump for `log`
--
-- --------------------------------------------------------
--
-- Table `session_variable`
--

CREATE TABLE `session_variable` (
  `id` int(11) NOT NULL auto_increment,
  `session_id` int(11) NOT NULL,
  `variable_name` varchar(64) collate utf8_unicode_ci NOT NULL,
  `variable_value` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Data dump for `session_variable`
--
-- --------------------------------------------------------
--
-- Table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL auto_increment,
  `active` varchar(1) NOT NULL,
  `privilege` int(11) NOT NULL,
  `username` text,
  `pwd` varchar(128) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pic_square` varchar(255) NOT NULL,
  `last_access` int(11) NOT NULL default '0',
  `session_cookie` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Data dump for `user`
--
INSERT INTO `user` VALUES(0, '0', 3, 'boostack', 'a8e2e732c47373c952834ae5d1e416fa9a699b7861c2189c887df400eaa12b20babba10f475debe5c2cb4a0c2a8d8edbeb953c4c47fff4689eb1ce32a2a20155', '@', '', 1381977584, '');
-- --------------------------------------------------------
--
-- Table `user_info`
--

CREATE TABLE `user_info` (
  `id` int(11) NOT NULL,
  `first_name` varchar(70) NOT NULL,
  `last_name` varchar(70) default NULL,
  `name` varchar(255) default NULL,
  `locale` varchar(255) default NULL,
  `city` varchar(200) default NULL,
  `state` varchar(100) default NULL,
  `country` varchar(100) default NULL,
  `zip` varchar(10) default NULL,
  `about_me` text,
  `tel` varchar(20) default NULL,
  `cell` varchar(20) default NULL,
  `profession` varchar(25) default NULL,
  `birthday` varchar(30) default NULL,
  `movies` varchar(300) default NULL,
  `music` varchar(300) default NULL,
  `political` varchar(300) default NULL,
  `interests` varchar(300) default NULL,
  `tv` varchar(300) default NULL,
  `religion` varchar(300) default NULL,
  `pic_big` varchar(255) default NULL,
  `sex` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Data dump for `user_info`
--
-- --------------------------------------------------------
--
-- Table `user_registration`
--

CREATE TABLE `user_registration` (
  `id` int(11) NOT NULL,
  `activation_date` int(11) NOT NULL default '0',
  `access_code` varchar(10) default NULL,
  `ip` varchar(16) NOT NULL,
  `join_date` int(11) NOT NULL,
  `join_idconfirm` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Data dump for `user_registration`
--
-- --------------------------------------------------------
--
-- Table `user_social`
--

CREATE TABLE `user_social` (
  `id` int(11) NOT NULL,
  `type` varchar(2) NOT NULL,
  `uid` varchar(90) NOT NULL,
  `uid_token` varchar(90) NOT NULL,
  `uid_token_secret` varchar(90) NOT NULL,
  `autosharing` varchar(1) NOT NULL default '1',
  `website` varchar(255) NOT NULL,
  `extra` varchar(10) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uid` (`uid`,`type`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Data dump for `user_social`
--
--
-- Constraints
--
--
-- Constraints`http_session`
--
ALTER TABLE `http_session`
  ADD CONSTRAINT `http_session_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints`session_variable`
--
ALTER TABLE `session_variable`
  ADD CONSTRAINT `session_variable_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `http_session` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints`user_info`
--
ALTER TABLE `user_info`
  ADD CONSTRAINT `user_info_ibfk_1` FOREIGN KEY (`id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints`user_registration`
--
ALTER TABLE `user_registration`
  ADD CONSTRAINT `user_registration_ibfk_1` FOREIGN KEY (`id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints`user_social`
--
ALTER TABLE `user_social`
  ADD CONSTRAINT `user_social_ibfk_1` FOREIGN KEY (`id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;