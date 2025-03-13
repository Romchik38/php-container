<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Romchik38\Container\Promise;

final class PromiseTest extends TestCase
{
    public function testKey(): void
    {
        $pr = new Promise('some_key');
        $this->assertSame('some_key', ($pr->key())());
    }

    public function testKeyAsString(): void
    {
        $pr = new Promise('some_key');
        $this->assertSame('some_key', $pr->keyAsString());
    }
}
