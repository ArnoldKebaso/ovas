-- OVAS - Veterinary Management & Appointment System
-- Plain PHP schema (normalized) with seeds
-- MySQL 8 / MariaDB, UTF8MB4
-- Timezone: Africa/Nairobi

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE */;
/*!40101 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS */;
/*!40101 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET time_zone = "+03:00";
START TRANSACTION;
/*!40101 SET NAMES utf8mb4 */;

-- Create DB if not exists
CREATE DATABASE IF NOT EXISTS `ovas_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ovas_db`;

-- =========================================================
-- DROP old/new tables in safe order (BACK UP FIRST!)
-- =========================================================

DROP TABLE IF EXISTS `notification_logs`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `appointments`;
DROP TABLE IF EXISTS `time_slots`;
DROP TABLE IF EXISTS `pets`;
DROP TABLE IF EXISTS `services`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `login_attempts`;
-- keep system_info/messages if you already use them, but recreate below:
DROP TABLE IF EXISTS `system_info`;
DROP TABLE IF EXISTS `message_list`;

-- Legacy tables from older build (drop if present):
DROP TABLE IF EXISTS `appointment_list`;
DROP TABLE IF EXISTS `service_list`;
DROP TABLE IF EXISTS `category_list`;

-- =========================================================
-- USERS
-- =========================================================
CREATE TABLE `users` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(120)    NOT NULL,
  `email`         VARCHAR(190)    NOT NULL,
  `phone`         VARCHAR(40)     DEFAULT NULL,
  `address`       VARCHAR(255)    DEFAULT NULL,
  `password_hash` VARCHAR(255)    NOT NULL,
  `is_admin`      TINYINT(1)      NOT NULL DEFAULT 0,
  `email_verified_at` DATETIME    DEFAULT NULL,
  `status`        TINYINT(1)      NOT NULL DEFAULT 1, -- 1 active, 0 disabled
  `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_users_email` (`email`),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- seed admin (password = "password" -> standard bcrypt sample)
INSERT INTO `users` (`name`,`email`,`phone`,`address`,`password_hash`,`is_admin`)
VALUES ('Administrator','admin@ovas.test','0700000000','Nairobi',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- =========================================================
-- PETS
-- =========================================================
CREATE TABLE `pets` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `name`       VARCHAR(120)    NOT NULL,
  `species`    ENUM('dog','cat','rabbit','bird','other') NOT NULL DEFAULT 'dog',
  `breed`      VARCHAR(120)    DEFAULT NULL,
  `age`        VARCHAR(40)     DEFAULT NULL,
  `weight`     VARCHAR(40)     DEFAULT NULL,
  `notes`      TEXT            DEFAULT NULL,
  `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pets_user` (`user_id`),
  CONSTRAINT `fk_pets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- SERVICES
-- =========================================================
CREATE TABLE `services` (
  `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(120)    NOT NULL,
  `description`  TEXT            DEFAULT NULL,
  `fee`          DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  `duration_min` INT             NOT NULL DEFAULT 30,
  `is_active`    TINYINT(1)      NOT NULL DEFAULT 1,
  `created_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_services_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- seed core services
INSERT INTO `services` (`name`,`description`,`fee`,`duration_min`) VALUES
('Vaccination','Routine vaccinations and boosters for common diseases.',1700.00,30),
('Check-up','Comprehensive physical examination and basic diagnostics.',500.00,30),
('Dental Care','Dental cleaning and oral health procedures.',2500.00,60),
('Grooming','Bathing, nail trimming, fur care.',1200.00,45),
('Surgery','Elective and emergency surgical procedures.',15000.00,120),
('Diagnostics','Lab tests and imaging as required.',2000.00,45);

-- =========================================================
-- TIME SLOTS
-- =========================================================
CREATE TABLE `time_slots` (
  `id`               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `start_time`       TIME          NOT NULL,
  `end_time`         TIME          NOT NULL,
  `duration_min`     INT           NOT NULL DEFAULT 30,
  `max_appointments` INT           NOT NULL DEFAULT 1,
  `is_active`        TINYINT(1)    NOT NULL DEFAULT 1,
  `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_timeslots_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- seed 08:00–18:00 in 30-min blocks
INSERT INTO `time_slots` (`start_time`,`end_time`,`duration_min`,`max_appointments`,`is_active`) VALUES
('08:00:00','08:30:00',30,2,1),('08:30:00','09:00:00',30,2,1),
('09:00:00','09:30:00',30,2,1),('09:30:00','10:00:00',30,2,1),
('10:00:00','10:30:00',30,2,1),('10:30:00','11:00:00',30,2,1),
('11:00:00','11:30:00',30,2,1),('11:30:00','12:00:00',30,2,1),
('12:00:00','12:30:00',30,2,1),('12:30:00','13:00:00',30,2,1),
('13:00:00','13:30:00',30,2,1),('13:30:00','14:00:00',30,2,1),
('14:00:00','14:30:00',30,2,1),('14:30:00','15:00:00',30,2,1),
('15:00:00','15:30:00',30,2,1),('15:30:00','16:00:00',30,2,1),
('16:00:00','16:30:00',30,2,1),('16:30:00','17:00:00',30,2,1),
('17:00:00','17:30:00',30,2,1),('17:30:00','18:00:00',30,2,1);

-- =========================================================
-- APPOINTMENTS
-- =========================================================
CREATE TABLE `appointments` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`           VARCHAR(50)     NOT NULL,
  `user_id`        BIGINT UNSIGNED NOT NULL,
  `pet_id`         BIGINT UNSIGNED NOT NULL,
  `service_id`     INT UNSIGNED    NOT NULL,
  `time_slot_id`   INT UNSIGNED    NOT NULL,
  `schedule_date`  DATE            NOT NULL,
  `status`         ENUM('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_status` ENUM('unpaid','paid','refunded') NOT NULL DEFAULT 'unpaid',
  `total_fee`      DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  `notes`          TEXT            DEFAULT NULL,
  `google_event_id` VARCHAR(128)   DEFAULT NULL,
  `created_at`     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_appointments_code` (`code`),
  KEY `idx_appt_user` (`user_id`),
  KEY `idx_appt_date` (`schedule_date`),
  KEY `idx_appt_status` (`status`),
  CONSTRAINT `fk_appt_user`     FOREIGN KEY (`user_id`)      REFERENCES `users`     (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_appt_pet`      FOREIGN KEY (`pet_id`)       REFERENCES `pets`      (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_appt_service`  FOREIGN KEY (`service_id`)   REFERENCES `services`  (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_appt_timeslot` FOREIGN KEY (`time_slot_id`) REFERENCES `time_slots`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Capacity helper (unique pseudo-constraint suggestion):
-- You will enforce "count < max_appointments" in code for (schedule_date, time_slot_id).

-- =========================================================
-- PAYMENTS (M-Pesa)
-- =========================================================
CREATE TABLE `payments` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `appointment_id` BIGINT UNSIGNED DEFAULT NULL,
  `provider`       ENUM('mpesa')   NOT NULL DEFAULT 'mpesa',
  `amount`         DECIMAL(10,2)   NOT NULL,
  `currency`       CHAR(3)         NOT NULL DEFAULT 'KES',
  `reference`      VARCHAR(64)     DEFAULT NULL, -- mpesa receipt / merchant ref
  `status`         ENUM('initiated','success','failed') NOT NULL DEFAULT 'initiated',
  `raw_payload`    JSON            DEFAULT NULL,
  `created_at`     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pay_status` (`status`),
  KEY `idx_pay_appt`   (`appointment_id`),
  CONSTRAINT `fk_pay_appt` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- NOTIFICATION LOGS (email/SMS audit)
-- =========================================================
CREATE TABLE `notification_logs` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`      BIGINT UNSIGNED DEFAULT NULL,
  `channel`      ENUM('email','sms') NOT NULL,
  `template_key` VARCHAR(64)     NOT NULL,
  `payload`      JSON            DEFAULT NULL,
  `status`       ENUM('sent','failed') NOT NULL DEFAULT 'sent',
  `error`        TEXT            DEFAULT NULL,
  `sent_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notif_user` (`user_id`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- LOGIN RATE LIMIT
-- =========================================================
CREATE TABLE `login_attempts` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`        VARCHAR(190) NOT NULL,
  `ip`           VARBINARY(16) NOT NULL,
  `attempted_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_login_email_time` (`email`,`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- SYSTEM INFO (meta key-value for UI & config)
-- Retained for compatibility with your current UI (footer/header).
-- =========================================================
CREATE TABLE `system_info` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `meta_field` VARCHAR(100) NOT NULL,
  `meta_value` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_system_info_field` (`meta_field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `system_info` (`meta_field`,`meta_value`) VALUES
('name','Veterinary Appointment System'),
('short_name','OVAS'),
('logo','uploads/logo-1641262650.png'),
('user_avatar','uploads/user_avatar.jpg'),
('cover','uploads/cover-1641262651.png'),
('email','clinic@example.com'),
('contact','07123456789'),
('address','Nairobi, Kenya'),
('from_time','08:00'),
('to_time','18:00'),
('clinic_schedule','8:00 AM - 6:00 PM'),
-- App config for backend code (read from this meta table)
('booking_fee','500'),
('appointments_auto_confirm','1'),
('appointments_cutoff_hours','3'),
('google_calendar_id',''),
('google_credentials_json_path','storage/google/credentials.json');

-- =========================================================
-- CONTACT MESSAGES (kept from your existing build)
-- =========================================================
CREATE TABLE `message_list` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fullname`     VARCHAR(150) NOT NULL,
  `contact`      VARCHAR(60)  NOT NULL,
  `email`        VARCHAR(190) NOT NULL,
  `message`      TEXT NOT NULL,
  `status`       TINYINT(1) NOT NULL DEFAULT 0,
  `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
