# GameTopUp Premium - E-commerce Platform

A scalable, high-performance e-commerce platform for gaming gift cards and top-ups, designed to handle 10,000+ users and 100,000+ orders without performance drops.

## Features

- **Scalable Architecture**: Built for high-traffic e-commerce with optimized database queries
- **Email OTP Verification**: Secure user registration with email verification
- **Admin Panel**: Comprehensive management for users, orders, products, variations, meta data, payments, mail settings, currency, general settings, SEO, logo upload, and maintenance mode
- **Database-Driven Catalog**: Full CRUD for products, variations, and meta fields with bulk import/export
- **Pluggable Payment System**: Test payment gateway with extensible driver interface
- **Web Installer**: One-time setup wizard for easy deployment
- **Maintenance Mode**: Global site toggle with 503 response
- **Order Management**: Order notes with email notifications, CSV export/import
- **Multi-currency Support**: Configurable currencies with exchange rates
- **SEO Optimization**: Meta fields for pages and products

## Quick Start

### Option 1: Web Installer (Recommended)

1. Upload files to your web server
2. Visit `https://yourdomain.com/install` or `https://yourdomain.com/insall`
3. Follow the setup wizard to configure database and create admin account
4. Start using your e-commerce platform!

### Option 2: Manual Setup

1. **Database Setup**:
   ```sql
   -- Option A: Use the complete demo.sql file
   mysql -u your_user -p your_database < demo.sql
   
   -- Option B: Run migrations manually
   php bin/migrate.php
   ```

2. **Environment Configuration**:
   ```bash
   cp .env.example .env
   # Edit .env with your database and mail settings
   ```

3. **Create Admin User**:
   ```sql
   INSERT INTO users (email, password, is_verified, is_admin) 
   VALUES ('admin@example.com', '$2y$10$...', 1, 1);
   ```

## Requirements

- **PHP**: 8.0+ with extensions: `pdo_mysql`, `mbstring`, `curl`, `openssl`, `json`
- **Database**: MySQL 5.7+ or MariaDB 10.2+
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Storage**: 100MB+ for application files, additional space for uploads

## Installation

### Local Development

```bash
# Clone repository
git clone https://github.com/your-repo/gametopup.git
cd gametopup

# Install dependencies (optional - installer works without Composer)
composer install

# Start PHP server
php -S localhost:8000

# Visit http://localhost:8000/install
```

### VPS Deployment

#### Apache Configuration

```apache
# Virtual host configuration
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/your-site
    
    <Directory /var/www/your-site>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/your-site_error.log
    CustomLog ${APACHE_LOG_DIR}/your-site_access.log combined
</VirtualHost>
```

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/your-site; # Point to your project root
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.x-fpm.sock; # Adjust PHP-FPM socket
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Deny access to .env and other sensitive files
    location ~ /\.env {
        deny all;
    }
    
    # Handle uploads
    location /uploads {
        try_files $uri =404;
    }
}
```

#### HestiaCP Setup

1. Create domain in HestiaCP
2. Upload files to `/home/admin/web/yourdomain.com/public_html/`
3. Set document root to `/home/admin/web/yourdomain.com/public_html/`
4. Enable PHP 8.0+ in domain settings
5. Create MySQL database and user
6. Visit `https://yourdomain.com/install`

#### Hostinger VPS Setup

1. Connect via SSH to your VPS
2. Install LAMP stack:
   ```bash
   sudo apt update
   sudo apt install apache2 mysql-server php8.1 php8.1-mysql php8.1-curl php8.1-mbstring php8.1-openssl php8.1-json
   ```
3. Upload files to `/var/www/html/`
4. Set permissions:
   ```bash
   sudo chown -R www-data:www-data /var/www/html/
   sudo chmod -R 755 /var/www/html/
   sudo chmod 666 /var/www/html/.env
   ```
5. Create database and user
6. Visit `https://yourdomain.com/install`

## Usage

### Admin Panel

Access admin panel at `/admin` with your admin credentials.

**Features**:
- **Dashboard**: Overview of orders, users, and revenue
- **Products**: Add/edit/delete products, variations, and meta fields
- **Orders**: View orders, add notes, export/import CSV
- **Users**: Manage user accounts, export/import CSV
- **Settings**: Site configuration, SMTP, currencies, payment methods
- **Maintenance**: Toggle maintenance mode

### API Endpoints

#### Authentication
- `POST /api/auth/register` - User registration with email OTP
- `POST /api/auth/verify-otp` - Verify email OTP
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `GET /api/auth/me` - Get current user info

#### Catalog
- `GET /api/catalog/list` - List products with search/filter/pagination
- `GET /api/catalog/detail/{id}` - Get product with variations

#### Orders
- `POST /api/checkout/orders` - Create new order
- `GET /api/orders/list` - List user orders

#### Admin APIs
- `GET /api/admin/products` - List all products
- `POST /api/admin/products` - Create/update product
- `DELETE /api/admin/products/{id}` - Delete product
- `GET /api/admin/orders` - List all orders
- `POST /api/admin/orders/{id}/notes` - Add order note
- `GET /api/admin/users` - List all users
- `GET /api/admin/settings` - Get site settings
- `POST /api/admin/settings` - Update site settings

### Maintenance Mode

Toggle maintenance mode in admin settings. When enabled, all requests return 503 status with maintenance page.

### CSV Import/Export

**Export Users**:
```bash
curl -X GET "https://yourdomain.com/api/admin/users/export" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Import Users**:
```bash
curl -X POST "https://yourdomain.com/api/admin/users/import" \
  -H "Content-Type: multipart/form-data" \
  -F "file=@users.csv"
```

## File Structure

```
/
├── index.php                 # Main front controller
├── .htaccess                 # URL rewriting rules
├── demo.sql                  # Complete database schema and sample data
├── composer.json             # PHP dependencies
├── .env.example              # Environment variables template
├── README.md                 # This file
├── src/                      # Application source code
│   ├── Core/                 # Core classes (App, Router, DB)
│   ├── Http/Controllers/     # HTTP controllers
│   └── Support/              # Helper classes
├── views/                    # View templates
│   ├── admin/                # Admin panel views
│   └── install/              # Installer views
├── database/migrations/      # Database migration files
├── bin/                      # Command-line scripts
├── uploads/                  # File uploads directory
└── public/                   # Public assets (CSS, JS, images)
```

## Database Schema

The platform uses a comprehensive database schema with the following main tables:

- **users**: User accounts with admin flags
- **products**: Product catalog with variations
- **product_variations**: Product variations (size, color, etc.)
- **product_meta**: Custom meta fields for products
- **variation_meta**: Custom meta fields for variations
- **orders**: Order management
- **order_items**: Order line items
- **order_notes**: Order notes with email notifications
- **currencies**: Multi-currency support
- **payment_methods**: Pluggable payment gateways
- **settings**: Site configuration
- **pages_meta**: SEO meta fields

## Troubleshooting

### Installer 500 Error

The installer has been fixed to work without Composer dependencies. If you still get errors:

1. Check PHP extensions: `php -m | grep -E "(pdo_mysql|mbstring|curl|openssl|json)"`
2. Check file permissions: `chmod 755 /path/to/project`
3. Check error logs: `/var/log/apache2/error.log` or `/var/log/nginx/error.log`

### Database Connection Issues

1. Verify database credentials in `.env`
2. Check MySQL service: `sudo systemctl status mysql`
3. Test connection: `mysql -u username -p database_name`

### Upload Issues

1. Create uploads directory: `mkdir uploads && chmod 755 uploads`
2. Check web server permissions
3. Verify PHP upload settings in `php.ini`

## Git Workflow

The project uses the main branch as the primary codebase. All features are developed and tested before being merged to main.

**Important**: The project root (`/`) is the web document root, not `/public/`. This simplifies deployment and eliminates the need to configure web servers to point to a subdirectory.

## Support

For issues and questions:
1. Check the troubleshooting section above
2. Review error logs
3. Ensure all requirements are met
4. Try the web installer at `/install`

## License

This project is licensed under the MIT License.