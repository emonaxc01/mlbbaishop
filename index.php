<?php
declare(strict_types=1);

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

// Set error handler to catch all errors
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Set exception handler
set_exception_handler(function($exception) {
    $debug = $_ENV['APP_DEBUG'] ?? 'false';
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    
    // For installer routes, always show detailed errors
    if (str_starts_with($path, '/install') || str_starts_with($path, '/insall')) {
        echo '<!doctype html><html><head><meta charset="utf-8"><title>Installer Error</title><meta name="viewport" content="width=device-width, initial-scale=1"><style>body{font-family:sans-serif;background:#f9fafb;color:#111827;padding:20px} .error{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:32px;box-shadow:0 10px 25px rgba(0,0,0,.05);max-width:800px;margin:0 auto} .error h1{color:#dc2626;margin:0 0 16px} .error pre{background:#f3f4f6;padding:16px;border-radius:8px;overflow-x:auto;font-size:14px}</style></head><body><div class="error"><h1>Installer Error</h1><p><strong>Error:</strong> ' . htmlspecialchars($exception->getMessage()) . '</p><p><strong>File:</strong> ' . htmlspecialchars($exception->getFile()) . ':' . $exception->getLine() . '</p><pre>' . htmlspecialchars($exception->getTraceAsString()) . '</pre><p><a href="/debug.php">View Debug Information</a></p></div></body></html>';
    } else {
        if ($debug === 'true') {
            echo '<h1>Error: ' . $exception->getMessage() . '</h1>';
            echo '<h2>File: ' . $exception->getFile() . ':' . $exception->getLine() . '</h2>';
            echo '<pre>' . $exception->getTraceAsString() . '</pre>';
        } else {
            http_response_code(500);
            echo '<h1>Internal Server Error</h1>';
            echo '<p>Something went wrong. Please try again later.</p>';
        }
    }
    exit;
});

// Register autoloader with correct namespace mapping
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
    
    // Debug: Log the class and file path (remove in production)
    if (strpos($class, 'App\\') === 0) {
        error_log("Autoloader: Loading class '$class' from file '$file'");
    }
    
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    
    return false;
});

use App\Core\App;
use App\Core\Router;

// Bootstrap application
$app = new App(__DIR__);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define routes
Router::get('/', ['\App\Http\Controllers\PageController', 'home']);
Router::get('/game-topup', ['\App\Http\Controllers\PageController', 'gameTopUp']);

// Auth
Router::post('/api/auth/register', ['\App\Http\Controllers\AuthController', 'register']);
Router::post('/api/auth/verify-otp', ['\App\Http\Controllers\AuthController', 'verifyOtp']);
Router::post('/api/auth/login', ['\App\Http\Controllers\AuthController', 'login']);
Router::post('/api/auth/logout', ['\App\Http\Controllers\AuthController', 'logout']);
Router::get('/api/auth/me', ['\App\Http\Controllers\AuthController', 'me']);

// Orders
Router::post('/api/orders', ['\App\Http\Controllers\OrderController', 'create']);
Router::get('/api/orders', ['\App\Http\Controllers\OrderController', 'list']);
Router::post('/api/checkout/orders', ['\App\Http\Controllers\CheckoutController', 'createOrder']);

// Game Top-Up APIs
Router::get('/api/games', ['\App\Http\Controllers\GameTopUpController', 'getGames']);
Router::get('/api/games/{slug}', ['\App\Http\Controllers\GameTopUpController', 'getGame']);
Router::get('/api/games/{slug}/packages', ['\App\Http\Controllers\GameTopUpController', 'getGamePackages']);
Router::get('/api/currencies', ['\App\Http\Controllers\GameTopUpController', 'getCurrencies']);
Router::get('/api/exchange-rates', ['\App\Http\Controllers\GameTopUpController', 'getExchangeRates']);
Router::post('/api/convert-price', ['\App\Http\Controllers\GameTopUpController', 'convertPrice']);
Router::post('/api/orders/create', ['\App\Http\Controllers\GameTopUpController', 'createOrder']);
Router::get('/api/orders/{identifier}', ['\App\Http\Controllers\GameTopUpController', 'getOrder']);
Router::post('/api/orders/{userId}/user', ['\App\Http\Controllers\GameTopUpController', 'getUserOrders']);
Router::put('/api/orders/{orderId}/status', ['\App\Http\Controllers\GameTopUpController', 'updateOrderStatus']);
Router::post('/api/orders/{orderId}/notes', ['\App\Http\Controllers\GameTopUpController', 'addOrderNote']);
Router::get('/api/orders/stats', ['\App\Http\Controllers\GameTopUpController', 'getOrderStats']);
Router::post('/api/orders/search', ['\App\Http\Controllers\GameTopUpController', 'searchOrders']);
Router::get('/api/orders/recent', ['\App\Http\Controllers\GameTopUpController', 'getRecentOrders']);

// Admin pages
Router::get('/admin', ['\App\Http\Controllers\AdminController', 'dashboard']);
Router::get('/admin/products', ['\App\Http\Controllers\AdminController', 'productsPage']);
Router::get('/admin/catalog', ['\App\Http\Controllers\AdminCatalogController', 'productsPage']);
Router::get('/admin/catalog/product', ['\App\Http\Controllers\AdminCatalogController', 'productDetailPage']);
Router::get('/admin/orders', ['\App\Http\Controllers\AdminController', 'ordersPage']);
Router::get('/admin/users', ['\App\Http\Controllers\AdminController', 'usersPage']);
Router::get('/admin/settings', ['\App\Http\Controllers\AdminController', 'settingsPage']);

// Admin APIs
Router::get('/api/admin/products', ['\App\Http\Controllers\AdminController', 'listProducts']);
Router::post('/api/admin/products', ['\App\Http\Controllers\AdminController', 'upsertProduct']);
Router::get('/api/admin/packages', ['\App\Http\Controllers\AdminController', 'listPackages']);
Router::post('/api/admin/packages', ['\App\Http\Controllers\AdminController', 'upsertPackage']);
Router::get('/api/admin/orders', ['\App\Http\Controllers\AdminController', 'listAllOrders']);
Router::post('/api/admin/orders/note', ['\App\Http\Controllers\AdminController', 'addOrderNote']);
Router::get('/api/admin/export/users', ['\App\Http\Controllers\AdminController', 'exportUsers']);
Router::get('/api/admin/export/orders', ['\App\Http\Controllers\AdminController', 'exportOrders']);
Router::post('/api/admin/import/users', ['\App\Http\Controllers\AdminController', 'importUsers']);
Router::post('/api/admin/import/orders', ['\App\Http\Controllers\AdminController', 'importOrders']);
Router::get('/api/catalog', ['\App\Http\Controllers\CatalogController', 'list']);
Router::get('/api/catalog/detail', ['\App\Http\Controllers\CatalogController', 'detail']);

// Admin catalog APIs
Router::get('/api/admin/catalog/products', ['\App\Http\Controllers\AdminCatalogController', 'listProducts']);
Router::post('/api/admin/catalog/product', ['\App\Http\Controllers\AdminCatalogController', 'saveProduct']);
Router::get('/api/admin/catalog/product/delete', ['\App\Http\Controllers\AdminCatalogController', 'deleteProduct']);
Router::get('/api/admin/catalog/variations', ['\App\Http\Controllers\AdminCatalogController', 'listVariations']);
Router::post('/api/admin/catalog/variation', ['\App\Http\Controllers\AdminCatalogController', 'saveVariation']);
Router::get('/api/admin/catalog/variation/delete', ['\App\Http\Controllers\AdminCatalogController', 'deleteVariation']);
Router::get('/api/admin/catalog/product/meta', ['\App\Http\Controllers\AdminCatalogController', 'listProductMeta']);
Router::post('/api/admin/catalog/product/meta', ['\App\Http\Controllers\AdminCatalogController', 'saveProductMeta']);
Router::get('/api/admin/catalog/product/meta/delete', ['\App\Http\Controllers\AdminCatalogController', 'deleteProductMeta']);
Router::get('/api/admin/catalog/variation/meta', ['\App\Http\Controllers\AdminCatalogController', 'listVariationMeta']);
Router::post('/api/admin/catalog/variation/meta', ['\App\Http\Controllers\AdminCatalogController', 'saveVariationMeta']);
Router::get('/api/admin/catalog/variation/meta/delete', ['\App\Http\Controllers\AdminCatalogController', 'deleteVariationMeta']);
Router::get('/api/admin/users', ['\App\Http\Controllers\AdminController', 'listUsers']);
Router::post('/api/admin/users', ['\App\Http\Controllers\AdminController', 'updateUser']);
Router::get('/api/admin/settings', ['\App\Http\Controllers\AdminController', 'getSettings']);
Router::post('/api/admin/settings', ['\App\Http\Controllers\AdminController', 'saveSettings']);
Router::get('/api/admin/currencies', ['\App\Http\Controllers\AdminController', 'listCurrencies']);
Router::post('/api/admin/currencies', ['\App\Http\Controllers\AdminController', 'upsertCurrency']);
Router::get('/api/admin/payments', ['\App\Http\Controllers\AdminController', 'listPaymentMethods']);
Router::post('/api/admin/payments', ['\App\Http\Controllers\AdminController', 'upsertPaymentMethod']);
Router::post('/api/admin/upload-logo', ['\App\Http\Controllers\AdminController', 'uploadLogo']);

// Ping
Router::get('/api/health', function() {
    App::json(['status' => 'ok']);
});

// Installer routes (aliases /install and /insall)
Router::get('/install', ['\App\Http\Controllers\InstallerController', 'form']);
Router::get('/insall', ['\App\Http\Controllers\InstallerController', 'form']);
Router::get('/install/check', ['\App\Http\Controllers\InstallerController', 'check']);
Router::post('/install/save-env', ['\App\Http\Controllers\InstallerController', 'saveEnv']);
Router::get('/install/migrate', ['\App\Http\Controllers\InstallerController', 'runMigrations']);
Router::post('/install/create-admin', ['\App\Http\Controllers\InstallerController', 'createAdmin']);

// Maintenance mode guard for public site (skip for installer)
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$isInstallerPath = str_starts_with($path, '/install') || str_starts_with($path, '/insall');
$isAdminPath = str_starts_with($path, '/admin') || str_starts_with($path, '/api/admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && !$isInstallerPath) {
    if (!$isAdminPath) {
        try {
            $maintenance = \App\Support\Settings::get('maintenance_mode', 'off');
            if ($maintenance === 'on') {
                http_response_code(503);
                echo '<!doctype html><html><head><meta charset="utf-8"><title>Maintenance</title><meta name="viewport" content="width=device-width, initial-scale=1"><style>body{font-family:sans-serif;background:#f9fafb;color:#111827;display:flex;align-items:center;justify-content:center;height:100vh} .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:32px;box-shadow:0 10px 25px rgba(0,0,0,.05)}</style></head><body><div class="card"><h1 style="font-size:20px;margin:0 0 8px">We\'ll be back soon</h1><p>Site is under maintenance. Please check again later.</p></div></body></html>';
                exit;
            }
        } catch (Exception $e) {
            // If settings table doesn't exist yet, continue normally
        }
    }
}

// Dispatch
Router::dispatch();
