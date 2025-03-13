<?php

namespace Romchik38\Container;

/** @internal */
class Shared extends AbstractEntry
{
    /** @param array<int,mixed> $params */
    public function __construct(
        ClassName $className, 
        array $params
    ) {
        parent::__construct($className, $params, true);
    }

    public function key(): string
    {
        return ($this->className)();
    }
}
