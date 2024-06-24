-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mariadb
-- Creato il: Giu 23, 2024 alle 23:09
-- Versione del server: 11.3.2-MariaDB-1:11.3.2+maria~ubu2204
-- Versione PHP: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `boostack_db`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `boostack_message_queue`
--

CREATE TABLE `boostack_message_queue` (
  `id` int(11) NOT NULL,
  `queue_name` varchar(255) NOT NULL,
  `callable` text NOT NULL,
  `params` text NOT NULL,
  `executed_at` timestamp NULL DEFAULT NULL,
  `retries` int(11) DEFAULT 0,
  `max_retries` int(11) DEFAULT 3,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_access` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `boostack_message_queue`
--
ALTER TABLE `boostack_message_queue`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `boostack_message_queue`
--
ALTER TABLE `boostack_message_queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
