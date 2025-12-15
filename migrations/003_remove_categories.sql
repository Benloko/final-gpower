-- Remove category system from database

-- Drop foreign key constraint first
ALTER TABLE products DROP FOREIGN KEY products_ibfk_1;

-- Drop category_id column and its index from products table
ALTER TABLE products DROP INDEX idx_category;
ALTER TABLE products DROP COLUMN category_id;

-- Drop categories table
DROP TABLE IF EXISTS categories;
