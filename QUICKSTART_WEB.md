# Web Version - Quick Start Guide

## ⚡ 5-Minute Setup

### Step 1: Create MySQL Database

**Option A: Using phpMyAdmin**
1. Open phpMyAdmin in browser
2. Go to "SQL" tab
3. Copy-paste contents of `setup.sql`
4. Click "Go"

**Option B: Using MySQL CLI**
```bash
mysql -u root -p < setup.sql
```

### Step 2: Configure Database

Edit `includes/config.php`:
```php
define('DB_HOST', 'localhost');      // Usually localhost
define('DB_USER', 'root');           // Your MySQL user
define('DB_PASS', '');               // Your MySQL password
define('DB_NAME', 'library_db');     // Database name (from setup.sql)
```

### Step 3: Place Files

1. Copy entire `library_web` folder to:
   - **XAMPP**: `C:\xampp\htdocs\library_web`
   - **WAMP**: `C:\wamp\www\library_web`
   - **LAMP**: `/var/www/html/library_web`

2. Set permissions (Linux/Mac):
   ```bash
   chmod -R 755 library_web
   ```

### Step 4: Open in Browser

```
http://localhost/library_web/login.php
```

## 🔑 Login

**Use these demo credentials:**

```
Username: john_doe
Password: password123
```

Or register a new account.

## 🎯 First Steps

1. **View Dashboard**: See your activity overview
2. **Browse Books**: Visit "Books" section
3. **Search**: Use search bar to find books
4. **Borrow**: Click "Borrow" on any available book
5. **Check My Loans**: View all your active loans
6. **Review Books**: Leave ratings and comments

## 📊 Sample Data Included

**Pre-loaded Users:**
- john_doe
- jane_smith
- bob_johnson

**Pre-loaded Books:**
- Clean Code
- The Pragmatic Programmer
- Design Patterns
- To Kill a Mockingbird
- 1984

All have password: `password123`

## ⚙️ Important Settings

Edit `includes/config.php` to change:

```php
// Session timeout (default 1 hour)
define('SESSION_TIMEOUT', 3600);

// Borrowing period (lines 14 days in borrow.php)
$dueDate = date('Y-m-d H:i:s', strtotime('+14 days'));

// Fine rate ($0.50/day in functions.php)
function calculateFine($dueDate, $dailyRate = 0.50)

// Debug mode (set to false in production)
define('DEBUG_MODE', true);
```

## 🔧 Common Issues

### "Connection failed: Access denied"
✓ Check username/password in config.php
✓ Ensure MySQL is running
✓ Verify user has database_name.* permissions

### "Table 'library_db.users' doesn't exist"
✓ Run setup.sql again to create tables
✓ Check DB_NAME in config.php

### "CSS and JS not loading"
✓ Update SITE_URL in config.php
✓ Ensure files are in correct directories
✓ Clear browser cache (Ctrl+F5)

### Can't login after registration
✓ Check MySQL for records: 
   ```sql
   SELECT * FROM users;
   ```
✓ Verify password stored correctly

## 📁 File Locations

| File | Purpose |
|------|---------|
| `login.php` | Login page |
| `register.php` | New user registration |
| `dashboard.php` | Main dashboard |
| `pages/books.php` | Book catalog |
| `pages/my-lending.php` | Your loans |
| `pages/reservations.php` | Your reservations |
| `pages/reviews.php` | Your reviews |
| `pages/profile.php` | Profile settings |
| `includes/config.php` | **EDIT THIS** - Database config |

## 🌐 Features Overview

| Feature | Location |
|---------|----------|
| Search Books | `pages/books.php` |
| Borrow Book | Books page → "Borrow" button |
| Return Book | `pages/my-lending.php` → "Return" button |
| Renew Loan | `pages/my-lending.php` → "Renew" button |
| Reserve Book | Books page → "Reserve" button (if unavailable) |
| View Reservations | `pages/reservations.php` |
| Leave Review | `pages/reviews.php` or book detail page |
| Update Profile | `pages/profile.php` |

## 🚀 Production Deployment

When going live:

1. **Database**:
   ```bash
   mysqldump -u root -p library_db > backup.sql
   ```

2. **Config**:
   ```php
   define('DEBUG_MODE', false);
   define('SITE_URL', 'https://yourdomain.com/library');
   ```

3. **Security**:
   - Use HTTPS
   - Strong MySQL password
   - Regular backups
   - Update PHP regularly

4. **Performance**:
   - Enable gzip compression
   - Use CDN for images
   - Optimize database indexes

## 📞 Quick Reference

| Task | Location |
|------|----------|
| Configure DB | `includes/config.php` |
| Edit CSS | `css/style.css` |
| Modify JS | `js/script.js` |
| Change fine rate | `includes/functions.php` |
| Setup database | `setup.sql` |

## ✅ Verification

After setup, verify:
- [ ] Can access login page: `http://localhost/library_web/login.php`
- [ ] Can login with john_doe/password123
- [ ] Dashboard loads with statistics
- [ ] Can see books in catalog
- [ ] Can borrow/return books
- [ ] CSS styling appears correct
- [ ] No error messages in console

## 🎉 Ready to Go!

Your web-based Library Management System is ready!

Start at: **`http://localhost/library_web/login.php`**

---

**Need help?** Check the errors in the browser console or PHP error log.

**Documentation:** See README_WEB.md for detailed information.
