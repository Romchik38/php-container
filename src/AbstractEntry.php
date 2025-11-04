<?php

declare(strict_types=1);

namespace Romchik38\Container;

use Psr\Container\ContainerInterface;
use ReflectionClass;

/** @internal */
abstract class AbstractEntry implements EntryInterface
{
    protected object|null $instance = null;

    /** @param array<int,mixed> $params */
    public function __construct(
        protected readonly ClassName $className,
        protected readonly array $params,
        protected readonly bool $isShared,
        protected readonly bool $isLazy
    ) {
    }

    public function __invoke(ContainerInterface $container): object
    {
        if ($this->isShared === true && $this->instance !== null) {
            return $this->instance;
        }

        $newParams = [];

        foreach ($this->params as $param) {
            if ($param instanceof Promise) {
                $promised    = $container->get($param->keyAsString());
                $newParams[] = $promised;
            } else {
                $newParams[] = $param;
            }
        }

        $classNameAsString = ($this->className)();
        if (! $this->isLazy) {
            $instance = new $classNameAsString(...$newParams);
        } else {
            /* @phpstan-ignore argument.type */
            $instance = (new ReflectionClass($classNameAsString))->newLazyGhost(
                /* @phpstan-ignore method.notFound */
                fn($object) => $object->__construct(...$newParams)
            );
        }

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
