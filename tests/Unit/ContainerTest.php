<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Romchik38\Container\ClassName;
use Romchik38\Container\Container;

use Romchik38\Tests\Unit\Classes\Primitive1;

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
        
    }
}
