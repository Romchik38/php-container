<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Romchik38\Container\ClassName;
use Romchik38\Container\Container;
use Romchik38\Container\Key;
use Romchik38\Container\Multi;

final class MultiTest extends TestCase
{
    public function testKey(): void
    {
        $m = new Multi(
            new ClassName('Romchik38\Tests\Unit\Classes\Primitive1'),
            [],
            new Key('some_class'),
            true
        );

        $this->assertSame('some_class', $m->key());
    }
}