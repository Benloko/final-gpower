-- Migration: add quantity column to products table
-- Run this with your MySQL client or via phpMyAdmin.

ALTER TABLE `products`
  ADD COLUMN `quantity` INT NOT NULL DEFAULT 0 AFTER `price`;
