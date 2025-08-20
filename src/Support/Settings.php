<?php
namespace App\Support;

use App\Core\DB;

class Settings
{
    private static array $cache = [];

    public static function get(string $key, ?string $default = null): ?string
    {
        if (isset(self::$cache[$key])) return self::$cache[$key];
        $pdo = DB::conn();
        $stmt = $pdo->prepare('SELECT `value` FROM settings WHERE `key` = ?');
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        if ($val === false) return $default;
        self::$cache[$key] = (string)$val;
        return (string)$val;
    }

    public static function set(string $key, string $value): void
    {
        $pdo = DB::conn();
        $stmt = $pdo->prepare('INSERT INTO settings (`key`, `value`, created_at, updated_at) VALUES (?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), updated_at = NOW()');
        $stmt->execute([$key, $value]);
        self::$cache[$key] = $value;
    }
}
