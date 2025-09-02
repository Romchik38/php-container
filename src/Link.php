<?php

declare(strict_types=1);

namespace Romchik38\Container;

use Psr\Container\ContainerInterface;

/** @internal */
class Link implements EntryInterface
{
    public function __construct(
        protected readonly Key $key,
        protected readonly Promise $value
    ) {
    }

    public function __invoke(ContainerInterface $container): mixed
    {
        return $container->get($this->value->keyAsString());
    }

    public function params(): array
    {
        return [];
    }

    public function key(): string
    {
        return ($this->key)();
    }
}
