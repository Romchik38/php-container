<?php

declare(strict_types=1);

namespace Romchik38\Container;

use Psr\Container\ContainerInterface;

class Fresh extends Shared
{
    public function __invoke(ContainerInterface $container): object
    {
        $newParams = [];

        foreach($this->params as $param) {
            if ($param instanceof Promise) {
                $promised = $container->get($param->keyAsString());
                $newParams[] = $promised;
            } else {
                $newParams[] = $param;
            }
        }

        $classNameAsString = ($this->className)();
        return new $classNameAsString(...$newParams);
    }
}
