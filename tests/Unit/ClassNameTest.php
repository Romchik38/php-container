<?php

declare(strict_types=1);

namespace Romchik38\Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Romchik38\Container\ClassName;
use stdClass;

final class ClassNameTest extends TestCase
{
    public function testConstructNotExistingClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ClassName('Not exist');
    }

    public function testConstruct(): void
    {
        $someClass = new stdClass();
        $cl        = new ClassName($someClass::class);
        $this->assertSame($someClass::class, $cl->className);
    }
}
