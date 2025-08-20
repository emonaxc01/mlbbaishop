# GameTopUp Premium

Modern, scalable PHP app for game top-ups/e-commerce with:
- Email OTP auth, sessions
- DB-driven catalog (products, variations, meta)
- Orders, order items, payments (pluggable drivers, includes TestPay)
- Admin panel: users, orders, catalog, currencies, payments, settings, SEO, logo upload
- Maintenance mode, CSV import/export (users/orders)
- Web installer (write .env, run migrations, create admin)

Project layout:
- Root is the web docroot. `.htaccess` routes to `index.php`.
- `index.php`: front controller and routes
- `src/`: framework-lite core and controllers
- `views/`: PHP views (admin pages, installer)
- `database/migrations/`: SQL migrations and seeds
- `bin/migrate.php`: migration runner

Requirements:
- PHP 8.0+
- MySQL/MariaDB
- Composer
- SMTP (for email OTP)

Install (local):
1) Clone and install deps
   - git clone <repo>
   - cd project && composer install
2) Configure environment
   - cp .env.example .env
   - Fill DB_ and MAIL_ values
3) Run migrations
   - php bin/migrate.php
4) Serve
   - php -S 0.0.0.0:8000 -t .

Web installer (alternative):
- Visit `/install` (or `/insall`)
  - Requirements check
  - Save .env
  - Run migrations
  - Create initial admin

Deploy to VPS (Ubuntu/Apache example):
1) System deps
   - sudo apt update && sudo apt install -y apache2 libapache2-mod-php php php-mysql php-xml php-mbstring php-curl unzip git
2) Clone to /var/www/site
   - sudo git clone <repo> /var/www/site
   - cd /var/www/site && composer install
3) Env + DB
   - cp .env.example .env; edit DB_ / MAIL_
   - php bin/migrate.php
4) Apache vhost
   - DocumentRoot /var/www/site
   - <Directory /var/www/site> AllowOverride All </Directory>
   - sudo a2ensite site && sudo a2enmod rewrite && sudo systemctl reload apache2
5) DNS + SSL
   - Point domain to server IP
   - sudo apt install -y certbot python3-certbot-apache && sudo certbot --apache -d yourdomain.com

Usage:
- Homepage: dynamic SPA served via `views/home.php`
- Admin (login, then): `/admin`, `/admin/catalog`, `/admin/orders`, `/admin/users`, `/admin/settings`
- Maintenance mode: toggle in Settings
- CSV import/export on Users/Orders pages

APIs (selected):
- Auth: POST `/api/auth/register`, `/api/auth/verify-otp`, `/api/auth/login`, `/api/auth/logout`, GET `/api/auth/me`
- Catalog: GET `/api/catalog` (q, page, limit), GET `/api/catalog/detail?slug=...`
- Checkout: POST `/api/checkout/orders` { items: [{ productId, variationId?, quantity }], paymentMethod }
- Orders: GET `/api/orders`

Git workflow (public -> main directory):
- The app is designed to run from the repository root (no public/ webroot required)
- Front controller moved to `/index.php`; `public/index.php` remains as a passthrough for legacy hosting
- Branching:
  - Feature branches (e.g., `feature/admin-panel`) merged into `main`
  - Deploy from `main`