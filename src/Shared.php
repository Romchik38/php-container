<?php

namespace Romchik38\Container;

use Psr\Container\ContainerInterface;

/** @internal */
class Shared
{
    /** @var array<int,mixed> $params */
    protected readonly array $params;

    protected object|null $instance = null;

    public function __construct(
        protected readonly ClassName $className, 
        ...$params
        )
    {
        $this->params = $params;
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
