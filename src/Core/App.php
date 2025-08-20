<?php
namespace App\Core;

class App
{
    public static string $basePath;

    public function __construct(string $basePath)
    {
        self::$basePath = rtrim($basePath, '/');

        // Load .env file manually if dotenv is not available
        $this->loadEnv();

        date_default_timezone_set('UTC');

        // Ensure response headers
        header_remove('X-Powered-By');
    }

    private function loadEnv(): void
    {
        $envPath = self::$basePath . '/.env';
        
        if (!file_exists($envPath)) {
            return; // .env file doesn't exist, continue without it
        }

        // Try to load .env if dotenv is available
        if (class_exists('\Dotenv\Dotenv')) {
            try {
                $dotenv = \Dotenv\Dotenv::createImmutable(self::$basePath);
                $dotenv->safeLoad();
            } catch (\Exception $e) {
                // Fall back to manual loading
                $this->loadEnvManually($envPath);
            }
        } else {
            // Load .env manually
            $this->loadEnvManually($envPath);
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

    public static function view(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewPath = self::$basePath . '/views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo 'View not found: ' . $viewPath;
            return;
        }
        require $viewPath;
    }

    public static function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload);
    }
}
