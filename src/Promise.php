<?php

declare(strict_types=1);

namespace Romchik38\Container;

final class Promise
{
    public function __construct(
        public readonly ClassName $className
    ) {  
    }

    public function asString(): string
    {
        return ($this->className)();
    }
}
