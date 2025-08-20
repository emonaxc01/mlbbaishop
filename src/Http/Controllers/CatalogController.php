<?php
namespace App\Http\Controllers;

use App\Core\App;
use App\Core\DB;
use PDO;

class CatalogController
{
    public function list(): void
    {
        $pdo = DB::conn();
        $q = trim((string)($_GET['q'] ?? ''));
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = min(50, max(1, (int)($_GET['limit'] ?? 12)));
        $offset = ($page - 1) * $limit;
        $where = 'status = 1';
        $params = [];
        if ($q !== '') { $where .= ' AND name LIKE ?'; $params[] = "%$q%"; }
        $stmt = $pdo->prepare("SELECT id, name, slug, sku, image_url, price, stock FROM products WHERE $where ORDER BY id DESC LIMIT $limit OFFSET $offset");
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        App::json(['page'=>$page,'limit'=>$limit,'items'=>$rows]);
    }

    public function detail(): void
    {
        $pdo = DB::conn();
        $slug = (string)($_GET['slug'] ?? '');
        $stmt = $pdo->prepare('SELECT id, name, slug, sku, image_url, price, stock, status, description FROM products WHERE slug = ?');
        $stmt->execute([$slug]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) { http_response_code(404); App::json(['error'=>'Not found'],404); return; }
        $pid = (int)$product['id'];
        $vars = $pdo->prepare('SELECT id, name, sku, price, stock, status FROM product_variations WHERE product_id = ? ORDER BY id ASC');
        $vars->execute([$pid]);
        $variations = $vars->fetchAll(PDO::FETCH_ASSOC);
        $meta = $pdo->prepare('SELECT meta_key, meta_value FROM product_meta WHERE product_id = ?');
        $meta->execute([$pid]);
        $metas = $meta->fetchAll(PDO::FETCH_KEY_PAIR);
        App::json(['product'=>$product,'variations'=>$variations,'meta'=>$metas]);
    }
}
