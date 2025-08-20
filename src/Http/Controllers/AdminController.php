<?php
namespace App\Http\Controllers;

use App\Core\App;
use App\Core\DB;
use App\Support\Request;
use PDO;

class AdminController
{
    private function requireAdmin(): int
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = (int)($_SESSION['user_id'] ?? 0);
        if (!$userId) {
            http_response_code(302);
            header('Location: /');
            exit;
        }
        $pdo = DB::conn();
        $isAdmin = (int)$pdo->query('SELECT is_admin FROM users WHERE id = ' . $userId)->fetchColumn();
        if ($isAdmin !== 1) {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }
        return $userId;
    }

    public function dashboard(): void
    {
        $this->requireAdmin();
        App::view('admin/dashboard');
    }

    public function productsPage(): void
    {
        $this->requireAdmin();
        App::view('admin/products');
    }

    public function listProducts(): void
    {
        $this->requireAdmin();
        $pdo = DB::conn();
        $rows = $pdo->query('SELECT id, code, name, status, created_at FROM products ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
        App::json(['products' => $rows]);
    }

    public function upsertProduct(): void
    {
        $this->requireAdmin();
        $data = Request::json();
        $id = (int)($data['id'] ?? 0);
        $code = substr(trim((string)($data['code'] ?? '')), 0, 64);
        $name = substr(trim((string)($data['name'] ?? '')), 0, 191);
        $status = (int)($data['status'] ?? 1);
        if ($code === '' || $name === '') return App::json(['error' => 'Invalid'], 422);
        $pdo = DB::conn();
        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE products SET code=?, name=?, status=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$code, $name, $status, $id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO products (code, name, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
            $stmt->execute([$code, $name, $status]);
            $id = (int)$pdo->lastInsertId();
        }
        App::json(['id' => $id]);
    }

    public function listPackages(): void
    {
        $this->requireAdmin();
        $productId = (int)($_GET['productId'] ?? 0);
        $pdo = DB::conn();
        $stmt = $pdo->prepare('SELECT id, code, label, diamonds, price, created_at FROM packages WHERE product_id = ? ORDER BY id DESC');
        $stmt->execute([$productId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        App::json(['packages' => $rows]);
    }

    public function upsertPackage(): void
    {
        $this->requireAdmin();
        $data = Request::json();
        $id = (int)($data['id'] ?? 0);
        $productId = (int)($data['productId'] ?? 0);
        $code = substr(trim((string)($data['code'] ?? '')), 0, 64);
        $label = substr(trim((string)($data['label'] ?? '')), 0, 191);
        $diamonds = (int)($data['diamonds'] ?? 0);
        $price = (float)($data['price'] ?? 0);
        if ($productId <= 0 || $code === '' || $label === '' || $diamonds <= 0 || $price <= 0) return App::json(['error' => 'Invalid'], 422);
        $pdo = DB::conn();
        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE packages SET code=?, label=?, diamonds=?, price=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$code, $label, $diamonds, $price, $id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO packages (product_id, code, label, diamonds, price, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
            $stmt->execute([$productId, $code, $label, $diamonds, $price]);
            $id = (int)$pdo->lastInsertId();
        }
        App::json(['id' => $id]);
    }

    public function ordersPage(): void
    {
        $this->requireAdmin();
        App::view('admin/orders');
    }

    public function listAllOrders(): void
    {
        $this->requireAdmin();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = min(200, max(1, (int)($_GET['limit'] ?? 50)));
        $offset = ($page - 1) * $limit;
        $pdo = DB::conn();
        $stmt = $pdo->prepare('SELECT o.id, u.email, o.game, o.product_id, o.variation_code, o.player_id, o.amount, o.currency, o.payment_method, o.status, o.created_at FROM orders o JOIN users u ON u.id = o.user_id ORDER BY o.id DESC LIMIT ? OFFSET ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        App::json(['orders' => $rows, 'page' => $page, 'limit' => $limit]);
    }
}
