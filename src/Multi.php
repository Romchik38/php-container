<?php

declare(strict_types=1);

namespace Romchik38\Container;

/** @internal */
class Multi extends AbstractEntry
{
    /** @param array<int,mixed> $params */
    public function __construct(
        ClassName $className,
        array $params,
        protected readonly Key $key,
        bool $isShared
    ) {
        parent::__construct($className, $params, $isShared);
    }

    public function key(): string
    {
        return ($this->key)();
    }
}
