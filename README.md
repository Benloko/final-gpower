# Gpower E-commerce Website

Professional e-commerce website for Gpower - Power Equipment Sales

## Features

### Public Website
- **Responsive Design**: Fully responsive Bootstrap 5 layout optimized for mobile and desktop
- **Multi-language Support**: English and French language options (easily expandable)
- **Product Catalog**: Browse products by category with search and filtering
- **Product Details**: Detailed product pages with image galleries
- **WhatsApp Integration**: Direct WhatsApp contact button on every product
- **Modern UI**: Professional design with smooth animations and transitions

### Admin Panel
- **Secure Login**: Password-protected admin area
- **Dashboard**: Overview of products, categories, and statistics
- **Product Management**: 
  - Add, edit, and delete products
  - Multiple image gallery support
  - Featured products option
  - Price management
- **Category Management**: 
  - Create and manage product categories
  - Category images
  - Active/inactive status
- **Settings**: Configure site information, contact details, and WhatsApp number

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 8.0+
- **Frontend**: 
  - HTML5/CSS3
  - Bootstrap 5.3
  - JavaScript (ES6+)
  - Font Awesome 6.4

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 8.0 or higher
- Apache/Nginx web server
- mod_rewrite enabled (for Apache)

### Setup Instructions

1. **Import Database**
   ```bash
   mysql -u root -p < database.sql
   ```

2. **Configure Database**
   Edit `/config/database.php` and update:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'gpower_db');
   ```

3. **Configure Base URL**
   Edit `/config/config.php` and update:
   ```php
   define('BASE_URL', 'http://yourdomain.com');
   ```

4. **Set Permissions**
   ```bash
   chmod 755 -R /path/to/gpower
   chmod 777 -R /path/to/gpower/uploads
   ```

5. **Access Admin Panel**
   - URL: `http://yourdomain.com/admin/`
   - Default credentials:
     - Username: `admin`
     - Password: `admin123`
   - **IMPORTANT**: Change the default password immediately!

## Directory Structure

```
gpower/
├── admin/                  # Admin panel
│   ├── includes/          # Admin header/footer
│   ├── login.php          # Admin login
│   ├── index.php          # Dashboard
│   ├── categories.php     # Category management
│   ├── products.php       # Product management
│   └── settings.php       # Site settings
├── assets/                # Static assets
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   └── images/           # Static images
├── config/               # Configuration files
│   ├── config.php        # Main config
│   └── database.php      # Database connection
├── includes/             # Shared includes
│   ├── header.php        # Site header
│   ├── footer.php        # Site footer
│   └── language.php      # Language handler
├── lang/                 # Language files
│   ├── en.php           # English translations
│   └── fr.php           # French translations
├── uploads/              # Uploaded files
│   └── products/        # Product images
├── index.php             # Homepage
├── products.php          # Product listing
├── product-details.php   # Product detail page
├── categories.php        # Category listing
├── about.php            # About page
├── contact.php          # Contact page
└── database.sql         # Database schema
```

## Usage

### Admin Operations

#### Adding Categories
1. Login to admin panel
2. Go to Categories → Add New Category
3. Fill in category details and upload image
4. Save category

#### Adding Products
1. Go to Products → Add New Product
2. Fill in product details:
   - Name, category, price
   - Description and specifications
   - Upload main image
   - Add gallery images (multiple)
   - Set as featured (optional)
3. Save product

#### Managing Settings
1. Go to Settings
2. Update site information:
   - Site name and email
   - Phone number
   - WhatsApp number (for contact button)
3. Change admin password if needed

### Multi-language

The site supports multiple languages. Admin only works in English, but the frontend automatically translates based on user selection.

To add a new language:
1. Create a new language file in `/lang/` (e.g., `es.php`)
2. Copy the structure from `en.php`
3. Translate all strings
4. Update the language switcher in `/includes/header.php`

### WhatsApp Integration

The WhatsApp button appears on:
- Every product card
- Product detail pages
- Footer (floating button)

Configure the WhatsApp number in Admin → Settings.

## Security Notes

- Change default admin password immediately
- Use strong passwords
- Keep PHP and MySQL updated
- Set proper file permissions
- Consider using HTTPS
- Backup database regularly

## Customization

### Changing Colors
Edit `/assets/css/style.css` and modify the CSS variables:
```css
:root {
    --primary-color: #0d6efd;
    --secondary-color: #6c757d;
    /* Add your colors */
}
```

### Adding New Features
- Product reviews
- Shopping cart
- Payment integration
- Email notifications
- Advanced search

## Support

For issues or questions:
- Email: contact@gpower.com
- WhatsApp: Configure in settings

## License

Proprietary - All rights reserved

## Credits

Developed for Gpower
- Bootstrap 5
- Font Awesome
- PHP/MySQL
