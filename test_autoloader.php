<?php
// Test autoloader functionality
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h1>Autoloader Test</h1>";

// Register autoloader with the same logic as index.php
spl_autoload_register(function($class) {
    $basePath = __DIR__;
    
    // Handle App namespace specifically
    if (strpos($class, 'App\\') === 0) {
        // Remove 'App\' prefix and convert to file path
        $relativePath = substr($class, 4); // Remove 'App\'
        $file = $basePath . '/src/' . str_replace('\\', '/', $relativePath) . '.php';
    } else {
        // For other namespaces, use standard mapping
        $file = $basePath . '/src/' . str_replace('\\', '/', $class) . '.php';
    }
    
    echo "Trying to load class: $class<br>";
    echo "Looking for file: $file<br>";
    echo "File exists: " . (file_exists($file) ? "YES" : "NO") . "<br><br>";
    
    if (file_exists($file)) {
        require_once $file;
        echo "✅ Successfully loaded: $class<br><br>";
        return true;
    }
    
    echo "❌ Failed to load: $class<br><br>";
    return false;
});

// Test loading App class
echo "<h2>Testing App\Core\App</h2>";
try {
    $app = new App\Core\App(__DIR__);
    echo "✅ App class loaded and instantiated successfully!<br>";
    echo "Base path: " . App\Core\App::$basePath . "<br>";
} catch (Exception $e) {
    echo "❌ Error loading App class: " . $e->getMessage() . "<br>";
}

// Test loading Router class
echo "<h2>Testing App\Core\Router</h2>";
try {
    if (class_exists('App\Core\Router')) {
        echo "✅ Router class loaded successfully!<br>";
    } else {
        echo "❌ Router class not found<br>";
    }
} catch (Exception $e) {
    echo "❌ Error loading Router class: " . $e->getMessage() . "<br>";
}

// Test loading DB class
echo "<h2>Testing App\Core\DB</h2>";
try {
    if (class_exists('App\Core\DB')) {
        echo "✅ DB class loaded successfully!<br>";
    } else {
        echo "❌ DB class not found<br>";
    }
} catch (Exception $e) {
    echo "❌ Error loading DB class: " . $e->getMessage() . "<br>";
}

// Test loading a controller class
echo "<h2>Testing App\Http\Controllers\InstallerController</h2>";
try {
    if (class_exists('App\Http\Controllers\InstallerController')) {
        echo "✅ InstallerController class loaded successfully!<br>";
    } else {
        echo "❌ InstallerController class not found<br>";
    }
} catch (Exception $e) {
    echo "❌ Error loading InstallerController class: " . $e->getMessage() . "<br>";
}

echo "<h2>Test Complete</h2>";
?>