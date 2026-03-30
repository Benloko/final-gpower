-- Migration: Add PDF and Stock tracking to products
-- Created: 2026-03-29

ALTER TABLE `products` ADD COLUMN `pdf_path` VARCHAR(255) NULL AFTER `main_image`;
ALTER TABLE `products` ADD COLUMN `stock_number` INT DEFAULT 0 AFTER `quantity`;

-- Add index for stock queries
CREATE INDEX `idx_stock_number` ON `products` (`stock_number`);
