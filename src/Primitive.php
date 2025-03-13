<?php

declare(strict_types=1);

namespace Romchik38\Container;

use Psr\Container\ContainerInterface;

class Primitive implements EntryInterface
{
    public function __construct(
        protected readonly Key $key,
        protected readonly int|float|string|array $value
    ) {   
    }

    public function __invoke(ContainerInterface $container): int|float|string|array
    {
        return $this->value;
    }

    public function params(): array {
        return [];
    }

    public function key(): string
    {
        return $this->key();
    }
}