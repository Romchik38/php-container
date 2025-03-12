<?php 

declare(strict_types=1);

/**
 * Implemetation of psr-11 container
 * 
 * Php version 8.3
 * 
 * @category Psr-11
 * @package  Container
 * @author   Romchik38 <pomahehko.c@gmail.com>
 * @license  MIT https://opensource.org/license/mit/
 * @link     no link
 */

namespace Romchik38\Container;

use Psr\Container\ContainerInterface;

/**
 * Implemetation of psr-11 container
 */
class Container implements ContainerInterface
{
    protected array $containers;

    public function get(string $id): mixed
    {

        $result = $this->has($id);

        if ($result === false) {
            $errName = 'Container with id: "' . $id . '" was not found';
            throw new NotFoundException($errName);
        }

        $entry = $this->containers[$id];

        if (is_callable($entry)) {
            return $entry($this);
        }

        return $this->containers[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->containers);
    }
}
