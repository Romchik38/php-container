<?php

namespace Romchik38\Container;

use Psr\Container\ContainerInterface;

/** @internal */
interface EntryInterface
{
    public function __invoke(ContainerInterface $container): mixed;

    public function params(): array;

    public function key(): string;
}
