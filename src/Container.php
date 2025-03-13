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
 * @throws ContainerExceptionInterface
 */
class Container implements ContainerInterface
{
    /** 
     * Ready to use containers 
     * 
     * @var array<string,EntryInterface>
     * */
    protected array $containers = [];

    /** 
     * Promised entries
     * 
     * @var array<int, Promise> 
     * */
    protected array $promised = [];

    /** 
     * Any primitive or object type to store in a container. 
     * The given value will not be proccessed by container and will be returned as is 
     * */
    public function add(string $id, mixed $value): void
    {
        $this->containers[$id] = $value;
    }

    public function get(string $id): mixed
    {
        // promise ready check
        if(count($this->promised) > 0) {
            foreach($this->promised as $promise) {
                $promisedClass = $promise->key;
                $checkPromise = $this->containers[$promisedClass] ?? null;
                if ($checkPromise === null) {
                    throw new ContainerException(
                        sprintf('Class %s was promised, but not configured', $promisedClass)
                    );
                }
            }
            $this->promised = [];
        }


        $result = $this->has($id);

        if ($result === false) {
            $errName = 'Container with id: "' . $id . '" was not found';
            throw new NotFoundException($errName);
        }

        $entry = $this->containers[$id];

        return $entry($this);
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->containers);
    }

    /** store config about shared objects */
    public function shared(ClassName $className, ...$params): void
    {
        // check on re-add
        $isAdded = $this->containers[$className()] ?? null;
        if($isAdded !== null) {
            throw new ContainerException(
                sprintf('%s was already added', $className())
            );
        }

        // check cercular and do promise
        $this->promise($className, $params);

        // create a shared
        if(count($params) > 0) {
            $this->containers[$className()] = new Shared($className, $params);
        } else {
            $this->containers[$className()] = new Shared($className, []);
        }
        
    }

    protected function promise(ClassName $className, $params): void
    {
        $list = [];
        foreach($params as $param) {
            if ($param instanceof Promise) {
                $this->promised[] = $param;
                $result = $this->checkDependency(
                    $className(), 
                    $param->key, 
                    $list
                );
                array_merge($list, $result);
            }
        }
    }

    protected function checkDependency(
        string $target, 
        string $candidate, 
        array $checked
    ): array
    {
        if ($target === $candidate) {
            throw new ContainerException(sprintf(
                'cercular found: %s checked: %s',
                $target,
                implode(', ', $checked)
            ));
        }
    
        $candidateInstance = $this->containers[$candidate] ?? null;
        if ($candidateInstance === null) {
            return $checked;
        }
    
        $checked[] = $candidate;
    
        foreach($candidateInstance->params() as $candidateDep) {
            if ($candidateDep instanceof Promise) {
                $checked = $this->checkDependency($target, $candidateDep->key, $checked);
            }
        }
    
        return $checked;
    }
}
