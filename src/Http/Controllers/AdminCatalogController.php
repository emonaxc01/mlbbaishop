<?php
namespace App\Http\Controllers;

use App\Core\App;
use App\Core\DB;
use App\Support\Request;
use PDO;

class AdminCatalogController
{
    private function requireAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $uid = (int)($_SESSION['user_id'] ?? 0);
        if (!$uid) { http_response_code(302); header('Location:/'); exit; }
        $pdo = DB::conn();
        $is = (int)$pdo->query('SELECT is_admin FROM users WHERE id = '.$uid)->fetchColumn();
        if ($is !== 1) { http_response_code(403); echo 'Forbidden'; exit; }
    }

    public function productsPage(): void { $this->requireAdmin(); App::view('admin/catalog_products'); }
    public function productDetailPage(): void { $this->requireAdmin(); App::view('admin/catalog_product_detail'); }

    public function listProducts(): void
    {
        $this->requireAdmin();
        $pdo = DB::conn();
        $rows = $pdo->query('SELECT id, name, slug, sku, price, stock, status FROM products ORDER BY id DESC LIMIT 500')->fetchAll(PDO::FETCH_ASSOC);
        App::json(['items'=>$rows]);
    }
    public function saveProduct(): void
    {
        $this->requireAdmin();
        $d = Request::json();
        $id = (int)($d['id'] ?? 0);
        $name = substr(trim((string)($d['name'] ?? '')), 0, 191);
        $slug = substr(trim((string)($d['slug'] ?? '')), 0, 191);
        $sku = substr(trim((string)($d['sku'] ?? '')), 0, 64);
        $price = (float)($d['price'] ?? 0);
        $stock = (int)($d['stock'] ?? 0);
        $status = (int)($d['status'] ?? 1);
        $image_url = substr(trim((string)($d['image_url'] ?? '')), 0, 512);
        $desc = (string)($d['description'] ?? null);
        if ($name === '' || $slug === '') return App::json(['error'=>'Invalid'],422);
        $pdo = DB::conn();
        if ($id>0) {
            $stmt = $pdo->prepare('UPDATE products SET name=?, slug=?, sku=?, price=?, stock=?, status=?, image_url=?, description=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$name,$slug,$sku,$price,$stock,$status,$image_url,$desc,$id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO products (name,slug,sku,price,stock,status,image_url,description,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,NOW(),NOW())');
            $stmt->execute([$name,$slug,$sku,$price,$stock,$status,$image_url,$desc]);
            $id = (int)$pdo->lastInsertId();
        }
        App::json(['id'=>$id]);
    }
    public function deleteProduct(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id<=0) return App::json(['error'=>'Invalid'],422);
        DB::conn()->prepare('DELETE FROM products WHERE id=?')->execute([$id]);
        App::json(['ok'=>true]);
    }

    public function listVariations(): void
    {
        $this->requireAdmin();
        $pid = (int)($_GET['productId'] ?? 0);
        $stmt = DB::conn()->prepare('SELECT id, name, sku, price, stock, status FROM product_variations WHERE product_id=? ORDER BY id ASC');
        $stmt->execute([$pid]);
        App::json(['items'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }
    public function saveVariation(): void
    {
        $this->requireAdmin();
        $d = Request::json();
        $id = (int)($d['id'] ?? 0);
        $pid = (int)($d['product_id'] ?? 0);
        $name = substr(trim((string)($d['name'] ?? '')), 0, 191);
        $sku = substr(trim((string)($d['sku'] ?? '')), 0, 64);
        $price = (float)($d['price'] ?? 0);
        $stock = (int)($d['stock'] ?? 0);
        $status = (int)($d['status'] ?? 1);
        if ($pid<=0 || $name==='') return App::json(['error'=>'Invalid'],422);
        $pdo = DB::conn();
        if ($id>0) {
            $stmt = $pdo->prepare('UPDATE product_variations SET name=?, sku=?, price=?, stock=?, status=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$name,$sku,$price,$stock,$status,$id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO product_variations (product_id,name,sku,price,stock,status,created_at,updated_at) VALUES (?,?,?,?,?,?,NOW(),NOW())');
            $stmt->execute([$pid,$name,$sku,$price,$stock,$status]);
            $id = (int)$pdo->lastInsertId();
        }
        App::json(['id'=>$id]);
    }
    public function deleteVariation(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id<=0) return App::json(['error'=>'Invalid'],422);
        DB::conn()->prepare('DELETE FROM product_variations WHERE id=?')->execute([$id]);
        App::json(['ok'=>true]);
    }

    public function listProductMeta(): void
    {
        $this->requireAdmin();
        $pid = (int)($_GET['productId'] ?? 0);
        $stmt = DB::conn()->prepare('SELECT id, meta_key, meta_value FROM product_meta WHERE product_id=? ORDER BY id ASC');
        $stmt->execute([$pid]);
        App::json(['items'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }
    public function saveProductMeta(): void
    {
        $this->requireAdmin();
        $d = Request::json();
        $id = (int)($d['id'] ?? 0);
        $pid = (int)($d['product_id'] ?? 0);
        $k = substr(trim((string)($d['meta_key'] ?? '')), 0, 128);
        $v = (string)($d['meta_value'] ?? '');
        if ($pid<=0 || $k==='') return App::json(['error'=>'Invalid'],422);
        $pdo = DB::conn();
        if ($id>0) {
            $pdo->prepare('UPDATE product_meta SET meta_key=?, meta_value=?, updated_at=NOW() WHERE id=?')->execute([$k,$v,$id]);
        } else {
            $pdo->prepare('INSERT INTO product_meta (product_id, meta_key, meta_value, created_at, updated_at) VALUES (?,?,?,NOW(),NOW())')->execute([$pid,$k,$v]);
            $id = (int)$pdo->lastInsertId();
        }
        App::json(['id'=>$id]);
    }
    public function deleteProductMeta(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id<=0) return App::json(['error'=>'Invalid'],422);
        DB::conn()->prepare('DELETE FROM product_meta WHERE id=?')->execute([$id]);
        App::json(['ok'=>true]);
    }

    public function listVariationMeta(): void
    {
        $this->requireAdmin();
        $vid = (int)($_GET['variationId'] ?? 0);
        $stmt = DB::conn()->prepare('SELECT id, meta_key, meta_value FROM variation_meta WHERE variation_id=? ORDER BY id ASC');
        $stmt->execute([$vid]);
        App::json(['items'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }
    public function saveVariationMeta(): void
    {
        $this->requireAdmin();
        $d = Request::json();
        $id = (int)($d['id'] ?? 0);
        $vid = (int)($d['variation_id'] ?? 0);
        $k = substr(trim((string)($d['meta_key'] ?? '')), 0, 128);
        $v = (string)($d['meta_value'] ?? '');
        if ($vid<=0 || $k==='') return App::json(['error'=>'Invalid'],422);
        $pdo = DB::conn();
        if ($id>0) {
            $pdo->prepare('UPDATE variation_meta SET meta_key=?, meta_value=?, updated_at=NOW() WHERE id=?')->execute([$k,$v,$id]);
        } else {
            $pdo->prepare('INSERT INTO variation_meta (variation_id, meta_key, meta_value, created_at, updated_at) VALUES (?,?,?,NOW(),NOW())')->execute([$vid,$k,$v]);
            $id = (int)$pdo->lastInsertId();
        }
        App::json(['id'=>$id]);
    }
    public function deleteVariationMeta(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id<=0) return App::json(['error'=>'Invalid'],422);
        DB::conn()->prepare('DELETE FROM variation_meta WHERE id=?')->execute([$id]);
        App::json(['ok'=>true]);
    }
}
