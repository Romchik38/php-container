<?php

include __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/example/bootstrap.php';

use Example\App;

try {
    
    $app = new App($container);
    $app->run([
        $_SERVER
    ]);    
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    echo '500 - server error';
    http_response_code(500);
}
