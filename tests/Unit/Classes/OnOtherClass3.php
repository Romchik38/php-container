<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit\Classes;

class OnOtherClass3
{
    /** @param array<int,string> $listOfStrings */
    public function __construct(
        public readonly array $listOfStrings,
        public readonly Primitive2 $depPromitive2,
        public readonly Primitive3 $depPromitive3
    ) {
    }
}
