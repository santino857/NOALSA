<?php

/**
 * Punto de entrada para Vercel (runtime vercel-php).
 * En Vercel solo se puede escribir en /tmp, así que ahí van cache y logs.
 */
$writable = '/tmp/writable';
foreach (['cache', 'logs', 'debugbar', 'uploads', 'session'] as $dir) {
    if (! is_dir("{$writable}/{$dir}")) {
        mkdir("{$writable}/{$dir}", 0777, true);
    }
}
putenv('WRITABLE_PATH=' . $writable);
$_SERVER['WRITABLE_PATH'] = $writable;

// Si no se define otra cosa en Vercel, se corre en producción
if (! getenv('CI_ENVIRONMENT')) {
    putenv('CI_ENVIRONMENT=production');
    $_SERVER['CI_ENVIRONMENT'] = 'production';
}

// Configurar APP_BASE_URL automáticamente según el host de Vercel si no se especificó
if (! getenv('APP_BASE_URL')) {
    $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['VERCEL_URL'] ?? 'noalsa.vercel.app');
    $baseUrl = "https://{$host}/";
    putenv("APP_BASE_URL={$baseUrl}");
    $_SERVER['APP_BASE_URL'] = $baseUrl;
    $_ENV['APP_BASE_URL'] = $baseUrl;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';

chdir(__DIR__ . '/../public');
require __DIR__ . '/../public/index.php';
