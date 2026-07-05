<?php

// 1. Paksa PHP pindah ke root directory
chdir(__DIR__ . '/../');

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 🔥 BAJAK DENGAN AMAN: Menggunakan $app->instance agar lolos dari TypeError
$app->instance(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    new class implements Illuminate\Contracts\Debug\ExceptionHandler {
        public function report(Throwable $e) {}
        public function shouldReport(Throwable $e) { return true; }
        public function render($request, Throwable $e) {
            header('Content-Type: text/html', true, 500);
            echo "<div style='padding:20px; background:#fff5f5; color:#c53030; font-family:sans-serif;'>";
            echo "<h1>🚨 BIANG KEROK ASLI KETEMU!</h1>";
            echo "<h2>" . htmlspecialchars($e->getMessage()) . "</h2>";
            echo "<p>Eror ini terjadi di file: <b>" . $e->getFile() . "</b> baris ke-<b>" . $e->getLine() . "</b></p>";
            echo "<h3>Stack Trace:</h3>";
            echo "<pre style='background:#fff; padding:10px; border:1px solid #feb2b2; overflow:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            echo "</div>";
            exit;
        }
        public function renderForConsole($output, Throwable $e) {}
    }
);

// 2. Jalankan aplikasi Laravel
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);