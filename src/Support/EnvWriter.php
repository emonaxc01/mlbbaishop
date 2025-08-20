<?php
namespace App\Support;

class EnvWriter
{
    public static function write(array $vars, string $path): bool
    {
        $lines = [];
        foreach ($vars as $k => $v) {
            $v = str_replace(["\r","\n"], '', (string)$v);
            if (preg_match('/\s/', $v)) {
                $v = '"' . addslashes($v) . '"';
            }
            $lines[] = $k . '=' . $v;
        }
        $content = implode("\n", $lines) . "\n";
        return (bool)@file_put_contents($path, $content);
    }
}
