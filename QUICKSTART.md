# Gpower E-commerce Website - Quick Start Guide

## 🚀 Quick Start

1. **Import Database**
   - Open phpMyAdmin
   - Create database: `gpower_db`
   - Import file: `database.sql`

2. **Configure**
   - Edit `/config/database.php` with your MySQL credentials
   - Edit `/config/config.php` with your site URL

3. **Set Permissions**
   ```bash
   chmod -R 777 uploads/
   ```

4. **Access Admin**
   - URL: `http://localhost/gpower/admin/`
   - Username: `admin`
   - Password: `admin123` (Change this!)

## 📋 Features Checklist

✅ Responsive design (mobile & desktop)
✅ Product management with image galleries
✅ Category management
✅ Multi-language support (EN/FR)
✅ WhatsApp integration
✅ Admin dashboard
✅ Search & filter products
✅ Featured products
✅ SEO-friendly URLs

## 🔧 Common Tasks

### Add a Product
Admin → Products → Add New Product → Fill form → Upload images → Save

### Change WhatsApp Number
Admin → Settings → WhatsApp Number → Save

### Add Category
Admin → Categories → Add New Category → Fill details → Save

### Change Language
Click EN/FR button in top navigation

## 📱 Contact Integration

- WhatsApp button on every product
- Floating WhatsApp button (bottom-right)
- Contact form on Contact page

## 🎨 Customization

**Colors**: Edit `/assets/css/style.css`
**Layout**: Edit template files in `/includes/`
**Languages**: Add new files in `/lang/`

## ⚠️ Important Notes

- Default admin password must be changed
- Upload folder needs write permissions
- Use HTTPS in production
- Backup database regularly

## 📞 Support

Need help? Contact via WhatsApp or email configured in settings.

---

**Developed for Gpower** | Professional Power Equipment
