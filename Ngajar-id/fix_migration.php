<?php
function logMessage($msg)
{
    echo $msg;
    file_put_contents('migration_log.txt', $msg, FILE_APPEND);
}
logMessage("--- Migration Start: " . date('Y-m-d H:i:s') . " ---\n");
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    logMessage("Connecting to DB: " . config('database.default') . " on " . config('database.connections.pgsql.host') . ":" . config('database.connections.pgsql.port') . "\n");
    $exists = Illuminate\Support\Facades\Schema::hasTable('broadcast_logs');
    logMessage("Table broadcast_logs exists: " . ($exists ? "YES" : "NO") . "\n");

    if (!$exists) {
        logMessage("Running migration manually via Artisan...\n");
        $exitCode = Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        logMessage("Migration exit code: " . $exitCode . "\n");
        logMessage("Artisan Output: \n" . Illuminate\Support\Facades\Artisan::output() . "\n");

        $existsAfter = Illuminate\Support\Facades\Schema::hasTable('broadcast_logs');
        logMessage("Table broadcast_logs exists after Artisan call: " . ($existsAfter ? "YES" : "NO") . "\n");
    }
} catch (\Throwable $e) {
    logMessage("FATAL ERROR: " . $e->getMessage() . "\n");
    logMessage("Trace: " . $e->getTraceAsString() . "\n");
}
