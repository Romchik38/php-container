<?php

declare(strict_types=1);

namespace Romchik38\Container;

/** @internal */
class Fresh extends AbstractEntry
{
    /** @param array<int,mixed> $params */
    public function __construct(
        ClassName $className, 
        array $params
    ) {
        parent::__construct($className, $params, false);
    }

    public function key(): string
    {
        return ($this->className)();
    }
}
