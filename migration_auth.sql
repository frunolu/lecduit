-- Migration: Add authentication columns to users table
-- Run this SQL in your database management tool (phpMyAdmin, Adminer, etc.)

USE lecduit;

ALTER TABLE `users` 
ADD COLUMN `password_hash` VARCHAR(255) DEFAULT NULL AFTER `email`,
ADD COLUMN `email_verified` TINYINT(1) DEFAULT 0 AFTER `password_hash`,
ADD COLUMN `verification_token` VARCHAR(64) DEFAULT NULL AFTER `email_verified`,
ADD COLUMN `reset_token` VARCHAR(64) DEFAULT NULL AFTER `verification_token`,
ADD COLUMN `reset_token_expires` DATETIME DEFAULT NULL AFTER `reset_token`;

-- Verify the changes
DESCRIBE users;
