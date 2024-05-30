<?php declare(strict_types=1);

/**
 * Implemetation of psr-11 container
 * 
 * Php version 8.2
 * 
 * @category Psr-11
 * @package  Container
 * @author   Romchik38 <pomahehko.c@gmail.com>
 * @license  MIT https://opensource.org/license/mit/
 * @link     no link
 */

namespace Romchik38;

use Psr\Container\ContainerInterface;
use Romchik38\NotFoundException;

/**
 * Implemetation of psr-11 container
 */
class Container implements ContainerInterface
{
    private $__containers = [];

    public function add(string $id, $value)
    {
        $this->__containers[$id] = $value;
    }
    public function get(string $id): mixed
    {

        $result = $this->has($id);

        if ($result === false) {
            $errName = 'Container with id: "' . $id . '" was not found';
            throw new NotFoundException($errName);
        }

        $entry = $this->__containers[$id];

        if (is_callable($entry)) {
            return $entry($this);
        }

        return $this->__containers[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->__containers);
    }
}
