<?php
namespace App\Support;

class EnvWriter
{
    public static function write(array $vars, string $envPath): bool
    {
        try {
            $content = '';
            
            // Read existing .env if it exists
            if (file_exists($envPath)) {
                $content = file_get_contents($envPath);
                if ($content === false) {
                    return false;
                }
            }
            
            // Parse existing variables
            $existing = [];
            $lines = explode("\n", $content);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, '#') === 0) {
                    continue;
                }
                if (strpos($line, '=') !== false) {
                    $parts = explode('=', $line, 2);
                    $existing[trim($parts[0])] = trim($parts[1]);
                }
            }
            
            // Merge new variables
            $allVars = array_merge($existing, $vars);
            
            // Build new content
            $newContent = '';
            foreach ($allVars as $key => $value) {
                $newContent .= "{$key}={$value}\n";
            }
            
            // Write to file
            $result = file_put_contents($envPath, $newContent);
            return $result !== false;
            
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public static function update(string $key, string $value, string $envPath): bool
    {
        return self::write([$key => $value], $envPath);
    }
}
