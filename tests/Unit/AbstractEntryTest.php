<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Romchik38\Container\AbstractEntry;
use Romchik38\Container\ClassName;
use Romchik38\Container\Container;
use Romchik38\Container\Promise;

final class AbstractEntryTest extends TestCase
{
    public function testParams(): void
    {
        $className = new ClassName('\Romchik38\Tests\Unit\Classes\Primitive1');
        $params = [1];

        $a = $this->create($className, $params, true);

        $this->assertSame($params, $a->params());
    }

    /** shared instance */
    public function testInvokeShared(): void
    {
        $className = new ClassName('\Romchik38\Tests\Unit\Classes\Primitive1');
        $params = [1];

        $a = $this->create($className, $params, true);
        
        $c = new Container();

        $i1 = $a($c);
        $i2 = $a($c);

        $this->assertSame($i1, $i2);
        $this->assertSame($i1->numb, $i2->numb);
    }

    /** fresh instance */
    public function testInvokeFresh(): void
    {
        $className = new ClassName('\Romchik38\Tests\Unit\Classes\Primitive1');
        $params = [1];

        $a = $this->create($className, $params, false);
        
        $c = new Container();

        $i1 = $a($c);
        $i2 = $a($c);

        $this->assertNotSame($i1, $i2);
        $this->assertSame($i1->numb, $i2->numb);
    }

    public function testInvokeWithPromise(): void
    {
        $className = new ClassName('\Romchik38\Tests\Unit\Classes\OnOtherClass2');
        $params = [
            'some_string',
            new Promise('\Romchik38\Tests\Unit\Classes\Primitive1')
        ];

        $c = new Container();
        $c->shared('\Romchik38\Tests\Unit\Classes\Primitive1', [1]);

        $a = $this->create($className, $params, true);

        $i1 = $a($c);

        $this->assertSame('some_string', $i1->str);
        $this->assertSame(1, $i1->depPromitive1->numb);
    }

    protected function create(
        ClassName $className,
        array $params,
        bool $isShared
    ): AbstractEntry
    {
        return new class(
            $className,
            $params,
            $isShared
        ) extends AbstractEntry
        {
            public function key(): string
            {
                return 'some_key';
            }            
        };
    }
}
