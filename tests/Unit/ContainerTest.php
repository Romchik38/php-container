<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Romchik38\Container\ClassName;
use Romchik38\Container\Container;
use Romchik38\Container\Key;
use Romchik38\Container\Promise;
use Romchik38\Tests\Unit\Classes\Primitive1;
use Romchik38\Tests\Unit\Classes\OnOtherClass2;

class ContainerTest extends TestCase
{
    public function testAdd()
    {
        $id = 'some id';
        $value = 'some value';
        $container = new Container();
        $container->add($id, $value);

        $this->assertSame($value, $container->get($id));
    }

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

    public function testShared(): void
    {
        $container = new Container();

        $container->shared(new ClassName(Primitive1::class), 7);
        
        $sh1 = $container->get(Primitive1::class);
        $sh2 = $container->get(Primitive1::class);

        $this->assertSame($sh1, $sh2);
        $this->assertSame($sh1->numb, $sh1->numb);
    }

    public function testFresh(): void
    {
        $container = new Container();

        $container->fresh(new ClassName(Primitive1::class), 7);
        
        $sh1 = $container->get(Primitive1::class);
        $sh2 = $container->get(Primitive1::class);

        $this->assertNotSame($sh1, $sh2);
        $this->assertSame($sh1->numb, $sh1->numb);
    }
    
    public function testMultiPrimitive(): void
    {
        $container = new Container();

        $container->multi(
            new ClassName(Primitive1::class),
            new Key('one'),
            1
        );
        
        $container->multi(
            new ClassName(Primitive1::class),
            new Key('seven'),
            7
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
            new ClassName(OnOtherClass2::class),
            new Key('first'),
            'some_string_for_first',
            new Promise(Primitive1::class)
            
        );
        
        $container->multi(
            new ClassName(OnOtherClass2::class),
            new Key('seconds'),
            'another_string_for_second',
            new Promise(Primitive1::class)
            
        );

        $container->fresh(new ClassName(Primitive1::class), 7);
        
        $mFirst = $container->get('first');
        $mSecond = $container->get('seconds');

        $this->assertNotSame($mFirst, $mSecond);
        $this->assertSame('some_string_for_first', $mFirst->str);
        $this->assertSame('some_string_for_first', $mFirst->str);
        $this->assertSame(7, $mFirst->depPromitive1->numb);
        $this->assertSame(7, $mSecond->depPromitive1->numb);
    }
}
