<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Romchik38\Container\Container;
use Romchik38\Container\Promise;
use Romchik38\Tests\Unit\Classes\Cercular1;
use Romchik38\Tests\Unit\Classes\Cercular2;
use Romchik38\Tests\Unit\Classes\OnOtherClass2;
use Romchik38\Tests\Unit\Classes\Primitive1;

class ContainerTest extends TestCase
{
    /** ADD */
    public function testAdd()
    {
        $id        = 'some key';
        $value     = 'some value';
        $container = new Container();
        $container->add($id, $value);

        $this->assertSame($value, $container->get($id));
    }

    /** GET */
    public function testGetString()
    {
        $id        = 'some key';
        $value     = 'some value';
        $container = new Container();
        $container->add($id, $value);

        $this->assertSame($value, $container->get($id));
    }

    public function testGetCallable()
    {
        $id = 'some key';

        $fn1 = function (string $value) {
            return $value;
        };

        $container = new Container();
        $container->add('callback', $fn1);

        $result = $container->get('callback');
        $this->assertSame($id, $result($id));
    }

    public function testGetWaspromisedButDidNotAdded(): void
    {
        $container = new Container();
        $container->shared(
            OnOtherClass2::class,
            [
                'some_str',
                new Promise('numb.one'),
            ]
        );

        $this->expectException(ContainerExceptionInterface::class);
        $container->get(OnOtherClass2::class);
    }

    public function testGetNotFound(): void
    {
        $container = new Container();
        $this->expectException(NotFoundExceptionInterface::class);
        $container->get(OnOtherClass2::class);
    }

    /** HAS */
    public function testHasWasAdded(): void
    {
        $id        = 'some key';
        $value     = 'some value';
        $container = new Container();
        $container->add($id, $value);

        $this->assertSame(true, $container->has('some key'));
    }

    public function testHasWasNotAdded(): void
    {
        $container = new Container();

        $this->assertSame(false, $container->has('some key'));
    }

    /** SHARED */
    public function testSharedReAdd(): void
    {
        $container = new Container();
        $container->shared(Primitive1::class);

        $this->expectException(ContainerExceptionInterface::class);
        $container->shared(Primitive1::class);
    }

    public function testSharedWithoutPromise(): void
    {
        $container = new Container();

        $container->shared(Primitive1::class, [7]);

        $sh1 = $container->get(Primitive1::class);
        $sh2 = $container->get(Primitive1::class);

        $this->assertSame($sh1, $sh2);
        $this->assertSame($sh1->numb, $sh1->numb);
    }

    public function testSharedWithPromise(): void
    {
        $container = new Container();

        $container->shared(OnOtherClass2::class, [
            'some_string_param',
            new Promise(Primitive1::class),
        ]);

        $container->shared(Primitive1::class, [1]);

        $sh1 = $container->get(OnOtherClass2::class);
        $sh2 = $container->get(OnOtherClass2::class);

        $this->assertSame($sh1, $sh2);
        $this->assertSame($sh1->str, $sh1->str);
        $this->assertSame($sh1->depPromitive1, $sh2->depPromitive1);
        $this->assertSame($sh1->depPromitive1->numb, $sh2->depPromitive1->numb);
    }

    public function testSharedWithCercular(): void
    {
        $container = new Container();

        $container->shared(Cercular1::class, [
            new Promise(Cercular2::class),
        ]);

        $this->expectException(ContainerExceptionInterface::class);

        $container->shared(Cercular2::class, [
            new Promise(Cercular1::class),
        ]);
    }

    /** FRESH */
    public function testFreshReAdd(): void
    {
        $container = new Container();
        $container->fresh(Primitive1::class);

        $this->expectException(ContainerExceptionInterface::class);
        $container->fresh(Primitive1::class);
    }

    public function testFresh(): void
    {
        $container = new Container();

        $container->fresh(Primitive1::class, [7]);

        $sh1 = $container->get(Primitive1::class);
        $sh2 = $container->get(Primitive1::class);

        $this->assertNotSame($sh1, $sh2);
        $this->assertSame($sh1->numb, $sh1->numb);
    }

    public function testFreshWithPromise(): void
    {
        $container = new Container();

        $container->fresh(OnOtherClass2::class, [
            'some_string_param',
            new Promise(Primitive1::class),
        ]);

        $container->fresh(Primitive1::class, [1]);

        $sh1 = $container->get(OnOtherClass2::class);
        $sh2 = $container->get(OnOtherClass2::class);

        $this->assertNotSame($sh1, $sh2);
        $this->assertSame($sh1->str, $sh1->str);
        $this->assertNotSame($sh1->depPromitive1, $sh2->depPromitive1);
        $this->assertSame($sh1->depPromitive1->numb, $sh2->depPromitive1->numb);
    }

    public function testFreshWithCercular(): void
    {
        $container = new Container();

        $container->fresh(Cercular1::class, [
            new Promise(Cercular2::class),
        ]);

        $this->expectException(ContainerExceptionInterface::class);

        $container->shared(Cercular2::class, [
            new Promise(Cercular1::class),
        ]);
    }

    /** MULTI */
    public function testMultiReAdd(): void
    {
        $container = new Container();
        $container->add('key1', 10);

        $this->expectException(ContainerExceptionInterface::class);

        $container->multi(
            Primitive1::class,
            'key1',
            true,
            [1]
        );
    }

    public function testMultiPrimitive(): void
    {
        $container = new Container();

        $container->multi(
            Primitive1::class,
            'one',
            true,
            [1]
        );

        $container->multi(
            Primitive1::class,
            'seven',
            true,
            [7]
        );

        $mOne   = $container->get('one');
        $mSeven = $container->get('seven');

        $this->assertNotSame($mOne, $mSeven);
        $this->assertSame(1, $mOne->numb);
        $this->assertSame(7, $mSeven->numb);
    }

    public function testMultiDep(): void
    {
        $container = new Container();

        $container->multi(
            OnOtherClass2::class,
            'first',
            true,
            [
                'some_string_for_first',
                new Promise(Primitive1::class),
            ]
        );

        $container->multi(
            OnOtherClass2::class,
            'seconds',
            true,
            [
                'another_string_for_second',
                new Promise(Primitive1::class),
            ]
        );

        $container->fresh(Primitive1::class, [7]);

        $mFirst  = $container->get('first');
        $mSecond = $container->get('seconds');

        $this->assertNotSame($mFirst, $mSecond);
        $this->assertSame('some_string_for_first', $mFirst->str);
        $this->assertSame('some_string_for_first', $mFirst->str);
        $this->assertSame(7, $mFirst->depPromitive1->numb);
        $this->assertSame(7, $mSecond->depPromitive1->numb);
    }

    public function testMultiWithCercular(): void
    {
        $container = new Container();

        $container->multi(
            Cercular1::class,
            'key.1',
            true,
            [
                new Promise(Cercular2::class),
            ]
        );

        $this->expectException(ContainerExceptionInterface::class);

        $container->multi(
            Cercular2::class,
            'key.1',
            true,
            [
                new Promise(Cercular1::class),
            ]
        );
    }
}
