<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Romchik38\Container\ClassName;
use Romchik38\Container\Shared;

final class SharedTest extends TestCase
{
    public function testKey(): void
    {
        $s = new Shared(
            new ClassName('\Romchik38\Tests\Unit\Classes\Primitive1'),
            []
        );

        $this->assertSame('\Romchik38\Tests\Unit\Classes\Primitive1', $s->key());
    }
}
