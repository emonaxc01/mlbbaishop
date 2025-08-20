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
    
    // Convert namespace to file path
    $file = $basePath . '/src/' . str_replace('\\', '/', $class) . '.php';
    
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
Router::get('/', [App\Http\Controllers\PageController::class, 'home']);

// Auth
Router::post('/api/auth/register', [App\Http\Controllers\AuthController::class, 'register']);
Router::post('/api/auth/verify-otp', [App\Http\Controllers\AuthController::class, 'verifyOtp']);
Router::post('/api/auth/login', [App\Http\Controllers\AuthController::class, 'login']);
Router::post('/api/auth/logout', [App\Http\Controllers\AuthController::class, 'logout']);
Router::get('/api/auth/me', [App\Http\Controllers\AuthController::class, 'me']);

// Orders
Router::post('/api/orders', [App\Http\Controllers\OrderController::class, 'create']);
Router::get('/api/orders', [App\Http\Controllers\OrderController::class, 'list']);
Router::post('/api/checkout/orders', [App\Http\Controllers\CheckoutController::class, 'createOrder']);

// Admin pages
Router::get('/admin', [App\Http\Controllers\AdminController::class, 'dashboard']);
Router::get('/admin/products', [App\Http\Controllers\AdminController::class, 'productsPage']);
Router::get('/admin/catalog', [App\Http\Controllers\AdminCatalogController::class, 'productsPage']);
Router::get('/admin/catalog/product', [App\Http\Controllers\AdminCatalogController::class, 'productDetailPage']);
Router::get('/admin/orders', [App\Http\Controllers\AdminController::class, 'ordersPage']);
Router::get('/admin/users', [App\Http\Controllers\AdminController::class, 'usersPage']);
Router::get('/admin/settings', [App\Http\Controllers\AdminController::class, 'settingsPage']);

// Admin APIs
Router::get('/api/admin/products', [App\Http\Controllers\AdminController::class, 'listProducts']);
Router::post('/api/admin/products', [App\Http\Controllers\AdminController::class, 'upsertProduct']);
Router::get('/api/admin/packages', [App\Http\Controllers\AdminController::class, 'listPackages']);
Router::post('/api/admin/packages', [App\Http\Controllers\AdminController::class, 'upsertPackage']);
Router::get('/api/admin/orders', [App\Http\Controllers\AdminController::class, 'listAllOrders']);
Router::post('/api/admin/orders/note', [App\Http\Controllers\AdminController::class, 'addOrderNote']);
Router::get('/api/admin/export/users', [App\Http\Controllers\AdminController::class, 'exportUsers']);
Router::get('/api/admin/export/orders', [App\Http\Controllers\AdminController::class, 'exportOrders']);
Router::post('/api/admin/import/users', [App\Http\Controllers\AdminController::class, 'importUsers']);
Router::post('/api/admin/import/orders', [App\Http\Controllers\AdminController::class, 'importOrders']);
Router::get('/api/catalog', [App\Http\Controllers\CatalogController::class, 'list']);
Router::get('/api/catalog/detail', [App\Http\Controllers\CatalogController::class, 'detail']);

// Admin catalog APIs
Router::get('/api/admin/catalog/products', [App\Http\Controllers\AdminCatalogController::class, 'listProducts']);
Router::post('/api/admin/catalog/product', [App\Http\Controllers\AdminCatalogController::class, 'saveProduct']);
Router::get('/api/admin/catalog/product/delete', [App\Http\Controllers\AdminCatalogController::class, 'deleteProduct']);
Router::get('/api/admin/catalog/variations', [App\Http\Controllers\AdminCatalogController::class, 'listVariations']);
Router::post('/api/admin/catalog/variation', [App\Http\Controllers\AdminCatalogController::class, 'saveVariation']);
Router::get('/api/admin/catalog/variation/delete', [App\Http\Controllers\AdminCatalogController::class, 'deleteVariation']);
Router::get('/api/admin/catalog/product/meta', [App\Http\Controllers\AdminCatalogController::class, 'listProductMeta']);
Router::post('/api/admin/catalog/product/meta', [App\Http\Controllers\AdminCatalogController::class, 'saveProductMeta']);
Router::get('/api/admin/catalog/product/meta/delete', [App\Http\Controllers\AdminCatalogController::class, 'deleteProductMeta']);
Router::get('/api/admin/catalog/variation/meta', [App\Http\Controllers\AdminCatalogController::class, 'listVariationMeta']);
Router::post('/api/admin/catalog/variation/meta', [App\Http\Controllers\AdminCatalogController::class, 'saveVariationMeta']);
Router::get('/api/admin/catalog/variation/meta/delete', [App\Http\Controllers\AdminCatalogController::class, 'deleteVariationMeta']);
Router::get('/api/admin/users', [App\Http\Controllers\AdminController::class, 'listUsers']);
Router::post('/api/admin/users', [App\Http\Controllers\AdminController::class, 'updateUser']);
Router::get('/api/admin/settings', [App\Http\Controllers\AdminController::class, 'getSettings']);
Router::post('/api/admin/settings', [App\Http\Controllers\AdminController::class, 'saveSettings']);
Router::get('/api/admin/currencies', [App\Http\Controllers\AdminController::class, 'listCurrencies']);
Router::post('/api/admin/currencies', [App\Http\Controllers\AdminController::class, 'upsertCurrency']);
Router::get('/api/admin/payments', [App\Http\Controllers\AdminController::class, 'listPaymentMethods']);
Router::post('/api/admin/payments', [App\Http\Controllers\AdminController::class, 'upsertPaymentMethod']);
Router::post('/api/admin/upload-logo', [App\Http\Controllers\AdminController::class, 'uploadLogo']);

// Ping
Router::get('/api/health', function() {
    App::json(['status' => 'ok']);
});

// Installer routes (aliases /install and /insall)
Router::get('/install', [App\Http\Controllers\InstallerController::class, 'form']);
Router::get('/insall', [App\Http\Controllers\InstallerController::class, 'form']);
Router::get('/install/check', [App\Http\Controllers\InstallerController::class, 'check']);
Router::post('/install/save-env', [App\Http\Controllers\InstallerController::class, 'saveEnv']);
Router::get('/install/migrate', [App\Http\Controllers\InstallerController::class, 'runMigrations']);
Router::post('/install/create-admin', [App\Http\Controllers\InstallerController::class, 'createAdmin']);

// Maintenance mode guard for public site (skip for installer)
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$isInstallerPath = str_starts_with($path, '/install') || str_starts_with($path, '/insall');
$isAdminPath = str_starts_with($path, '/admin') || str_starts_with($path, '/api/admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && !$isInstallerPath) {
    if (!$isAdminPath) {
        try {
            $maintenance = App\Support\Settings::get('maintenance_mode', 'off');
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
