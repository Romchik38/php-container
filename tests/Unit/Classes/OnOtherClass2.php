<?php

namespace Romchik38\Tests\Unit\Classes;

class OnOtherClass2
{
    public function __construct(
        public readonly string $str,
        public readonly Primitive1 $depPromitive1
    ) {
    }
}
