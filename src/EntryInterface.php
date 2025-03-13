<?php

namespace Romchik38\Container;

use Psr\Container\ContainerInterface;

interface EntryInterface
{
    public function __invoke(ContainerInterface $container): object;

    public function params(): array;
}
