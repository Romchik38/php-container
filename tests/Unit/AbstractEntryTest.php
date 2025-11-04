<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Romchik38\Container\AbstractEntry;
use Romchik38\Container\ClassName;
use Romchik38\Container\Container;
use Romchik38\Container\Promise;
use Romchik38\Tests\Unit\Classes\OnOtherClass2;
use Romchik38\Tests\Unit\Classes\Primitive1;

final class AbstractEntryTest extends TestCase
{
    public function testParams(): void
    {
        $className = new ClassName(Primitive1::class);
        $params    = [1];

        $a = $this->create($className, $params, true);

        $this->assertSame($params, $a->params());
    }

    /** shared instance */
    public function testInvokeShared(): void
    {
        $className = new ClassName(Primitive1::class);
        $params    = [1];

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
        $className = new ClassName(Primitive1::class);
        $params    = [1];

        $a = $this->create($className, $params, false);

        $c = new Container();

        $i1 = $a($c);
        $i2 = $a($c);

        $this->assertNotSame($i1, $i2);
        $this->assertSame($i1->numb, $i2->numb);
    }

    public function testInvokeWithPromise(): void
    {
        $className = new ClassName(OnOtherClass2::class);
        $params    = [
            'some_string',
            new Promise(Primitive1::class),
        ];

        $c = new Container();
        $c->shared(Primitive1::class, [1]);

        $a = $this->create($className, $params, true);

        $i1 = $a($c);

        $this->assertSame('some_string', $i1->str);
        $this->assertSame(1, $i1->depPromitive1->numb);
    }

    public function testLazyAsShared(): void
    {
        $className = new ClassName(Primitive1::class);
        $params    = [1];

        $a = $this->create($className, $params, true, true);

        $c = new Container(true);

        $i1 = $a($c);

        $reflectionClass = new ReflectionClass(Primitive1::class);

        $this->assertTrue($reflectionClass->isUninitializedLazyObject($i1));
        $this->assertSame(1, $i1->numb);
        $this->assertFalse($reflectionClass->isUninitializedLazyObject($i1));
    }

    /** Not Lazy */
    protected function create(
        ClassName $className,
        array $params,
        bool $isShared,
        bool $isLazy = false
    ): AbstractEntry {
        return new class (
            $className,
            $params,
            $isShared,
            $isLazy
        ) extends AbstractEntry
        {
            public function key(): string
            {
                return 'some_key';
            }
        };
    }
}
