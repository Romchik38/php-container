<?php

declare(strict_types=1);

namespace Romchik38\Container;

/** @internal */
class Shared extends AbstractEntry
{
    /** @param array<int,mixed> $params */
    public function __construct(
        ClassName $className,
        array $params,
        bool $isLazy
    ) {
        parent::__construct($className, $params, true, $isLazy);
    }

    public function key(): string
    {
        return ($this->className)();
    }
}
