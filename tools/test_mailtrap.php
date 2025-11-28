<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

echo "Testing Mailtrap SMTP connection...\n";
echo "Mail driver: " . config('mail.default') . "\n";
echo "Mail host: " . config('mail.mailers.smtp.host') . "\n";
echo "Mail port: " . config('mail.mailers.smtp.port') . "\n";
echo "Mail username: " . config('mail.mailers.smtp.username') . "\n\n";

try {
    Mail::raw('This is a test email from Laravel to Mailtrap', function ($message) {
        $message->to('test@mailtrap.io')->subject('Test Email to Mailtrap');
    });
    echo "Email sent successfully to Mailtrap!\n";
} catch (Exception $e) {
    echo "Error sending email: " . $e->getMessage() . "\n";
}
