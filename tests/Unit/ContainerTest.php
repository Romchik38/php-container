<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;
use Romchik38\Container\Container;
use Romchik38\Container\ContainerException;
use Romchik38\Container\Key;
use Romchik38\Container\NotFoundException;
use Romchik38\Container\Promise;
use Romchik38\Tests\Unit\Classes\Primitive1;
use Romchik38\Tests\Unit\Classes\OnOtherClass2;

class ContainerTest extends TestCase
{
    /** ADD */
    public function testAdd()
    {
        $id = 'some id';
        $value = 'some value';
        $container = new Container();
        $container->add($id, $value);

        $this->assertSame($value, $container->get($id));
    }

    /** GET */
    public function testGetString()
    {
        $id = 'some id';
        $value = 'some value';
        $container = new Container();
        $container->add($id, $value);

        $this->assertSame($value, $container->get($id));
    }

    public function testGetCallable()
    {
        $id = 'some id';

        $fn1 = function(string $value) {
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
            '\Romchik38\Tests\Unit\Classes\OnOtherClass2',
            [
                'some_str',
                new Promise('numb.one')
            ]
        );

        $this->expectException(ContainerException::class);
        $container->get('\Romchik38\Tests\Unit\Classes\OnOtherClass2');
    }

    public function testGetNotFound(): void
    {
        $container = new Container();
        $this->expectException(NotFoundExceptionInterface::class);
        $container->get('\Romchik38\Tests\Unit\Classes\OnOtherClass2');
    }

    /** SHARED */
    public function testShared(): void
    {
        $container = new Container();

        $container->shared(Primitive1::class, [7]);
        
        $sh1 = $container->get(Primitive1::class);
        $sh2 = $container->get(Primitive1::class);

        $this->assertSame($sh1, $sh2);
        $this->assertSame($sh1->numb, $sh1->numb);
    }

    /** FRESH */
    public function testFresh(): void
    {
        $container = new Container();

        $container->fresh(Primitive1::class, [7]);
        
        $sh1 = $container->get(Primitive1::class);
        $sh2 = $container->get(Primitive1::class);

        $this->assertNotSame($sh1, $sh2);
        $this->assertSame($sh1->numb, $sh1->numb);
    }
 
    /** MULTI */
    public function testMultiPrimitive(): void
    {
        $container = new Container();

        $container->multi(
            Primitive1::class,
            new Key('one'),
            true,
            [1]
        );
        
        $container->multi(
            Primitive1::class,
            new Key('seven'),
            true,
            [7]
        );
        
        $mOne = $container->get('one');
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
            new Key('first'),
            true,
            [
                'some_string_for_first',
                new Promise(Primitive1::class)
            ]
            
        );
        
        $container->multi(
            OnOtherClass2::class,
            new Key('seconds'),
            true,
            [
                'another_string_for_second',
                new Promise(Primitive1::class)
            ]
            
        );

        $container->fresh(Primitive1::class, [7]);
        
        $mFirst = $container->get('first');
        $mSecond = $container->get('seconds');

        $this->assertNotSame($mFirst, $mSecond);
        $this->assertSame('some_string_for_first', $mFirst->str);
        $this->assertSame('some_string_for_first', $mFirst->str);
        $this->assertSame(7, $mFirst->depPromitive1->numb);
        $this->assertSame(7, $mSecond->depPromitive1->numb);
    }
}
