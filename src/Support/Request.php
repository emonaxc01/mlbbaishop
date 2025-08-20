<?php
namespace App\Support;

class Request
{
    public static function json(): array
    {
        $input = file_get_contents('php://input');
        if (empty($input)) {
            return [];
        }
        
        $data = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }
        
        return $data ?: [];
    }

    public static function get(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    public static function post(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    public static function input(string $key, $default = null)
    {
        return $_REQUEST[$key] ?? $default;
    }

    public static function all(): array
    {
        return $_REQUEST;
    }

    public static function has(string $key): bool
    {
        return isset($_REQUEST[$key]);
    }
}
