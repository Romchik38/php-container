<?php

namespace Example;

use Romchik38\Container;
use Example\Router;

class App
{
    private Container $container;
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run(array $params)
    {
        $exist = array_key_exists('route', $params);
        if ($exist === false) {
            throw new \Exception('no route given');
        }
        $route = $params['route'];
        $routeType = gettype($route);
        if ($routeType !== 'string') {
            throw new \Exception('type of a route must be string, ' . $routeType . ' given');
        } 
        $router = $this->container->get(Router::class);
        $response = $router->get($route);
        return $response;
    }
}
