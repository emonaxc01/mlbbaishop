<?php
// Debug file to identify issues
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h1>Debug Information</h1>";

// Check PHP version
echo "<h2>PHP Version</h2>";
echo "PHP Version: " . PHP_VERSION . "<br>";

// Check required extensions
echo "<h2>Required Extensions</h2>";
$required_extensions = ['pdo_mysql', 'mbstring', 'curl', 'openssl', 'json'];
foreach ($required_extensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "$ext: " . ($loaded ? "✅ Loaded" : "❌ Not loaded") . "<br>";
}

// Check file permissions
echo "<h2>File Permissions</h2>";
$base_path = __DIR__;
echo "Base path: $base_path<br>";
echo "Base path writable: " . (is_writable($base_path) ? "✅ Yes" : "❌ No") . "<br>";

$env_path = $base_path . '/.env';
echo ".env exists: " . (file_exists($env_path) ? "✅ Yes" : "❌ No") . "<br>";
if (file_exists($env_path)) {
    echo ".env readable: " . (is_readable($env_path) ? "✅ Yes" : "❌ No") . "<br>";
    echo ".env writable: " . (is_writable($env_path) ? "✅ Yes" : "❌ No") . "<br>";
}

// Check if .env file exists and show its contents
echo "<h2>.env File Contents</h2>";
if (file_exists($env_path)) {
    echo "<pre>" . htmlspecialchars(file_get_contents($env_path)) . "</pre>";
} else {
    echo "❌ .env file not found<br>";
}

// Check if database connection works
echo "<h2>Database Connection Test</h2>";
if (file_exists($env_path)) {
    // Load .env manually
    $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env_vars = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            $parts = explode('=', $line, 2);
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
                $value = substr($value, 1, -1);
            }
            $env_vars[$key] = $value;
        }
    }
    
    if (isset($env_vars['DB_HOST']) && isset($env_vars['DB_DATABASE']) && isset($env_vars['DB_USERNAME'])) {
        try {
            $dsn = "mysql:host={$env_vars['DB_HOST']};port={$env_vars['DB_PORT']};dbname={$env_vars['DB_DATABASE']};charset=utf8mb4";
            $pdo = new PDO($dsn, $env_vars['DB_USERNAME'], $env_vars['DB_PASSWORD'] ?? '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            echo "✅ Database connection successful<br>";
            
            // Check if tables exist
            $tables = ['users', 'products', 'orders', 'settings'];
            foreach ($tables as $table) {
                $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                $exists = $stmt->rowCount() > 0;
                echo "Table '$table': " . ($exists ? "✅ Exists" : "❌ Missing") . "<br>";
            }
        } catch (PDOException $e) {
            echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "❌ Database credentials not found in .env<br>";
    }
} else {
    echo "❌ Cannot test database connection - .env file not found<br>";
}

// Check if autoloader works
echo "<h2>Autoloader Test</h2>";
$autoloader_path = $base_path . '/src/Core/SimpleAutoloader.php';
echo "SimpleAutoloader exists: " . (file_exists($autoloader_path) ? "✅ Yes" : "❌ No") . "<br>";

if (file_exists($autoloader_path)) {
    try {
        require_once $autoloader_path;
        App\Core\SimpleAutoloader::register($base_path);
        echo "✅ SimpleAutoloader loaded successfully<br>";
        
        // Test loading a class
        if (class_exists('App\Core\App')) {
            echo "✅ App class loaded successfully<br>";
        } else {
            echo "❌ App class not found<br>";
        }
    } catch (Exception $e) {
        echo "❌ Autoloader error: " . $e->getMessage() . "<br>";
    }
}

// Check web server info
echo "<h2>Web Server Info</h2>";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "<br>";
echo "Script Name: " . ($_SERVER['SCRIPT_NAME'] ?? 'Unknown') . "<br>";

echo "<h2>End Debug</h2>";
?>