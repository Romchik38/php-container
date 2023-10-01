<?php

/**
 * Usage
 * 
 * http://localhost:8000/one
 * must return 1
 * 
 * http://localhost:8000/some
 * must return 404 Not Found
 * 
 */

include __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/example/bootstrap.php';

use Example\App;

function parseUrl(string $path): string
{
    $result = pathinfo($path, PATHINFO_FILENAME);
    if (gettype($result) === 'string') {
        if (strlen($result > 0)) return $result;
    }
    return "";
}

function send(int $code, string $message = ''): void
{
    $defaultAnswers = [
        404 => '404 Not Found'
    ];
    $response = '';
    if (strlen($message) === 0) $response = $defaultAnswers[$code];
    else $response = $message;
    echo $response;
    http_response_code($code);
}

try {
    $exist = key_exists('REQUEST_URI', $_SERVER);
    if ($exist === false) exit('Program must run via php-fpm server');
    $path = $_SERVER['REQUEST_URI'];
    $route = parseUrl($path);
    if (strlen($route) === 0) {
        send(404);
        exit();
    }
    $app = new App($container);
    $response = $app->run([
        'route' => $route
    ]);
    if ($response === Null) send(404);
    else send(200, $response);
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    send(500, '500 - server error');
}
