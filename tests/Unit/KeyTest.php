<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Romchik38\Container\Key;

final class KeyTest extends TestCase
{
    public function testConstruct(): void
    {
        $k = new Key('some_key');
        $this->assertSame('some_key', $k());
    }

    public function testConstructEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Key('');
    }
}
