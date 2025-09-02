<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Romchik38\Container\Container;
use Romchik38\Tests\Unit\Classes\Primitive1;

final class LinkTest extends TestCase
{
    public function testLink(): void
    {
        $c = new Container();
        $c->link('k1', 'k2');
        $c->add('k2', 1);

        $result = $c->get('k1');

        $this->assertSame(1, $result);
    }

    public function testMultiLinks(): void
    {
        $c = new Container();
        $c->link('k1', 'k2');
        $c->link('k3', 'k2');
        $c->add('k2', 1);

        $result1 = $c->get('k1');
        $result3 = $c->get('k3');

        $this->assertSame(1, $result1);
        $this->assertSame(1, $result3);
    }

    public function testLinkWithClass(): void
    {
        $c = new Container();
        $c->link('k1', Primitive1::class);
        $c->shared(Primitive1::class, [1]);

        $result = $c->get('k1');

        $this->assertSame(1, $result->numb);
    }
}
