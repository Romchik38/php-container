<?php

namespace Romchik38\Container;

use Psr\Container\ContainerInterface;

abstract class AbstractEntry implements EntryInterface
{
    protected object|null $instance = null;

    /** @param array<int,mixed> $params */
    public function __construct(
        protected readonly ClassName $className, 
        protected readonly array $params,
        protected readonly bool $isShared
    ) {
    }

    public function __invoke(ContainerInterface $container): object
    {
        if ($this->isShared === true && $this->instance !== null) {
            return $this->instance;
        }

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
        $instance = new $classNameAsString(...$newParams);
        if ($this->isShared === true && $this->instance === null) {
            $this->instance = $instance;
        }

        return $instance;
    }

    public function params(): array
    {
        return $this->params;
    }

    abstract public function key(): string;
}
