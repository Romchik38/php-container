<?php

declare(strict_types=1);

namespace Romchik38\Container;

use InvalidArgumentException;

use function class_exists;
use function sprintf;

/** @internal */
final class ClassName
{
    public readonly string $className;

    public function __construct(string $className)
    {
        if (class_exists($className)) {
            $this->className = $className;
        } else {
            throw new InvalidArgumentException(
                sprintf('Class %s does not exist', $className)
            );
        }
    }

    public function __invoke(): string
    {
        return $this->className;
    }
}
