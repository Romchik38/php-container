<?php

declare(strict_types=1);

namespace Romchik38\Container;

final class Promise
{
    public readonly Key $key;

    public function __construct(
        string $key
    ) {
        $this->key = new Key($key);
    }

    public function key(): Key
    {
        return $this->key;
    }

    public function keyAsString(): string
    {
        return ($this->key())();
    }
}
