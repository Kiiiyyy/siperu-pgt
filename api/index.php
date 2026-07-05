<?php

// 1. Paksa Vercel membuat folder temporary yang writable di dalam runtime Lambda
$writableFolders = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions'
];

foreach ($writableFolders as $folder) {
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }
}

// 2. Jalankan index utama Laravel seperti biasa
require __DIR__ . '/../public/index.php';