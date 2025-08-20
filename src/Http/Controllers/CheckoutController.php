<?php
namespace App\Http\Controllers;

use App\Core\App;
use App\Core\DB;
use App\Support\Request;
use PDO;

interface PaymentDriver {
    public function authorize(array $order): array; // returns ['status'=>'authorized'|'paid'|'failed', 'meta'=>[]]
}

class TestPayDriver implements PaymentDriver
{
    public function authorize(array $order): array
    {
        return ['status' => 'paid', 'meta' => ['driver' => 'testpay']];
    }
}

class CheckoutController
{
    private function requireAuth(): int
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return (int)($_SESSION['user_id'] ?? 0);
    }

    private function getDriver(string $code): PaymentDriver
    {
        return new TestPayDriver();
    }

    public function createOrder(): void
    {
        $uid = $this->requireAuth();
        if (!$uid) return App::json(['error'=>'Unauthorized'],401);

        $data = Request::json();
        $items = $data['items'] ?? [];
        $method = (string)($data['paymentMethod'] ?? 'testpay');
        if (!is_array($items) || count($items) === 0) return App::json(['error'=>'No items'],422);

        $pdo = DB::conn();
        $pdo->beginTransaction();
        try {
            // Calculate totals
            $subtotal = 0;
            $lineItems = [];
            foreach ($items as $it) {
                $productId = (int)($it['productId'] ?? 0);
                $variationId = (int)($it['variationId'] ?? 0);
                $qty = max(1,(int)($it['quantity'] ?? 1));
                if ($productId <= 0) throw new \RuntimeException('Invalid item');
                $row = null;
                if ($variationId > 0) {
                    $stmt = $pdo->prepare('SELECT p.name AS pname, pv.name AS vname, pv.sku, pv.price, pv.stock FROM product_variations pv JOIN products p ON p.id = pv.product_id WHERE pv.id = ?');
                    $stmt->execute([$variationId]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                } else {
                    $stmt = $pdo->prepare('SELECT name AS pname, sku, price, stock FROM products WHERE id = ?');
                    $stmt->execute([$productId]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                }
                if (!$row) throw new \RuntimeException('Item not found');
                $name = $variationId>0 ? ($row['pname'].' - '.$row['vname']) : $row['pname'];
                $price = (float)$row['price'];
                $lineTotal = $price * $qty;
                $subtotal += $lineTotal;
                $lineItems[] = [
                    'product_id'=>$productId,
                    'variation_id'=>$variationId?:null,
                    'name'=>$name,
                    'sku'=>$row['sku'] ?? null,
                    'price'=>$price,
                    'quantity'=>$qty,
                    'total'=>$lineTotal
                ];
            }
            $total = $subtotal; // taxes/discounts could be added later

            $stmt = $pdo->prepare('INSERT INTO orders (user_id, game, product_id, variation_code, player_id, amount, currency, payment_method, status, subtotal, total, payment_status, created_at, updated_at) VALUES (?, "", "", "", "", ?, "BDT", ?, "created", ?, ?, "unpaid", NOW(), NOW())');
            $stmt->execute([$uid, $total, $method, $subtotal, $total]);
            $orderId = (int)$pdo->lastInsertId();

            foreach ($lineItems as $li) {
                $s = $pdo->prepare('INSERT INTO order_items (order_id, product_id, variation_id, name, sku, price, quantity, total, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,NOW(),NOW())');
                $s->execute([$orderId, $li['product_id'], $li['variation_id'], $li['name'], $li['sku'], $li['price'], $li['quantity'], $li['total']]);
            }

            // invoke payment driver
            $driver = $this->getDriver($method);
            $result = $driver->authorize(['id'=>$orderId,'total'=>$total,'user_id'=>$uid]);
            $status = $result['status'] ?? 'failed';
            $meta = json_encode($result['meta'] ?? []);
            $pdo->prepare('INSERT INTO payments (order_id, method_code, amount, status, meta, created_at, updated_at) VALUES (?,?,?,?,?,NOW(),NOW())')->execute([$orderId,$method,$total,$status,$meta]);
            $pdo->prepare('UPDATE orders SET status = ?, payment_status = ?, updated_at = NOW() WHERE id = ?')->execute([$status==='paid'?'paid':'pending',$status,$orderId]);

            $pdo->commit();
            App::json(['orderId'=>$orderId,'status'=>$status]);
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            App::json(['error'=>'Checkout failed'],500);
        }
    }
}
