<?php

namespace Romchik38\Container;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends \RuntimeException 
    implements NotFoundExceptionInterface
{
}