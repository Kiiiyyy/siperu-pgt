<?php

// 1. PAKSA PHP pindah ke folder utama (Root), bukan di dalam folder api! (Krusial Banget)
chdir(__DIR__ . '/../');

// 2. Manipulasi server variable agar Laravel mengira dia berjalan normal dari public/index.php
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';

// 3. Amankan folder temporary untuk cache & session di Vercel (Bypass Read-Only)
$writableFolders = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs'
];

foreach ($writableFolders as $folder) {
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }
}

// 4. Panggil index utama Laravel yang asli
require __DIR__ . '/../public/index.php';