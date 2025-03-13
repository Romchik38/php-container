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

        $fn1 = function(string $value) {
            return $value;
        };

        $container = new Container();
        $container->add('callback', $fn1);
        
        $result = $container->get('callback');
        $this->assertSame($id, $result($id));
    }
}
