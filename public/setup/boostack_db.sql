-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mariadb
-- Creato il: Apr 10, 2024 alle 15:33
-- Versione del server: 11.3.2-MariaDB-1:11.3.2+maria~ubu2204
-- Versione PHP: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `boostack_db`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `boostack_api_request`
--

CREATE TABLE `boostack_api_request` (
  `id` int(10) UNSIGNED NOT NULL,
  `method` varchar(255) DEFAULT NULL,
  `endpoint` varchar(255) DEFAULT '',
  `verb` varchar(255) DEFAULT '',
  `get_args` text DEFAULT NULL,
  `post_args` text DEFAULT NULL,
  `remote_address` varchar(255) DEFAULT '',
  `remote_user_agent` varchar(255) DEFAULT '',
  `error` tinyint(3) UNSIGNED DEFAULT NULL,
  `code` smallint(6) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `output` mediumtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_update` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_access` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_args` longblob DEFAULT NULL,
  `client_code` varchar(225) DEFAULT NULL,
  `app_code` varchar(225) DEFAULT NULL,
  `user_code` varchar(225) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `boostack_cache`
--

CREATE TABLE `boostack_cache` (
  `key` varchar(255) NOT NULL DEFAULT '',
  `value` longtext DEFAULT NULL,
  `created_at` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `boostack_http_session`
--

CREATE TABLE `boostack_http_session` (
  `id` int(11) NOT NULL,
  `ascii_session_id` varchar(32) NOT NULL,
  `logged_in` varchar(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_impression` int(11) NOT NULL DEFAULT 0,
  `created` int(11) NOT NULL DEFAULT 0,
  `user_agent` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `boostack_log`
--

CREATE TABLE `boostack_log` (
  `id` int(11) NOT NULL,
  `level` enum('error','failure','information','success','warning','user','cronjob') DEFAULT NULL,
  `datetime` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `useragent` varchar(255) NOT NULL,
  `referrer` varchar(255) NOT NULL,
  `query` varchar(255) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `boostack_session_variable`
--

CREATE TABLE `boostack_session_variable` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `variable_name` varchar(64) NOT NULL,
  `variable_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `boostack_user`
--

CREATE TABLE `boostack_user` (
  `id` int(11) NOT NULL,
  `active` varchar(1) NOT NULL,
  `privilege` int(11) DEFAULT NULL,
  `name` varchar(120) NOT NULL DEFAULT '',
  `username` varchar(255) DEFAULT '',
  `pwd` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL,
  `pic_square` varchar(255) NOT NULL,
  `last_access` int(11) NOT NULL DEFAULT 0,
  `session_cookie` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `boostack_user_api`
--

CREATE TABLE `boostack_user_api` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `token` text NOT NULL,
  `issuer_url` varchar(255) NOT NULL,
  `audience_url` varchar(255) NOT NULL,
  `issued_time` int(11) NOT NULL,
  `not_before_time` int(11) NOT NULL,
  `expired_time` int(11) NOT NULL,
  `expired_timestamp` timestamp NOT NULL,
  `revoked_time` timestamp NULL DEFAULT NULL,
  `revoked_from` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_update` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_access` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `boostack_user_info`
--

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
  `about_me` text DEFAULT NULL,
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
  `sex` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `boostack_user_privilege`
--

CREATE TABLE `boostack_user_privilege` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dump dei dati per la tabella `boostack_user_privilege`
--

INSERT INTO `boostack_user_privilege` (`id`, `title`, `description`) VALUES
(0, 'SYSTEM', 'only \"boostack\" user'),
(1, 'SUPERADMIN', ''),
(2, 'ADMIN', ''),
(3, 'USER', '');

-- --------------------------------------------------------

--
-- Struttura della tabella `boostack_user_registration`
--

CREATE TABLE `boostack_user_registration` (
  `id` int(11) NOT NULL,
  `activation_date` int(11) NOT NULL DEFAULT 0,
  `access_code` varchar(10) DEFAULT NULL,
  `ip` varchar(16) NOT NULL,
  `join_date` int(11) NOT NULL,
  `join_idconfirm` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `boostack_user_social`
--

CREATE TABLE `boostack_user_social` (
  `id` int(11) NOT NULL,
  `type` varchar(2) NOT NULL,
  `uid` varchar(90) NOT NULL,
  `uid_token` varchar(90) NOT NULL,
  `uid_token_secret` varchar(90) NOT NULL,
  `autosharing` varchar(1) NOT NULL DEFAULT '1',
  `website` varchar(255) NOT NULL,
  `extra` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `boostack_api_request`
--
ALTER TABLE `boostack_api_request`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `boostack_cache`
--
ALTER TABLE `boostack_cache`
  ADD PRIMARY KEY (`key`);

--
-- Indici per le tabelle `boostack_http_session`
--
ALTER TABLE `boostack_http_session`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indici per le tabelle `boostack_log`
--
ALTER TABLE `boostack_log`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `boostack_session_variable`
--
ALTER TABLE `boostack_session_variable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indici per le tabelle `boostack_user`
--
ALTER TABLE `boostack_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Email` (`email`),
  ADD UNIQUE KEY `Username` (`username`),
  ADD KEY `privilege2` (`privilege`);

--
-- Indici per le tabelle `boostack_user_api`
--
ALTER TABLE `boostack_user_api`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indici per le tabelle `boostack_user_info`
--
ALTER TABLE `boostack_user_info`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `boostack_user_privilege`
--
ALTER TABLE `boostack_user_privilege`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `boostack_user_registration`
--
ALTER TABLE `boostack_user_registration`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `boostack_user_social`
--
ALTER TABLE `boostack_user_social`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `boostack_api_request`
--
ALTER TABLE `boostack_api_request`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `boostack_http_session`
--
ALTER TABLE `boostack_http_session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `boostack_log`
--
ALTER TABLE `boostack_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `boostack_session_variable`
--
ALTER TABLE `boostack_session_variable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `boostack_user`
--
ALTER TABLE `boostack_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `boostack_user_api`
--
ALTER TABLE `boostack_user_api`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `boostack_user_privilege`
--
ALTER TABLE `boostack_user_privilege`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `boostack_http_session`
--
ALTER TABLE `boostack_http_session`
  ADD CONSTRAINT `http_session_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `boostack_session_variable`
--
ALTER TABLE `boostack_session_variable`
  ADD CONSTRAINT `session_variable_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `boostack_http_session` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `boostack_user`
--
ALTER TABLE `boostack_user`
  ADD CONSTRAINT `boostack_user_ibfk_1` FOREIGN KEY (`privilege`) REFERENCES `boostack_user_privilege` (`id`);

--
-- Limiti per la tabella `boostack_user_api`
--
ALTER TABLE `boostack_user_api`
  ADD CONSTRAINT `boostack_user_api_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `boostack_user_info`
--
ALTER TABLE `boostack_user_info`
  ADD CONSTRAINT `user_info_ibfk_1` FOREIGN KEY (`id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `boostack_user_registration`
--
ALTER TABLE `boostack_user_registration`
  ADD CONSTRAINT `user_registration_ibfk_1` FOREIGN KEY (`id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `boostack_user_social`
--
ALTER TABLE `boostack_user_social`
  ADD CONSTRAINT `user_social_ibfk_1` FOREIGN KEY (`id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
