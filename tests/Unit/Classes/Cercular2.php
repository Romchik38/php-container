<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit\Classes;

class Cercular2
{
    public function __construct(
        public readonly Cercular1 $cercular1
    ) {
    }
}
