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
        // Enable error reporting for installer
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        
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
                'json' => extension_loaded('json'),
            ],
            'writable' => [
                'base' => is_writable(App::$basePath),
                '.env' => is_writable(App::$basePath . '/.env') || is_writable(App::$basePath),
                'uploads' => is_writable(App::$basePath . '/uploads') || is_writable(App::$basePath),
            ],
            'errors' => [],
            'warnings' => []
        ];
        
        // Check for critical issues
        if (!extension_loaded('pdo_mysql')) {
            $requirements['errors'][] = 'PDO MySQL extension is required but not installed';
        }
        if (!extension_loaded('json')) {
            $requirements['errors'][] = 'JSON extension is required but not installed';
        }
        if (!extension_loaded('mbstring')) {
            $requirements['errors'][] = 'MBString extension is required but not installed';
        }
        if (!extension_loaded('curl')) {
            $requirements['warnings'][] = 'cURL extension is recommended for email functionality';
        }
        if (!extension_loaded('openssl')) {
            $requirements['warnings'][] = 'OpenSSL extension is recommended for security';
        }
        if (!is_writable(App::$basePath)) {
            $requirements['errors'][] = 'Base directory is not writable - check permissions';
        }
        
        // Check if .env exists and is readable
        $envPath = App::$basePath . '/.env';
        if (file_exists($envPath)) {
            if (!is_readable($envPath)) {
                $requirements['errors'][] = '.env file exists but is not readable';
            }
        } else {
            $requirements['warnings'][] = '.env file does not exist - will be created during setup';
        }
        
        // Check if database tables exist (if .env is configured)
        if (file_exists($envPath)) {
            try {
                $this->loadEnvManually($envPath);
                if (isset($_ENV['DB_HOST']) && isset($_ENV['DB_DATABASE']) && isset($_ENV['DB_USERNAME'])) {
                    $dsn = "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABASE']};charset=utf8mb4";
                    $pdo = new \PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'] ?? '', [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    ]);
                    
                    // Check if tables exist
                    $tables = ['users', 'products', 'orders', 'settings'];
                    $missingTables = [];
                    foreach ($tables as $table) {
                        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                        if ($stmt->rowCount() === 0) {
                            $missingTables[] = $table;
                        }
                    }
                    
                    if (!empty($missingTables)) {
                        $requirements['warnings'][] = 'Database tables missing: ' . implode(', ', $missingTables) . ' - run migrations to create them';
                    } else {
                        $requirements['info'][] = 'Database tables exist - setup appears complete';
                    }
                }
            } catch (\Exception $e) {
                $requirements['warnings'][] = 'Database connection test failed: ' . $e->getMessage();
            }
        }
        
        App::json($requirements);
    }

    public function saveEnv(): void
    {
        try {
            $d = Request::json();
            if (empty($d)) {
                App::json(['error' => 'No data received'], 400);
                return;
            }
            
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
            
            // Validate required fields
            if (empty($vars['DB_DATABASE']) || empty($vars['DB_USERNAME'])) {
                App::json(['error' => 'Database name and username are required'], 400);
                return;
            }
            
            $envPath = App::$basePath . '/.env';
            $ok = EnvWriter::write($vars, $envPath);
            
            if (!$ok) {
                App::json(['error' => 'Failed to write .env file. Check directory permissions.'], 500);
                return;
            }
            
            // Test database connection
            try {
                $dsn = "mysql:host={$vars['DB_HOST']};port={$vars['DB_PORT']};dbname={$vars['DB_DATABASE']};charset=utf8mb4";
                $pdo = new \PDO($dsn, $vars['DB_USERNAME'], $vars['DB_PASSWORD'], [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                ]);
                App::json(['ok' => true, 'message' => '.env file created and database connection successful']);
            } catch (\PDOException $e) {
                App::json(['error' => 'Database connection failed: ' . $e->getMessage()], 500);
            }
            
        } catch (\Exception $e) {
            App::json(['error' => 'Error saving .env: ' . $e->getMessage()], 500);
        }
    }

    public function runMigrations(): void
    {
        try {
            // Load .env first
            $envPath = App::$basePath . '/.env';
            if (!file_exists($envPath)) {
                App::json(['error' => '.env file not found. Please save environment settings first.'], 400);
                return;
            }
            
            $this->loadEnvManually($envPath);
            
            // Test database connection first
            if (!isset($_ENV['DB_HOST']) || !isset($_ENV['DB_DATABASE']) || !isset($_ENV['DB_USERNAME'])) {
                App::json(['error' => 'Database credentials not found in .env file'], 400);
                return;
            }
            
            $dsn = "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABASE']};charset=utf8mb4";
            $pdo = new \PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'] ?? '', [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);
            
            // Run migrations manually
            $sqlDir = App::$basePath . '/database/migrations';
            if (!is_dir($sqlDir)) {
                App::json(['error' => 'Migrations directory not found: ' . $sqlDir], 500);
                return;
            }
            
            $files = glob($sqlDir . '/*.sql');
            if (empty($files)) {
                App::json(['error' => 'No migration files found in: ' . $sqlDir], 500);
                return;
            }
            
            sort($files);
            $executed = [];
            $errors = [];
            
            foreach ($files as $file) {
                $sql = file_get_contents($file);
                if ($sql === false) {
                    $errors[] = 'Could not read file: ' . basename($file);
                    continue;
                }
                
                try {
                    $pdo->exec($sql);
                    $executed[] = basename($file);
                } catch (\PDOException $e) {
                    $errors[] = 'Error in ' . basename($file) . ': ' . $e->getMessage();
                }
            }
            
            if (!empty($errors)) {
                App::json(['error' => 'Migration errors: ' . implode('; ', $errors)], 500);
                return;
            }
            
            App::json(['ok' => true, 'message' => 'Migrations completed successfully', 'executed' => $executed]);
            
        } catch (\Exception $e) {
            App::json(['error' => 'Migration failed: ' . $e->getMessage()], 500);
        }
    }

    public function createAdmin(): void
    {
        try {
            $d = Request::json();
            if (empty($d)) {
                App::json(['error' => 'No data received'], 400);
                return;
            }
            
            $email = strtolower(trim((string)($d['email'] ?? '')));
            $password = (string)($d['password'] ?? '');
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                App::json(['error' => 'Invalid email address'], 422);
                return;
            }
            
            if (strlen($password) < 8) {
                App::json(['error' => 'Password must be at least 8 characters long'], 422);
                return;
            }
            
            // Load .env and connect to database
            $envPath = App::$basePath . '/.env';
            if (!file_exists($envPath)) {
                App::json(['error' => '.env file not found. Please save environment settings first.'], 400);
                return;
            }
            
            $this->loadEnvManually($envPath);
            
            if (!isset($_ENV['DB_HOST']) || !isset($_ENV['DB_DATABASE']) || !isset($_ENV['DB_USERNAME'])) {
                App::json(['error' => 'Database credentials not found in .env file'], 400);
                return;
            }
            
            $dsn = "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABASE']};charset=utf8mb4";
            $pdo = new \PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'] ?? '', [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);
            
            // Check if users table exists
            $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
            if ($stmt->rowCount() === 0) {
                App::json(['error' => 'Users table not found. Please run migrations first.'], 400);
                return;
            }
            
            $stmt = $pdo->prepare('INSERT INTO users (email,password,is_verified,is_admin,wallet_balance,created_at,updated_at) VALUES (?,?,?,?,0,NOW(),NOW()) ON DUPLICATE KEY UPDATE password=VALUES(password), is_verified=1, is_admin=1');
            $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT), 1, 1]);
            
            App::json(['ok' => true, 'message' => 'Admin user created successfully']);
            
        } catch (\Exception $e) {
            App::json(['error' => 'Failed to create admin: ' . $e->getMessage()], 500);
        }
    }
    
    private function loadEnvManually(string $envPath): void
    {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip comments and empty lines
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }

            // Parse key=value pairs
            if (strpos($line, '=') !== false) {
                $parts = explode('=', $line, 2);
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                
                // Remove quotes if present
                if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                    (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
                    $value = substr($value, 1, -1);
                }
                
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}
