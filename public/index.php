<?php
declare(strict_types=1);

use App\Core\App;
use App\Core\Router;

require __DIR__ . '/../vendor/autoload.php';

// Bootstrap application
$app = new App(__DIR__ . '/..');
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

// Admin pages
Router::get('/admin', [App\Http\Controllers\AdminController::class, 'dashboard']);
Router::get('/admin/products', [App\Http\Controllers\AdminController::class, 'productsPage']);
Router::get('/admin/orders', [App\Http\Controllers\AdminController::class, 'ordersPage']);

// Admin APIs
Router::get('/api/admin/products', [App\Http\Controllers\AdminController::class, 'listProducts']);
Router::post('/api/admin/products', [App\Http\Controllers\AdminController::class, 'upsertProduct']);
Router::get('/api/admin/packages', [App\Http\Controllers\AdminController::class, 'listPackages']);
Router::post('/api/admin/packages', [App\Http\Controllers\AdminController::class, 'upsertPackage']);
Router::get('/api/admin/orders', [App\Http\Controllers\AdminController::class, 'listAllOrders']);

// Ping
Router::get('/api/health', function() {
    App::json(['status' => 'ok']);
});

// Dispatch
Router::dispatch();