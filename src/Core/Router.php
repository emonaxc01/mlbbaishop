<?php
namespace App\Core;

class Router
{
    private static array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public static function get(string $path, $handler): void
    {
        self::$routes['GET'][self::normalize($path)] = $handler;
    }

    public static function post(string $path, $handler): void
    {
        self::$routes['POST'][self::normalize($path)] = $handler;
    }

    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $path = self::normalize($uri);

        $handler = self::$routes[$method][$path] ?? null;
        if ($handler === null) {
            http_response_code(404);
            echo 'Not Found';
            return;
        }

        if (is_callable($handler)) {
            echo $handler();
            return;
        }

        if (is_array($handler) && count($handler) === 2) {
            [$class, $methodName] = $handler;
            $instance = new $class();
            return $instance->$methodName();
        }

        http_response_code(500);
        echo 'Invalid route handler';
    }

    private static function normalize(string $path): string
    {
        if ($path === '') return '/';
        if ($path[0] !== '/') $path = '/' . $path;
        return rtrim($path, '/') ?: '/';
    }
}
