# GameTopUp Premium (Scalable Backend)

Quick start:

1) Copy env

```bash
cp .env.example .env
```

2) Install dependencies

```bash
composer install
```

3) Configure MySQL and SMTP in `.env`

4) Run migrations

```bash
php bin/migrate.php
```

5) Serve (Apache/Nginx) document root -> `public/`

Or with PHP's built-in server:

```bash
php -S 0.0.0.0:8000 -t public
```

APIs:
- POST `/api/auth/register` { email, password } -> sends OTP email
- POST `/api/auth/verify-otp` { email, code } -> verifies and logs in
- POST `/api/auth/login` { email, password }
- POST `/api/auth/logout`
- Catalog: GET `/api/catalog` (q, page, limit), GET `/api/catalog/detail?slug=...`
- Checkout: POST `/api/checkout/orders` { items: [{ productId, variationId?, quantity }], paymentMethod }
- Orders: GET `/api/orders`

The UI in `index.php` is preserved and rendered via `views/home.php`.

Admin (requires `is_admin=1` user):
- Catalog CRUD: `/admin/catalog`, `/admin/catalog/product?id=...`
- Users, Orders, Settings: `/admin/users`, `/admin/orders`, `/admin/settings`

Installer:
- Basic page at `/installer` (CLI steps recommended now); can be expanded to run migrations and write env.

Deploy to VPS (Ubuntu/Apache example):
1) Install system deps
   - sudo apt update && sudo apt install -y apache2 libapache2-mod-php php php-mysql php-xml php-mbstring php-curl unzip git
2) Clone project into /var/www/your-site and set permissions
   - sudo git clone <repo> /var/www/your-site
   - cd /var/www/your-site && composer install
3) Configure env
   - cp .env.example .env and edit DB_ and MAIL_ values
   - php bin/migrate.php
4) Apache vhost
   - sudo nano /etc/apache2/sites-available/your-site.conf
   - Set DocumentRoot to /var/www/your-site/public and add AllowOverride All
   - sudo a2ensite your-site && sudo a2enmod rewrite && sudo systemctl reload apache2
5) DNS: point your domain to server IP
6) SSL (optional):
   - sudo apt install -y certbot python3-certbot-apache && sudo certbot --apache -d yourdomain.com