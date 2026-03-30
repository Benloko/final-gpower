-- Migration: Add identification number to products
-- Created: 2026-03-30

-- Adds a per-product identification number (optional for existing rows, required by admin UI for new/edited rows)
ALTER TABLE `products`
  ADD COLUMN `identification_number` VARCHAR(100) NULL AFTER `name`;
