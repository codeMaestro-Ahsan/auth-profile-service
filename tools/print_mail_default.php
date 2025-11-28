<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo 'mail.default = ' . config('mail.default') . PHP_EOL;
echo 'MAIL_MAILER env = ' . env('MAIL_MAILER') . PHP_EOL;
echo 'queue.default = ' . config('queue.default') . PHP_EOL;
echo 'QUEUE_CONNECTION env = ' . env('QUEUE_CONNECTION') . PHP_EOL;
