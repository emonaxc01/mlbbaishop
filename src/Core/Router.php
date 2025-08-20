<?php
namespace App\Core;

class Router
{
    private static array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
        'PATCH' => []
    ];

    public static function get(string $path, $handler): void
    {
        self::$routes['GET'][self::normalize($path)] = $handler;
    }

    public static function post(string $path, $handler): void
    {
        self::$routes['POST'][self::normalize($path)] = $handler;
    }

    public static function put(string $path, $handler): void
    {
        self::$routes['PUT'][self::normalize($path)] = $handler;
    }

    public static function delete(string $path, $handler): void
    {
        self::$routes['DELETE'][self::normalize($path)] = $handler;
    }

    public static function patch(string $path, $handler): void
    {
        self::$routes['PATCH'][self::normalize($path)] = $handler;
    }

    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $path = self::normalize($uri);

        // Check for exact match first
        $handler = self::$routes[$method][$path] ?? null;
        
        // If no exact match, try to find a dynamic route
        if ($handler === null) {
            $handler = self::findDynamicRoute($method, $path);
        }

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
            
            // Debug: Log the class name
            error_log("Router: Trying to instantiate class: '$class'");
            
            // Use the class name as-is, it should already be fully qualified
            $instance = new $class();
            $instance->$methodName();
            return;
        }

        http_response_code(500);
        echo 'Invalid route handler';
    }

    private static function findDynamicRoute(string $method, string $path): ?array
    {
        foreach (self::$routes[$method] as $route => $handler) {
            if (self::matchDynamicRoute($route, $path)) {
                return $handler;
            }
        }
        return null;
    }

    private static function matchDynamicRoute(string $route, string $path): bool
    {
        // Convert route parameters like {slug} to regex pattern
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $path);
    }

    private static function normalize(string $path): string
    {
        if ($path === '') return '/';
        if ($path[0] !== '/') $path = '/' . $path;
        return rtrim($path, '/') ?: '/';
    }
}
