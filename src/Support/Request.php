<?php
namespace App\Support;

class Request
{
    public static function json(): array
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw ?: '[]', true);
        if (!is_array($data)) return [];
        return $data;
    }
}
