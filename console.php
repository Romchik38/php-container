<?php declare(strict_types=1);

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
 */

try {
    $app = include __DIR__ . '/example/bootstrap.php';
    $route = 'one';
    $result = $app->run(['route' => $route]);    
    echo $result;
} catch (\Exception $e) {
    echo 'App error:';
    echo $e->getMessage() . PHP_EOL;
}
