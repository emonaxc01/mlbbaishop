<?php
namespace App\Core;

class SimpleAutoloader
{
    private static $basePath;
    
    public static function register(string $basePath): void
    {
        self::$basePath = $basePath;
        spl_autoload_register([self::class, 'loadClass']);
    }
    
    public static function loadClass(string $class): void
    {
        // Convert namespace to file path
        $file = self::$basePath . '/src/' . str_replace('\\', '/', $class) . '.php';
        
        if (file_exists($file)) {
            require_once $file;
        }
    }
}