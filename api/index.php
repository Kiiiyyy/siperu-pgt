<?php

// 1. Paksa PHP pindah ke root directory
chdir(__DIR__ . '/../');

// 2. Amankan jalur bootstrap cache ke /tmp (Bypass Read-Only Vercel)
$_ENV['APP_BOOTSTRAP_CACHE_PATH'] = '/tmp/storage/bootstrap/cache';
putenv('APP_BOOTSTRAP_CACHE_PATH=/tmp/storage/bootstrap/cache');

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';

// 3. Pastikan folder temporary terbuat dengan hak akses penuh
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

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 🔥 PASANG LAGI MATA-MATA (Pake $app->instance agar lolos validasi container Laravel 12)
$app->instance(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    new class implements Illuminate\Contracts\Debug\ExceptionHandler {
        public function report(Throwable $e) {}
        public function shouldReport(Throwable $e) { return true; }
        public function render($request, Throwable $e) {
            header('Content-Type: text/html', true, 500);
            echo "<div style='padding:20px; background:#fff5f5; color:#c53030; font-family:sans-serif;'>";
            echo "<h1>🚨 TANGKAPAN BARU MATA-MATA!</h1>";
            echo "<h2>" . htmlspecialchars($e->getMessage()) . "</h2>";
            echo "<p>Eror terjadi di file: <b>" . $e->getFile() . "</b> baris ke-<b>" . $e->getLine() . "</b></p>";
            echo "<h3>Stack Trace:</h3>";
            echo "<pre style='background:#fff; padding:10px; border:1px solid #feb2b2; overflow:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            echo "</div>";
            exit;
        }
        public function renderForConsole($output, Throwable $e) {}
    }
);

// 4. Jalankan aplikasi Laravel
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);