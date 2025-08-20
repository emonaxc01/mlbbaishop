<?php
namespace App\Core;

class App
{
    public static string $basePath;

    public function __construct(string $basePath)
    {
        self::$basePath = rtrim($basePath, '/');

        // Register simple autoloader
        SimpleAutoloader::register(self::$basePath);

        // Try to load .env if dotenv is available
        if (file_exists(self::$basePath . '/.env')) {
            if (class_exists('\Dotenv\Dotenv')) {
                $dotenv = \Dotenv\Dotenv::createImmutable(self::$basePath);
                $dotenv->safeLoad();
            }
        }

        date_default_timezone_set('UTC');

        // Ensure response headers
        header_remove('X-Powered-By');
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
