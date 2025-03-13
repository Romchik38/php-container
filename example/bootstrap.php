<?php

//require __DIR__ . './../vendor/autoload.php';

include 'Db.php';
include 'App.php';
include 'Router.php';

use Example\Db;
use Example\Router;
use Example\App;
use Romchik38\Container\Container;

$dataDb = ['one' => '1', 'two' => '2', 'hello' => 'world'];

$container = new Container();
$container->add(Db::class, new Db($dataDb));
$container->add(Router::class, new Router($container->get(Db::class)));
$app = new App($container);

return $app;