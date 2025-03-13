<?php

namespace Romchik38\Tests\Unit\Classes;

class OnOtherClass1
{
    public function __construct(
        public readonly int $numb,
        public readonly NoDep1 $noDep1
    ) {
    }
}
