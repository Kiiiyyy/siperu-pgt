<?php

// 1. Paksa PHP pindah ke root directory
chdir(__DIR__ . '/../');

// 🔥 CHEAT CODE: Pindahkan jalur bootstrap cache ke /tmp (Bypass Read-Only & Bersihkan Dev Packages)
$_ENV['APP_BOOTSTRAP_CACHE_PATH'] = '/tmp/storage/bootstrap/cache';
putenv('APP_BOOTSTRAP_CACHE_PATH=/tmp/storage/bootstrap/cache');

// 2. Manipulasi server variable agar Laravel berjalan normal
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';

// 3. Amankan seluruh folder temporary di Vercel
$writableFolders = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/bootstrap/cache',
    '/tmp/storage/logs'
];

foreach ($writableFolders as $folder) {
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }
}

// 4. Panggil engine utama Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 5. Jalankan aplikasi secara native (Kita lepas script mata-mata karena sistem udah stabil)
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);