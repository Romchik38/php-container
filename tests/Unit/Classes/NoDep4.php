<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit\Classes;

class NoDep4
{
    public int $numb;

    public function __construct()
    {
        $this->numb = 1;
    }
}
