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
     * Add a mixed value as is into the container. 
     * */
    public function add(string $id, mixed $value): void
    {
        $this->containers[$id] = new Primitive(new Key($id), $value);
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

    /** 
     * Creates a shared object of provided ClassName 
     * */
    public function shared(ClassName $className, ...$params): void
    {
        // check on re-add
        $this->chechReAdd($className());

        // check cercular and do promise
        $this->promise($className, $params);

        // create an instance
        if(count($params) > 0) {
            $this->containers[$className()] = new Shared($className, $params);
        } else {
            $this->containers[$className()] = new Shared($className, []);
        }
    }

    /** 
     * Creates a new copy of provided ClassName
     */
    public function fresh(ClassName $className, ...$params): void
    {
        // check on re-add
        $this->chechReAdd($className());

        // check cercular and do promise
        $this->promise($className, $params);

        // create an instance
        if(count($params) > 0) {
            $this->containers[$className()] = new Fresh($className, $params);
        } else {
            $this->containers[$className()] = new Fresh($className, []);
        }
    }

    /**
     * Creates a shared instance by provided Key
     */
    public function multi(
        ClassName $className,
        Key $key,
        ...$params
    ): void {
        // check on re-add
        $this->chechReAdd($key());

        // check cercular and do promise
        $this->promise($key, $params);

        // create an instance
        if(count($params) > 0) {
            $this->containers[$key()] = new Multi($className, $params, $key);
        } else {
            $this->containers[$key()] = new Multi($className, [], $key);
        }
    }

    /** @throws ContainerException */
    protected function chechReAdd(string $key): void
    {
        $isAdded = $this->containers[$key] ?? null;
        if($isAdded !== null) {
            throw new ContainerException(
                sprintf('Container key %s was already added', $key)
            );
        }
    }

    protected function promise(callable $key, $params): void
    {
        $list = [];
        foreach($params as $param) {
            if ($param instanceof Promise) {
                $this->promised[] = $param;
                $result = $this->checkDependency(
                    $key(), 
                    $param->keyAsString(), 
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
    ): array {
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
                $checked = $this->checkDependency($target, $candidateDep->keyAsString(), $checked);
            }
        }
    
        return $checked;
    }
}
