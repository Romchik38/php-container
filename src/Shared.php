<?php

namespace Romchik38\Container;

use Psr\Container\ContainerInterface;

/** @internal */
class Shared
{
    protected object|null $instance = null;

    /** @param array<int,mixed> $params */
    public function __construct(
        protected readonly ClassName $className, 
        protected readonly array $params
    ) {
    }

    public function __invoke(ContainerInterface $container): object
    {
        if ($this->instance !== null) {
            return $this->instance;
        }

        $newParams = [];

        foreach($this->params as $param) {
            if ($param instanceof Promise) {
                $promised = $container->get($param->asString());
                $newParams[] = $promised;
            } else {
                $newParams[] = $param;
            }
        }

        $classNameAsString = ($this->className)();
        $this->instance = new $classNameAsString(...$newParams);
        return $this->instance;
    }
}
