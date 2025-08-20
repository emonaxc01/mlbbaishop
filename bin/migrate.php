<?php
declare(strict_types=1);

use App\Core\App;
use App\Core\DB;

require __DIR__ . '/../vendor/autoload.php';

new App(dirname(__DIR__));

$pdo = DB::conn();
$sqlDir = __DIR__ . '/../database/migrations';
$files = glob($sqlDir . '/*.sql');
sort($files);

foreach ($files as $file) {
    $sql = file_get_contents($file);
    if ($sql === false) continue;
    echo "Running migration: " . basename($file) . PHP_EOL;
    $pdo->exec($sql);
}

echo "Migrations completed" . PHP_EOL;
