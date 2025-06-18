<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Jalankan semua perintah artisan yang kamu butuhkan
$commands = [
    'optimize:clear',
    'cache:clear',
    'config:clear',
    'route:clear',
    'view:clear',
    'config:cache',
    'route:cache',
];

foreach ($commands as $command) {
    echo "Menjalankan: php artisan {$command} <br>";
    $kernel->call($command);
}

// Setelah semua clear dan cache dijalankan, beri role Admin ke user ID 1
use App\Models\User;

$user = User::find(1);
if ($user) {
    $user->assignRole('Admin');
    echo "<br>✅ Role 'Admin' berhasil diberikan ke user ID 1.";
} else {
    echo "<br>❌ User ID 1 tidak ditemukan.";
}