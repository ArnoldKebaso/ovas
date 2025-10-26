-- Error Logging Tables for OVAS System

-- Error logs table for storing application errors
CREATE TABLE IF NOT EXISTS `error_logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `level` varchar(20) NOT NULL,
    `message` text NOT NULL,
    `context` longtext DEFAULT NULL,
    `file` varchar(255) DEFAULT NULL,
    `line` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `request_uri` varchar(500) DEFAULT NULL,
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_level` (`level`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_user_id` (`user_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin alerts table for critical notifications
CREATE TABLE IF NOT EXISTS `admin_alerts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `type` varchar(50) NOT NULL,
    `title` varchar(255) NOT NULL,
    `message` text NOT NULL,
    `severity` varchar(20) NOT NULL,
    `is_read` tinyint(1) DEFAULT 0,
    `created_at` datetime NOT NULL,
    `read_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_type` (`type`),
    KEY `idx_severity` (`severity`),
    KEY `idx_is_read` (`is_read`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Performance metrics table (optional for monitoring)
CREATE TABLE IF NOT EXISTS `performance_metrics` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `endpoint` varchar(255) NOT NULL,
    `method` varchar(10) NOT NULL,
    `response_time` decimal(10,3) NOT NULL,
    `memory_usage` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_endpoint` (`endpoint`),
    KEY `idx_response_time` (`response_time`),
    KEY `idx_created_at` (`created_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;