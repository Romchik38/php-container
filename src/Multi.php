<?php

namespace Romchik38\Container;

/** @internal */
class Multi extends Shared
{
    /** @param array<int,mixed> $params */
    public function __construct(
        ClassName $className, 
        array $params,
        protected readonly Key $key
    ) {
        parent::__construct($className, $params);
    }

    public function key(): string
    {
        return $this->key();
    }
}
