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