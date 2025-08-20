<?php
namespace App\Http\Controllers;

use App\Core\App;
use App\Core\DB;
use App\Support\Request;
use PDO;

class OrderController
{
    private function requireAuth(): ?int
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    public function create(): void
    {
        $userId = $this->requireAuth();
        if (!$userId) {
            return App::json(['error' => 'Unauthorized'], 401);
        }

        $data = Request::json();
        $game = substr(trim((string)($data['game'] ?? '')), 0, 32);
        $productId = substr(trim((string)($data['productId'] ?? '')), 0, 64);
        $variationCode = substr(trim((string)($data['variationCode'] ?? '')), 0, 64);
        $playerId = substr(trim((string)($data['playerId'] ?? '')), 0, 32);
        $amount = (float)($data['amount'] ?? 0);
        $currency = substr(trim((string)($data['currency'] ?? 'BDT')), 0, 3);
        $paymentMethod = substr(trim((string)($data['paymentMethod'] ?? '')), 0, 32);

        if ($game === '' || $productId === '' || $variationCode === '' || $playerId === '' || $amount <= 0 || $paymentMethod === '') {
            return App::json(['error' => 'Invalid order data'], 422);
        }

        $pdo = DB::conn();
        $stmt = $pdo->prepare('INSERT INTO orders (user_id, game, product_id, variation_code, player_id, amount, currency, payment_method, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, "paid", NOW(), NOW())');
        $stmt->execute([$userId, $game, $productId, $variationCode, $playerId, $amount, $currency, $paymentMethod]);
        $id = (int)$pdo->lastInsertId();

        return App::json(['id' => $id, 'status' => 'paid']);
    }

    public function list(): void
    {
        $userId = $this->requireAuth();
        if (!$userId) {
            return App::json(['error' => 'Unauthorized'], 401);
        }

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = min(100, max(1, (int)($_GET['limit'] ?? 20)));
        $offset = ($page - 1) * $limit;

        $pdo = DB::conn();
        $stmt = $pdo->prepare('SELECT id, game, product_id, variation_code, player_id, amount, currency, payment_method, status, created_at FROM orders WHERE user_id = ? ORDER BY id DESC LIMIT ? OFFSET ?');
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return App::json(['page' => $page, 'limit' => $limit, 'orders' => $orders]);
    }
}
