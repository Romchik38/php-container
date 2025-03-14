<?php

declare(strict_types=1);

namespace Romchik38\Container;

use InvalidArgumentException;

/** @internal */
class Key
{
    public function __construct(
        public readonly string $key
    ) {
        if ($key === '') {
            throw new InvalidArgumentException('key is empty');
        }
    }

    public function __invoke(): string
    {
        return $this->key;
    }
}