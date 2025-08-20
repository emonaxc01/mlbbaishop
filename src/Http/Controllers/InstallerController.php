<?php
namespace App\Http\Controllers;

use App\Core\App;
use App\Core\DB;
use App\Support\EnvWriter;
use App\Support\Request;

class InstallerController
{
    public function form(): void
    {
        App::view('install/form');
    }

    public function check(): void
    {
        $requirements = [
            'php_version' => PHP_VERSION,
            'extensions' => [
                'pdo_mysql' => extension_loaded('pdo_mysql'),
                'mbstring' => extension_loaded('mbstring'),
                'curl' => extension_loaded('curl'),
                'openssl' => extension_loaded('openssl'),
            ],
            'writable' => [
                'base' => is_writable(App::$basePath),
                '.env' => is_writable(App::$basePath . '/.env') || is_writable(App::$basePath),
            ]
        ];
        App::json($requirements);
    }

    public function saveEnv(): void
    {
        $d = Request::json();
        $vars = [
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'APP_URL' => (string)($d['app_url'] ?? ''),
            'DB_HOST' => (string)($d['db_host'] ?? '127.0.0.1'),
            'DB_PORT' => (string)($d['db_port'] ?? '3306'),
            'DB_DATABASE' => (string)($d['db_name'] ?? ''),
            'DB_USERNAME' => (string)($d['db_user'] ?? ''),
            'DB_PASSWORD' => (string)($d['db_pass'] ?? ''),
            'MAIL_HOST' => (string)($d['mail_host'] ?? ''),
            'MAIL_PORT' => (string)($d['mail_port'] ?? '587'),
            'MAIL_USERNAME' => (string)($d['mail_user'] ?? ''),
            'MAIL_PASSWORD' => (string)($d['mail_pass'] ?? ''),
            'MAIL_FROM_ADDRESS' => (string)($d['mail_from'] ?? ''),
            'MAIL_FROM_NAME' => (string)($d['mail_name'] ?? 'GameTopUp Premium'),
        ];
        $ok = EnvWriter::write($vars, App::$basePath . '/.env');
        if (!$ok) { App::json(['error'=>'Failed to write .env'], 500); return; }
        App::json(['ok'=>true]);
    }

    public function runMigrations(): void
    {
        // crude invocation of migration runner
        require App::$basePath . '/bin/migrate.php';
        App::json(['ok'=>true]);
    }

    public function createAdmin(): void
    {
        $d = Request::json();
        $email = strtolower(trim((string)($d['email'] ?? '')));
        $password = (string)($d['password'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            App::json(['error'=>'Invalid'],422); return;
        }
        $pdo = DB::conn();
        $stmt = $pdo->prepare('INSERT INTO users (email,password,is_verified,is_admin,wallet_balance,created_at,updated_at) VALUES (?,?,?,?,0,NOW(),NOW()) ON DUPLICATE KEY UPDATE password=VALUES(password), is_verified=1, is_admin=1');
        $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT), 1, 1]);
        App::json(['ok'=>true]);
    }
}
