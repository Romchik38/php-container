<?php

namespace Romchik38\Container;

use Psr\Container\NotFoundExceptionInterface;

// class NotFoundException extends \Exception
// {
// }

// class NotFoundException implements NotFoundExceptionInterface
// {
// }

class NotFoundException extends \Exception implements NotFoundExceptionInterface
{
}