#!/bin/bash

echo "======================================"
echo "Gpower - Installation Script"
echo "======================================"
echo ""

# Create database and user
echo "Creating database and user..."
sudo mysql <<EOF
DROP DATABASE IF EXISTS gpower_db;
CREATE DATABASE gpower_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'gpower_user'@'localhost' IDENTIFIED BY 'gpower_pass_2025';
GRANT ALL PRIVILEGES ON gpower_db.* TO 'gpower_user'@'localhost';
FLUSH PRIVILEGES;
EOF

if [ $? -eq 0 ]; then
    echo "✓ Database and user created successfully"
else
    echo "✗ Failed to create database. Please run manually."
    exit 1
fi

# Import database schema
echo "Importing database schema..."
sudo mysql gpower_db < database.sql

if [ $? -eq 0 ]; then
    echo "✓ Database schema imported successfully"
else
    echo "✗ Failed to import schema. Please check database.sql"
    exit 1
fi

# Update config file
echo "Updating configuration..."
sed -i "s/define('DB_USER', 'root');/define('DB_USER', 'gpower_user');/" config/database.php
sed -i "s/define('DB_PASS', '');/define('DB_PASS', 'gpower_pass_2025');/" config/database.php

echo "✓ Configuration updated"

# Set permissions
echo "Setting permissions..."
chmod -R 755 .
chmod -R 777 uploads/

echo "✓ Permissions set"

echo ""
echo "======================================"
echo "Installation Complete!"
echo "======================================"
echo ""
echo "Access your site:"
echo "  Frontend: http://localhost:8000"
echo "  Admin: http://localhost:8000/admin/"
echo ""
echo "Default admin credentials:"
echo "  Username: admin"
echo "  Password: admin123"
echo ""
echo "Database credentials:"
echo "  User: gpower_user"
echo "  Password: gpower_pass_2025"
echo "  Database: gpower_db"
echo ""
echo "⚠️  IMPORTANT: Change admin password after first login!"
echo ""
