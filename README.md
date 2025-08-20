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
- POST `/api/orders` { game, productId, variationCode, playerId, amount, currency, paymentMethod }
- GET `/api/orders`

The UI in `index.php` is preserved and rendered via `views/home.php`.