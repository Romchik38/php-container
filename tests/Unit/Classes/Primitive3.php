<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit\Classes;

class Primitive3
{
    /** @param array<int,string> $listOfStrings */
    public function __construct(
        public readonly array $listOfStrings
    ) {
    }
}
