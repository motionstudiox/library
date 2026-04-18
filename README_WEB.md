# Library Management System - Web Version

A complete web-based Library Management System built with **PHP, HTML, MySQL**, and Bootstrap 5.

## 🚀 Features

✨ **Complete Library Management**:
- ✅ User registration and authentication
- ✅ Book catalog with search and filtering
- ✅ Book borrowing and returning
- ✅ Automatic fine calculation for overdue books
- ✅ Book reservations
- ✅ Reviews and ratings (1-5 stars)
- ✅ Lending history tracking
- ✅ User profile management
- ✅ Responsive design (mobile-friendly)

## 📋 Requirements

- **PHP 7.4+** (with mysqli extension)
- **MySQL 5.7+**
- **Web Server** (Apache with mod_rewrite or Nginx)
- **Modern Browser** (Chrome, Firefox, Safari, Edge)

## 🔧 Installation

### Step 1: Set Up MySQL Database

1. Open MySQL client (phpMyAdmin, MySQL Workbench, or command line)
2. Execute the SQL script:
   ```sql
   -- Copy contents from setup.sql file
   -- This will create:
   -- - Database: library_db
   -- - Tables: users, books, lending, reservation, review
   -- - Sample data
   ```

Or via command line:
```bash
mysql -u root -p < setup.sql
```

### Step 2: Configure Database Connection

Edit `includes/config.php`:
```php
define('DB_HOST', 'localhost');      // Your MySQL host
define('DB_USER', 'root');           // Your MySQL username
define('DB_PASS', '');               // Your MySQL password
define('DB_NAME', 'library_db');     // Database name
define('DB_PORT', 3306);             // MySQL port
```

### Step 3: Deploy Files

1. Place all files in your web server directory:
   - Apache: `/var/www/html/library_web`
   - Windows: `C:\xampp\htdocs\library_web`

2. Ensure proper permissions:
   ```bash
   chmod -R 755 /path/to/library_web
   ```

### Step 4: Update Site URL

Edit `includes/config.php`:
```php
define('SITE_URL', 'http://localhost/library_web');
```

### Step 5: Access the Application

Open in your browser:
```
http://localhost/library_web/login.php
```

## 👤 Demo Credentials

**Test Users** (from database seed):

| Username | Password | Full Name |
|----------|----------|-----------|
| john_doe | password123 | John Doe |
| jane_smith | password123 | Jane Smith |
| bob_johnson | password123 | Bob Johnson |

**Note**: Passwords are hashed using PHP's `password_hash()` for security.

## 📁 Directory Structure

```
library_web/
├── index.php                    # Redirects to login
├── login.php                    # Login page
├── register.php                 # Registration page
├── logout.php                   # Logout handler
├── dashboard.php                # Main dashboard
├── setup.sql                    # Database schema
│
├── includes/
│   ├── config.php              # Configuration
│   ├── database.php            # Database connection
│   ├── session.php             # Session/auth handler
│   ├── functions.php           # Helper functions
│   ├── header.php              # Header navigation
│   └── footer.php              # Footer
│
├── pages/
│   ├── books.php               # Book catalog
│   ├── book-detail.php         # Book details
│   ├── my-lending.php          # My loans
│   ├── reservations.php        # My reservations
│   ├── reviews.php             # My reviews
│   └── profile.php             # User profile
│
├── api/
│   ├── borrow.php              # Borrow book handler
│   ├── return-book.php         # Return book handler
│   ├── renew.php               # Renew book handler
│   ├── reserve.php             # Reserve book handler
│   └── cancel-reservation.php  # Cancel reservation handler
│
├── css/
│   └── style.css               # Main stylesheet
│
├── js/
│   └── script.js               # Main JavaScript
│
└── admin/                       # Reserved for admin features
```

## 🎯 Main Pages

### Dashboard (`dashboard.php`)
- Overview of your library activity
- Quick statistics (books borrowed, reservations, overdue items)
- Recent borrowing history
- Quick action buttons

### Book Catalog (`pages/books.php`)
- Browse all library books
- Search by title, author, ISBN
- Filter by genre
- View book details
- Borrow available books
- Reserve unavailable books
- Pagination support

### My Loans (`pages/my-lending.php`)
- View active borrowings
- Check due dates and overdue status
- Return books
- Renew books
- View fine amounts
- Full lending history

### Reservations (`pages/reservations.php`)
- View active reservations
- Cancel reservations
- Track reservation status

### Reviews (`pages/reviews.php`)
- Write and edit reviews
- Leave ratings (1-5 stars)
- View your reviews
- Delete reviews

### Profile (`pages/profile.php`)
- Update personal information
- Change email
- Update phone number
- View account status
- See membership date

## 🔐 Security Features

- ✅ **Password Security**: Using PHP's `password_hash()` and `password_verify()`
- ✅ **SQL Injection Prevention**: Prepared statements with mysqli
- ✅ **XSS Prevention**: HTML escaping with `htmlspecialchars()`
- ✅ **Session Management**: Configurable session timeout
- ✅ **CSRF Tokens**: Built-in token generation and verification
- ✅ **Input Validation**: Email, phone, ISBN validation
- ✅ **Authentication**: Login required for all user pages

## 📊 Database Schema

### Users Table
```sql
id, username (unique), email (unique), password_hash, 
full_name, phone, is_active, created_at, updated_at
```

### Books Table
```sql
id, isbn (unique), title, author, description, publisher,
publication_year, genre, total_copies, available_copies, added_at
```

### Lending Table
```sql
id, user_id (FK), book_id (FK), borrow_date, due_date,
return_date, is_returned, fine_amount
```

### Reservation Table
```sql
id, user_id (FK), book_id (FK), reservation_date,
pickup_date, is_active, is_fulfilled
```

### Review Table
```sql
id, user_id (FK), book_id (FK), rating (1-5),
comment, review_date, UNIQUE(user_id, book_id)
```

## 🔄 Workflow

1. **Register**: Create account with username, email, password
2. **Login**: Access the system with credentials
3. **Browse**: Search and explore the book catalog
4. **Borrow**: Checkout available books (14-day default period)
5. **Track**: Monitor due dates and active loans
6. **Renew**: Extend lending period if not overdue
7. **Return**: Check in books (automatic fine calculation if late)
8. **Reserve**: Place hold on unavailable books
9. **Review**: Rate and review books you've borrowed

## ⚙️ Configuration

Edit `includes/config.php` to customize:

```php
// Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'library_db');

// Session timeout (in seconds)
define('SESSION_TIMEOUT', 3600);  // 1 hour

// Site information
define('SITE_NAME', 'Library Management System');
define('SITE_URL', 'http://localhost/library_web');

// Debug mode
define('DEBUG_MODE', true);  // Set to false in production
```

## 🎨 Customization

### Change Colors
Edit `css/style.css`:
```css
:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    /* ... */
}
```

### Modify Lending Period
Edit `api/borrow.php`:
```php
$dueDate = date('Y-m-d H:i:s', strtotime('+14 days'));  // Change number
```

### Change Fine Rate
See `includes/functions.php`:
```php
function calculateFine($dueDate, $dailyRate = 0.50)  // Change rate
```

## 📱 Responsive Design

The application is fully responsive and works on:
- ✅ Desktop (1920x1080 and larger)
- ✅ Tablet (768px - 1024px)
- ✅ Mobile (320px - 767px)

Uses Bootstrap 5 for responsive grid system.

## 🚀 Deployment

### Production Checklist

1. **Database**:
   - Use strong MySQL password
   - Create dedicated MySQL user
   - Regular backups

2. **Security**:
   - Set `DEBUG_MODE` to `false`
   - Use HTTPS/SSL
   - Keep PHP updated
   - Disable error display to users

3. **Performance**:
   - Enable caching headers
   - Compress CSS/JS
   - Optimize database indexes
   - Use CDN for Bootstrap/jQuery

4. **Maintenance**:
   - Regular backups
   - Monitor error logs
   - Update dependencies
   - Clean old sessions

## 🐛 Troubleshooting

### "Connection failed: Access denied"
- Check DB_USER and DB_PASS in config.php
- Ensure MySQL is running
- Verify user has database access

### "Table doesn't exist"
- Run setup.sql to create tables
- Check DB_NAME is correct

### "No permission to create temporary table"
- Run: `GRANT ALL PRIVILEGES ON library_db.* TO 'username'@'localhost';`

### Assets not loading (CSS/JS)
- Check SITE_URL is correct
- Verify file paths
- Clear browser cache

### Session issues
- Check SESSION_TIMEOUT setting
- Verify server can write to /tmp
- Check MySQL time synchronization

## 📞 Support

For issues or questions:
1. Check error logs in PHP
2. Review MySQL error log
3. Test database connection separately
4. Verify file permissions

## 📝 Changelog

### Version 1.0.0
- Initial release
- Core features implemented
- User authentication
- Book management
- Lending system
- Reservations
- Reviews

## 📄 License

This project is open source.

## 🎉 You're All Set!

Start using the application:
1. Navigate to `http://localhost/library_web/login.php`
2. Register a new account or login with demo credentials
3. Explore the library!

---

**Version**: 1.0.0  
**Last Updated**: April 2026  
**Framework**: PHP, MySQL, Bootstrap 5
