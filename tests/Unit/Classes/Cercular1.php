<?php

namespace Romchik38\Tests\Unit\Classes;

class Cercular1
{
    public function __construct(
        public readonly Cercular2 $cercular2
    ) {  
    }
}
