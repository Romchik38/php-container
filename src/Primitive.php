<?php

declare(strict_types=1);

namespace Romchik38\Container;

use Psr\Container\ContainerInterface;

class Primitive implements EntryInterface
{
    public function __construct(
        protected readonly Key $key,
        protected readonly mixed $value
    ) {   
    }

    public function __invoke(ContainerInterface $container): mixed
    {
        return $this->value;
    }

    public function params(): array {
        return [];
    }

    public function key(): string
    {
        return ($this->key)();
    }
}