<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Romchik38\Container\ClassName;
use Romchik38\Container\Container;
use Romchik38\Container\Fresh;
use Romchik38\Container\Promise;
use Romchik38\Tests\Unit\Classes\OnOtherClass2;
use Romchik38\Tests\Unit\Classes\Primitive1;

final class FreshTest extends TestCase
{
    public function testInvoke(): void
    {
        $container = new Container();
        $fr        = new Fresh(
            new ClassName(Primitive1::class),
            [1]
        );

        $nd = $fr->__invoke($container);
        $this->assertSame(1, $nd->numb);
    }

    public function testInvokeWithPromise(): void
    {
        $container = new Container();
        $fr        = new Fresh(
            new ClassName(OnOtherClass2::class),
            [
                'some_string',
                new Promise(Primitive1::class),
            ],
        );

        $container->shared(Primitive1::class, [1]);

        $instance = $fr->__invoke($container);
        $this->assertSame('some_string', $instance->str);
        $this->assertSame(1, $instance->depPromitive1->numb);
    }
}
