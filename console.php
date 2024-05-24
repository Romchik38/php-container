<?php

declare(strict_types=1);

/**
 * Example to use with console
 * 
 * Php version 8.2
 * 
 * @category Psr-11
 * 
 * @package Container
 * 
 * @author Romchik38 <pomahehko.c@gmail.com>
 * 
 * @license MIT https://opensource.org/license/mit/
 * 
 * @link no link
 * 
 * Usage
 * 
 * "php console.php one" must print "1"
 * "php console.php some" must print "Not Fount"
 */

require __DIR__ . '/vendor/autoload.php';
$app = include __DIR__ . '/example/bootstrap.php';

try {
    if ($argc < 2) {
        printf('No arguments was passed to script');
        exit(0);
    };
    $route = $argv[1];
    $result = $app->run(['route' => $route]);
    if ($result === null) {
        printf('Not Found');
    } else {
        printf($result);
    }
} catch (\Exception $e) {
    echo 'App error:';
    echo $e->getMessage() . PHP_EOL;
}
