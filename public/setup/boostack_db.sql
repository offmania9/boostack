SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET time_zone = "+00:00";

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
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb3 COLLATE = utf8mb3_general_ci;

CREATE TABLE `boostack_asset` (
    `id` int(11) NOT NULL,
    `object_name` varchar(50) DEFAULT NULL,
    `object_type` varchar(50) DEFAULT NULL,
    `temp_name` varchar(200) NOT NULL,
    `filename` varchar(200) NOT NULL,
    `filepath` varchar(255) NOT NULL,
    `type` varchar(255) NOT NULL,
    `size` float NOT NULL,
    `extension` varchar(10) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `last_access` timestamp NOT NULL DEFAULT current_timestamp(),
    `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE `boostack_cache` (
    `key` varchar(255) NOT NULL DEFAULT '',
    `key_plain` text NOT NULL,
    `value` longtext DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb3 COLLATE = utf8mb3_general_ci;

CREATE TABLE `boostack_event` (
    `id` int(11) NOT NULL,
    `id_user` int(11) NOT NULL,
    `name` varchar(255) NOT NULL,
    `description` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `last_access` timestamp NULL DEFAULT current_timestamp(),
    `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE `boostack_http_session` (
    `id` int(11) NOT NULL,
    `ascii_session_id` varchar(32) NOT NULL,
    `logged_in` varchar(1) NOT NULL,
    `user_id` int(11) NOT NULL,
    `last_impression` int(11) NOT NULL DEFAULT 0,
    `created` int(11) NOT NULL DEFAULT 0,
    `user_agent` varchar(256) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb3 COLLATE = utf8mb3_general_ci;

CREATE TABLE `boostack_log` (
    `id` int(11) NOT NULL,
    `level` enum(
        'error',
        'failure',
        'information',
        'success',
        'warning',
        'user',
        'cronjob'
    ) DEFAULT NULL,
    `datetime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `username` varchar(60) NOT NULL,
    `ip` varchar(16) NOT NULL,
    `useragent` varchar(255) NOT NULL,
    `referrer` varchar(255) NOT NULL,
    `query` text NOT NULL,
    `message` text NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb3 COLLATE = utf8mb3_general_ci;

CREATE TABLE `boostack_notification` (
    `id` int(11) NOT NULL,
    `id_event` int(11) NOT NULL,
    `id_user_from` int(11) NOT NULL,
    `type` enum('web', 'email', 'all') NOT NULL,
    `send_date` timestamp NULL DEFAULT current_timestamp(),
    `message_content` text DEFAULT NULL,
    `email_content` text DEFAULT NULL,
    `json_object` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `last_access` timestamp NULL DEFAULT current_timestamp(),
    `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE `boostack_notification_email` (
    `id` int(11) NOT NULL,
    `id_notification` int(11) NOT NULL,
    `id_user_to` int(11) NOT NULL,
    `email_to` varchar(255) NOT NULL,
    `status` enum('pending', 'sent', 'failed') NOT NULL DEFAULT 'pending',
    `json_object` text DEFAULT NULL,
    `email_content` text NOT NULL,
    `retries` tinyint(4) NOT NULL DEFAULT 0,
    `max_retries` tinyint(4) DEFAULT NULL,
    `sent_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `last_access` timestamp NULL DEFAULT current_timestamp(),
    `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE `boostack_notification_web` (
    `id` int(11) NOT NULL,
    `id_notification` int(11) NOT NULL,
    `id_user_to` int(11) NOT NULL,
    `status` enum('pending', 'read') NOT NULL DEFAULT 'pending',
    `json_object` text DEFAULT NULL,
    `message_content` text NOT NULL,
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `last_access` timestamp NULL DEFAULT current_timestamp(),
    `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE `boostack_session_variable` (
    `id` int(11) NOT NULL,
    `session_id` int(11) NOT NULL,
    `variable_name` varchar(64) NOT NULL,
    `variable_value` text NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb3 COLLATE = utf8mb3_general_ci;

CREATE TABLE `boostack_user` (
    `id` int(11) NOT NULL,
    `active` varchar(1) NOT NULL,
    `privilege` int(11) DEFAULT NULL,
    `name` varchar(120) NOT NULL DEFAULT '',
    `username` varchar(255) DEFAULT '',
    `pwd` varchar(255) NOT NULL DEFAULT '',
    `email` varchar(255) NOT NULL,
    `pic_square` varchar(255) DEFAULT NULL,
    `last_access` int(11) NOT NULL DEFAULT 0,
    `session_cookie` varchar(64) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb3 COLLATE = utf8mb3_general_ci;

CREATE TABLE `boostack_user_api` (
    `id` int(11) NOT NULL,
    `id_user` int(11) NOT NULL,
    `token` text NOT NULL,
    `issuer_url` varchar(255) NOT NULL,
    `audience_url` varchar(255) NOT NULL,
    `issued_time` int(11) NOT NULL,
    `not_before_time` int(11) NOT NULL,
    `expired_time` int(11) NOT NULL,
    `expired_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `revoked_time` timestamp NULL DEFAULT NULL,
    `revoked_from` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `last_update` timestamp NOT NULL DEFAULT current_timestamp(),
    `last_access` timestamp NOT NULL DEFAULT current_timestamp(),
    `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE `boostack_user_info` (
    `id` int(11) NOT NULL,
    `first_name` varchar(70) NOT NULL,
    `last_name` varchar(70) DEFAULT NULL,
    `address` varchar(255) DEFAULT NULL,
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
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb3 COLLATE = utf8mb3_general_ci;

CREATE TABLE `boostack_user_privilege` (
    `id` int(11) NOT NULL,
    `title` varchar(255) NOT NULL DEFAULT '',
    `description` text NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb3 COLLATE = utf8mb3_general_ci;

CREATE TABLE `boostack_user_registration` (
    `id` int(11) NOT NULL,
    `activation_date` int(11) NOT NULL DEFAULT 0,
    `access_code` varchar(10) DEFAULT NULL,
    `ip` varchar(16) NOT NULL,
    `join_date` int(11) NOT NULL,
    `join_idconfirm` varchar(32) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb3 COLLATE = utf8mb3_general_ci;

CREATE TABLE `boostack_user_social` (
    `id` int(11) NOT NULL,
    `type` varchar(2) NOT NULL,
    `uid` varchar(90) NOT NULL,
    `uid_token` text NOT NULL,
    `uid_token_secret` varchar(90) NOT NULL,
    `autosharing` varchar(1) NOT NULL DEFAULT '1',
    `website` varchar(255) NOT NULL,
    `extra` varchar(10) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb3 COLLATE = utf8mb3_general_ci;

CREATE TABLE `boostack_user_sso` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `active` varchar(1) NOT NULL DEFAULT '0',
    `provider` varchar(20) NOT NULL,
    `provider_user_id` varchar(255) NOT NULL,
    `name` varchar(220) NOT NULL,
    `email` varchar(255) NOT NULL,
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    `last_update` timestamp NULL DEFAULT current_timestamp(),
    `last_access` timestamp NULL DEFAULT current_timestamp(),
    `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci;

ALTER TABLE `boostack_api_request` ADD PRIMARY KEY (`id`);

ALTER TABLE `boostack_asset` ADD PRIMARY KEY (`id`);

ALTER TABLE `boostack_cache` ADD PRIMARY KEY (`key`);

ALTER TABLE `boostack_event`
ADD PRIMARY KEY (`id`),
ADD KEY `boostack_event_ibfk_1` (`id_user`);

ALTER TABLE `boostack_http_session`
ADD PRIMARY KEY (`id`),
ADD KEY `user_id` (`user_id`);

ALTER TABLE `boostack_log` ADD PRIMARY KEY (`id`);

ALTER TABLE `boostack_notification`
ADD PRIMARY KEY (`id`),
ADD KEY `boostack_notification_ibfk_1` (`id_event`),
ADD KEY `boostack_notification_ibfk_2` (`id_user_from`);

ALTER TABLE `boostack_notification_email`
ADD PRIMARY KEY (`id`),
ADD KEY `boostack_notification_email_ibfk_1` (`id_notification`),
ADD KEY `boostack_notification_email_ibfk_2` (`id_user_to`);

ALTER TABLE `boostack_notification_web`
ADD PRIMARY KEY (`id`),
ADD KEY `boostack_notification_web_ibfk_1` (`id_notification`),
ADD KEY `boostack_notification_web_ibfk_2` (`id_user_to`);

ALTER TABLE `boostack_session_variable`
ADD PRIMARY KEY (`id`),
ADD KEY `session_id` (`session_id`);

ALTER TABLE `boostack_user`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `Email` (`email`),
ADD UNIQUE KEY `Username` (`username`),
ADD KEY `privilege2` (`privilege`);

ALTER TABLE `boostack_user_api`
ADD PRIMARY KEY (`id`),
ADD KEY `id_user` (`id_user`);

ALTER TABLE `boostack_user_info` ADD PRIMARY KEY (`id`);

ALTER TABLE `boostack_user_privilege` ADD PRIMARY KEY (`id`);

ALTER TABLE `boostack_user_registration` ADD PRIMARY KEY (`id`);

ALTER TABLE `boostack_user_social`
ADD PRIMARY KEY (`id`),
ADD KEY `id` (`id`);

ALTER TABLE `boostack_user_sso`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `user_id` (`user_id`, `provider`) USING BTREE;

ALTER TABLE `boostack_api_request`
MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `boostack_asset`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `boostack_event`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `boostack_http_session`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `boostack_log`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `boostack_notification`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `boostack_notification_email`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `boostack_notification_web`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `boostack_session_variable`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `boostack_user`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `boostack_user_api`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `boostack_user_privilege`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `boostack_user_sso`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `boostack_event`
ADD CONSTRAINT `boostack_event_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `boostack_http_session`
ADD CONSTRAINT `http_session_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE;

ALTER TABLE `boostack_notification`
ADD CONSTRAINT `boostack_notification_ibfk_1` FOREIGN KEY (`id_event`) REFERENCES `boostack_event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `boostack_notification_ibfk_2` FOREIGN KEY (`id_user_from`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `boostack_notification_email`
ADD CONSTRAINT `boostack_notification_email_ibfk_1` FOREIGN KEY (`id_notification`) REFERENCES `boostack_notification` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `boostack_notification_email_ibfk_2` FOREIGN KEY (`id_user_to`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `boostack_notification_web`
ADD CONSTRAINT `boostack_notification_web_ibfk_1` FOREIGN KEY (`id_notification`) REFERENCES `boostack_notification` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `boostack_notification_web_ibfk_2` FOREIGN KEY (`id_user_to`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `boostack_session_variable`
ADD CONSTRAINT `session_variable_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `boostack_http_session` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `boostack_user`
ADD CONSTRAINT `boostack_user_ibfk_1` FOREIGN KEY (`privilege`) REFERENCES `boostack_user_privilege` (`id`);

ALTER TABLE `boostack_user_api`
ADD CONSTRAINT `boostack_user_api_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `boostack_user_info`
ADD CONSTRAINT `user_info_ibfk_1` FOREIGN KEY (`id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `boostack_user_registration`
ADD CONSTRAINT `user_registration_ibfk_1` FOREIGN KEY (`id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `boostack_user_social`
ADD CONSTRAINT `user_social_ibfk_1` FOREIGN KEY (`id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `boostack_user_sso`
ADD CONSTRAINT `boostack_user_sso_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO
    `boostack_user_privilege` (`id`, `title`, `description`)
VALUES (
        0,
        'SYSTEM',
        'only \"boostack\" user'
    ),
    (1, 'SUPERADMIN', ''),
    (2, 'ADMIN', ''),
    (3, 'USER', '');

COMMIT;