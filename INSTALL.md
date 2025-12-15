# Gpower - Manual Installation Guide

## Option 1: Automatic Installation (Recommended)

Run the installation script:
```bash
./install.sh
```

This will:
- Create database `gpower_db`
- Create user `gpower_user` with password `gpower_pass_2025`
- Import database schema
- Set proper permissions

## Option 2: Manual Installation

### Step 1: Create Database and User

Open MySQL as root:
```bash
sudo mysql
```

Then run these commands:
```sql
CREATE DATABASE IF NOT EXISTS gpower_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'gpower_user'@'localhost' IDENTIFIED BY 'gpower_pass_2025';
GRANT ALL PRIVILEGES ON gpower_db.* TO 'gpower_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 2: Import Database Schema

```bash
sudo mysql gpower_db < database.sql
```

### Step 3: Update Configuration

Edit `config/database.php`:
```php
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'gpower_user');
define('DB_PASS', 'gpower_pass_2025');
define('DB_NAME', 'gpower_db');
```

### Step 4: Set Permissions

```bash
chmod -R 755 .
chmod -R 777 uploads/
```

### Step 5: Start Server

```bash
php -S localhost:8000
```

### Step 6: Access the Site

- Frontend: http://localhost:8000
- Admin: http://localhost:8000/admin/

**Default admin credentials:**
- Username: `admin`
- Password: `admin123`

⚠️ **Change the admin password immediately after first login!**

## Troubleshooting

### Error: Access denied for user 'root'@'localhost'

This happens because MySQL uses socket authentication. Use `sudo mysql` instead of `mysql -u root -p`.

### Error: Cannot create directory uploads/

Run: `chmod -R 777 uploads/`

### Error: Database connection failed

Check your credentials in `config/database.php` match what you created in MySQL.

## Using with Apache/Nginx

If using Apache or Nginx instead of PHP built-in server:

1. Point document root to `/path/to/gpower/`
2. Enable mod_rewrite (Apache)
3. Update `BASE_URL` in `config/config.php`

Example Apache config:
```apache
<VirtualHost *:80>
    DocumentRoot /path/to/gpower
    ServerName gpower.local
    
    <Directory /path/to/gpower>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
