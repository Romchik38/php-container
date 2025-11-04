<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Romchik38\Container\ClassName;
use Romchik38\Container\Shared;
use Romchik38\Tests\Unit\Classes\Primitive1;

final class SharedTest extends TestCase
{
    public function testKey(): void
    {
        $s = new Shared(
            new ClassName(Primitive1::class),
            [],
            false
        );

        $this->assertSame(Primitive1::class, $s->key());
    }
}
