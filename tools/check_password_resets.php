<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = $app->make('db');

try {
    $table = config('auth.passwords.users.table') ?? 'password_resets';
    $rows = $db->table($table)
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    if ($rows->isEmpty()) {
        echo "password_resets: no rows found\n";
    } else {
        foreach ($rows as $r) {
            echo "email: {$r->email}  token: {$r->token}  created_at: {$r->created_at}\n";
        }
    }
} catch (Exception $e) {
    echo 'error: ' . $e->getMessage() . PHP_EOL;
}
