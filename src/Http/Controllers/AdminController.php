<?php
namespace App\Http\Controllers;

use App\Core\App;
use App\Core\DB;
use App\Support\Request;
use App\Support\Settings;
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
        if ($code === '' || $name === '') {
            App::json(['error' => 'Invalid'], 422);
            return;
        }
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
        if ($productId <= 0 || $code === '' || $label === '' || $diamonds <= 0 || $price <= 0) {
            App::json(['error' => 'Invalid'], 422);
            return;
        }
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

    // Order notes
    public function addOrderNote(): void
    {
        $this->requireAdmin();
        $d = Request::json();
        $orderId = (int)($d['order_id'] ?? 0);
        $note = trim((string)($d['note'] ?? ''));
        $emailUser = (int)($d['email_user'] ?? 0) === 1;
        if ($orderId<=0 || $note==='') {
            App::json(['error'=>'Invalid'],422);
            return;
        }
        $pdo = DB::conn();
        $pdo->prepare('INSERT INTO order_notes (order_id, note, emailed, created_at) VALUES (?,?,?,NOW())')->execute([$orderId, $note, $emailUser?1:0]);
        if ($emailUser) {
            $stmt = $pdo->prepare('SELECT u.email FROM orders o JOIN users u ON u.id = o.user_id WHERE o.id = ?');
            $stmt->execute([$orderId]);
            $to = (string)$stmt->fetchColumn();
            if ($to) { \App\Support\Mailer::send($to, 'Order Update #'.$orderId, '<p>'.$note.'</p>'); }
        }
        App::json(['ok'=>true]);
    }

    // CSV export/import (basic)
    public function exportUsers(): void
    {
        $this->requireAdmin();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=users.csv');
        $out = fopen('php://output','w');
        fputcsv($out, ['id','email','is_verified','is_admin','wallet_balance','created_at']);
        $rows = DB::conn()->query('SELECT id,email,is_verified,is_admin,wallet_balance,created_at FROM users ORDER BY id ASC');
        foreach ($rows as $r) { fputcsv($out, $r); }
        fclose($out);
    }

    public function exportOrders(): void
    {
        $this->requireAdmin();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=orders.csv');
        $out = fopen('php://output','w');
        fputcsv($out, ['id','user_email','payment_status','total','currency','payment_method','status','created_at']);
        $stmt = DB::conn()->query('SELECT o.id, u.email, o.payment_status, o.total, o.currency, o.payment_method, o.status, o.created_at FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id ASC');
        foreach ($stmt as $r) { fputcsv($out, $r); }
        fclose($out);
    }

    public function importUsers(): void
    {
        $this->requireAdmin();
        if (!isset($_FILES['file'])) { http_response_code(400); echo 'No file'; return; }
        $h = fopen($_FILES['file']['tmp_name'],'r');
        if (!$h) { http_response_code(400); echo 'Invalid file'; return; }
        $pdo = DB::conn();
        $header = fgetcsv($h);
        while(($row = fgetcsv($h)) !== false){
            $data = array_combine($header, $row);
            if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) continue;
            $stmt = $pdo->prepare('INSERT INTO users (email, password, is_verified, is_admin, wallet_balance, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE is_verified=VALUES(is_verified), is_admin=VALUES(is_admin), wallet_balance=VALUES(wallet_balance), updated_at=NOW()');
            $stmt->execute([$data['email'], password_hash('changeme', PASSWORD_DEFAULT), (int)($data['is_verified']??0), (int)($data['is_admin']??0), (float)($data['wallet_balance']??0)]);
        }
        fclose($h);
        App::json(['ok'=>true]);
    }

    public function importOrders(): void
    {
        $this->requireAdmin();
        if (!isset($_FILES['file'])) { http_response_code(400); echo 'No file'; return; }
        $h = fopen($_FILES['file']['tmp_name'],'r');
        if (!$h) { http_response_code(400); echo 'Invalid file'; return; }
        // Basic placeholder: real import mapping would be more complex
        fclose($h);
        App::json(['ok'=>true]);
    }

    // Users page and APIs
    public function usersPage(): void { $this->requireAdmin(); App::view('admin/users'); }
    public function listUsers(): void
    {
        $this->requireAdmin();
        $pdo = DB::conn();
        $rows = $pdo->query('SELECT id, email, is_verified, is_admin, wallet_balance, created_at FROM users ORDER BY id DESC LIMIT 500')->fetchAll(PDO::FETCH_ASSOC);
        App::json(['users' => $rows]);
    }
    public function updateUser(): void
    {
        $this->requireAdmin();
        $data = Request::json();
        $id = (int)($data['id'] ?? 0);
        $is_admin = (int)($data['is_admin'] ?? 0);
        $is_verified = (int)($data['is_verified'] ?? 0);
        $wallet = (float)($data['wallet_balance'] ?? 0);
        if ($id <= 0) {
            App::json(['error' => 'Invalid'], 422);
            return;
        }
        $pdo = DB::conn();
        $stmt = $pdo->prepare('UPDATE users SET is_admin=?, is_verified=?, wallet_balance=?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$is_admin, $is_verified, $wallet, $id]);
        App::json(['ok' => true]);
    }

    // Settings page and APIs (maintenance, SEO, logo, mail, currency)
    public function settingsPage(): void { $this->requireAdmin(); App::view('admin/settings'); }
    public function getSettings(): void
    {
        $this->requireAdmin();
        $keys = ['site_title','maintenance_mode','site_logo_url','mail_host','mail_port','mail_username','mail_password','mail_from_address','mail_from_name'];
        $out = [];
        foreach ($keys as $k) { $out[$k] = \App\Support\Settings::get($k, ''); }
        App::json($out);
    }
    public function saveSettings(): void
    {
        $this->requireAdmin();
        $data = Request::json();
        foreach ($data as $k=>$v) { \App\Support\Settings::set($k, (string)$v); }
        App::json(['ok'=>true]);
    }

    // Currency APIs
    public function listCurrencies(): void
    {
        $this->requireAdmin();
        $pdo = DB::conn();
        $rows = $pdo->query('SELECT id, code, symbol, rate, is_default, enabled FROM currencies ORDER BY is_default DESC, code ASC')->fetchAll(PDO::FETCH_ASSOC);
        App::json(['currencies'=>$rows]);
    }
    public function upsertCurrency(): void
    {
        $this->requireAdmin();
        $d = Request::json();
        $id = (int)($d['id'] ?? 0);
        $code = substr(trim((string)($d['code'] ?? '')), 0, 3);
        $symbol = substr(trim((string)($d['symbol'] ?? '')), 0, 8);
        $rate = (float)($d['rate'] ?? 1);
        $is_default = (int)($d['is_default'] ?? 0);
        $enabled = (int)($d['enabled'] ?? 1);
        if ($code === '' || $symbol === '' || $rate <= 0) {
            App::json(['error'=>'Invalid'],422);
            return;
        }
        $pdo = DB::conn();
        if ($is_default === 1) { $pdo->exec('UPDATE currencies SET is_default = 0'); }
        if ($id>0) {
            $stmt = $pdo->prepare('UPDATE currencies SET code=?, symbol=?, rate=?, is_default=?, enabled=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$code,$symbol,$rate,$is_default,$enabled,$id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO currencies (code,symbol,rate,is_default,enabled,created_at,updated_at) VALUES(?,?,?,?,?,NOW(),NOW())');
            $stmt->execute([$code,$symbol,$rate,$is_default,$enabled]);
        }
        App::json(['ok'=>true]);
    }

    // Payment methods APIs
    public function listPaymentMethods(): void
    {
        $this->requireAdmin();
        $pdo = DB::conn();
        $rows = $pdo->query('SELECT id, code, name, enabled, config FROM payment_methods ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
        App::json(['methods'=>$rows]);
    }
    public function upsertPaymentMethod(): void
    {
        $this->requireAdmin();
        $d = Request::json();
        $id = (int)($d['id'] ?? 0);
        $code = substr(trim((string)($d['code'] ?? '')), 0, 64);
        $name = substr(trim((string)($d['name'] ?? '')), 0, 191);
        $enabled = (int)($d['enabled'] ?? 1);
        $config = json_encode($d['config'] ?? new \stdClass());
        $pdo = DB::conn();
        if ($id>0) {
            $stmt = $pdo->prepare('UPDATE payment_methods SET code=?, name=?, enabled=?, config=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$code,$name,$enabled,$config,$id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO payment_methods (code,name,enabled,config,created_at,updated_at) VALUES(?,?,?,?,NOW(),NOW())');
            $stmt->execute([$code,$name,$enabled,$config]);
        }
        App::json(['ok'=>true]);
    }

    // Logo upload
    public function uploadLogo(): void
    {
        $this->requireAdmin();
        if (!isset($_FILES['file'])) { http_response_code(400); echo 'No file'; return; }
        $file = $_FILES['file'];
        if ($file['error'] !== UPLOAD_ERR_OK) { http_response_code(400); echo 'Upload error'; return; }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        if (!in_array($ext, ['png','jpg','jpeg','gif','svg'])) { http_response_code(400); echo 'Invalid type'; return; }
        $dir = App::$basePath . '/uploads';
        if (!is_dir($dir)) mkdir($dir, 0775, true);
        $target = $dir . '/logo.' . $ext;
        move_uploaded_file($file['tmp_name'], $target);
        $url = '/uploads/' . basename($target);
        \App\Support\Settings::set('site_logo_url', $url);
        App::json(['url'=>$url]);
    }
}
