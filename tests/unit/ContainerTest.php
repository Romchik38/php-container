<?php

declare(strict_types=1);

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Romchik38\Container\Container;

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
        $value = 'some value';

        $fn1 = function($container) {
            return $container->get('some id');
        };

        $container = new Container();
        $container->add($id, $value);
        $container->add('callback', $fn1);
        
        $this->assertSame($value, $container->get('callback'));
    }
}
