<?php
function loadEnv($path)
{
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = array_map('trim', explode('=', $line, 2));
        }
        $_ENV[$name] = $value;
        putenv("$name=$value");
    }
}
define('APP_URL', getenv('APP_URL'));