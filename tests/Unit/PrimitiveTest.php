<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Romchik38\Container\Container;
use Romchik38\Container\Key;
use Romchik38\Container\Primitive;

final class PrimitiveTest extends TestCase
{
    public function testInvoke(): void
    {
        $c = new Container();
        $p = new Primitive(new Key('some_key'), 1);
        
        $this->assertSame(1, $p($c));
    }

    public function testParams(): void
    {
        $p = new Primitive(new Key('some_key'), 1);

        $this->assertSame([], $p->params());
    }

    public function testKey(): void
    {
        $p = new Primitive(new Key('some_key'), 1);

        $this->assertSame('some_key', $p->key());
    }
}
