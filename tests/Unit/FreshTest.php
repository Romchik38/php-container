<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Romchik38\Container\ClassName;
use Romchik38\Container\Container;
use Romchik38\Container\Fresh;
use Romchik38\Container\Promise;
use Romchik38\Container\Shared;
use Romchik38\Tests\Unit\Classes\NoDep1;
use Romchik38\Tests\Unit\Classes\Primitive1;
use Romchik38\Tests\Unit\Classes\OnOtherClass2;

final class FreshTest extends TestCase
{
    public function testInstanseOfShared(): void
    {
        $fr = new Fresh(
            new ClassName(NoDep1::class),
            []
        );

        $this->assertSame(true, $fr instanceof Shared);
    }

    public function testInvoke(): void
    {
        $container = new Container();
        $fr = new Fresh(
            new ClassName('\Romchik38\Tests\Unit\Classes\Primitive1'),
            [1]
        );

        $nd = $fr->__invoke($container);
        $this->assertSame(1, $nd->numb);
    }

    // public function testInvokeWithPromise(): void
    // {
    //     $container = new Container();
    //     $fr = new Fresh(
    //         new ClassName('\Romchik38\Tests\Unit\Classes\OnOtherClass2'),
    //         [
    //             'some_string', 
    //             new Promise('\Romchik38\Tests\Unit\Classes\Primitive1')
    //         ],
            
    //     );

    //     $nd = $fr->__invoke($container);
    //     $this->assertSame(1, $nd->numb);
    // }
}