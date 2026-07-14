<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo 'Success: Connected to ' . \Illuminate\Support\Facades\DB::connection()->getDatabaseName() . PHP_EOL;
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
