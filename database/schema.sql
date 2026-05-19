CREATE DATABASE IF NOT EXISTS quote_request_manager
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE quote_request_manager;

CREATE TABLE IF NOT EXISTS quote_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    sector VARCHAR(50) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT NULL,
    deleted_at TIMESTAMP DEFAULT NULL
);