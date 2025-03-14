<?php

declare(strict_types=1);

namespace Romchik38\Container;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class NotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
}
