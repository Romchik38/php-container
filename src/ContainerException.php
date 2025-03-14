<?php

declare(strict_types=1);

namespace Romchik38\Container;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class ContainerException extends RuntimeException implements ContainerExceptionInterface
{
}
