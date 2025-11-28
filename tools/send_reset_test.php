<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Use fully qualified class names
$userModel = 'App\\Models\\User';
$notificationClass = 'App\\Notifications\\PasswordResetNotification';
$passwordFacade = 'Illuminate\\Support\\Facades\\Password';

$u = $userModel::first();
if ($u) {
    $token = $passwordFacade::broker()->createToken($u);
    $u->notify(new $notificationClass($token));
    echo "notified: " . $u->email . PHP_EOL;
} else {
    echo "no user found" . PHP_EOL;
}
