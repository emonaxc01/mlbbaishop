<?php
// Test Router class instantiation
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h1>Router Test</h1>";

// Register autoloader
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
    
    echo "Loading class: $class from file: $file<br>";
    
    if (file_exists($file)) {
        require_once $file;
        echo "✅ Loaded: $class<br>";
        return true;
    }
    
    echo "❌ Failed to load: $class<br>";
    return false;
});

// Test 1: Load Router class
echo "<h2>Test 1: Loading Router</h2>";
try {
    $router = new App\Core\Router();
    echo "✅ Router instantiated successfully<br>";
} catch (Exception $e) {
    echo "❌ Router error: " . $e->getMessage() . "<br>";
}

// Test 2: Load InstallerController class
echo "<h2>Test 2: Loading InstallerController</h2>";
try {
    $controller = new App\Http\Controllers\InstallerController();
    echo "✅ InstallerController instantiated successfully<br>";
} catch (Exception $e) {
    echo "❌ InstallerController error: " . $e->getMessage() . "<br>";
}

// Test 3: Test class name resolution
echo "<h2>Test 3: Class Name Resolution</h2>";
$className = App\Http\Controllers\InstallerController::class;
echo "Class name from ::class: $className<br>";

try {
    $instance = new $className();
    echo "✅ Successfully instantiated using ::class<br>";
} catch (Exception $e) {
    echo "❌ Failed to instantiate using ::class: " . $e->getMessage() . "<br>";
}

echo "<h2>Test Complete</h2>";
?>