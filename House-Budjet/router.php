<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (str_starts_with($uri, '/api')) {
    require __DIR__ . '/api/index.php';
    return true;
}

$public = __DIR__ . '/public' . $uri;
if ($uri !== '/' && is_file($public)) {
    return false;
}

require __DIR__ . '/public/index.html';
return true;
