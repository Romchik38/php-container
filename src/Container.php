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
     * Adds a mixed value "as is" into the container. 
     * */
    public function add(string $key, mixed $value): void
    {
        $this->containers[$key] = new Primitive(new Key($key), $value);
    }

    public function get(string $key): mixed
    {
        // promise ready check
        if(count($this->promised) > 0) {
            foreach($this->promised as $promise) {
                $promisedClass = $promise->keyAsString();
                $checkPromise = $this->containers[$promisedClass] ?? null;
                if ($checkPromise === null) {
                    throw new ContainerException(
                        sprintf('Class %s was promised, but not configured', $promisedClass)
                    );
                }
            }
            $this->promised = [];
        }


        $result = $this->has($key);

        if ($result === false) {
            $errName = sprintf('Container with key: %s was not found', $key);
            throw new NotFoundException($errName);
        }

        $entry = $this->containers[$key];

        return $entry($this);
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->containers);
    }

    /** 
     * Creates a shared object of provided ClassName 
     * */
    public function shared(string $className, ...$params): void
    {
        $classNameVo = new ClassName($className);
        // check on re-add
        $this->chechReAdd($classNameVo());

        // check cercular and do promise
        $this->promise($classNameVo, $params);

        // create an instance
        if(count($params) > 0) {
            $this->containers[$className] = new Shared($classNameVo, $params);
        } else {
            $this->containers[$className] = new Shared($classNameVo, []);
        }
    }

    /** 
     * Creates a new copy of provided ClassName
     */
    public function fresh(string $className, ...$params): void
    {
        $classNameVo = new ClassName($className);
        // check on re-add
        $this->chechReAdd($classNameVo());

        // check cercular and do promise
        $this->promise($classNameVo, $params);

        // create an instance
        if(count($params) > 0) {
            $this->containers[$className] = new Fresh($classNameVo, $params);
        } else {
            $this->containers[$className] = new Fresh($classNameVo, []);
        }
    }

    /**
     * Creates a shared instance by provided Key (not class name)
     */
    public function multi(
        string $className,
        Key $key,
        ...$params
    ): void {
        $classNameVo = new ClassName($className);
        // check on re-add
        $this->chechReAdd($key());

        // check cercular and do promise
        $this->promise($classNameVo, $params);

        // create an instance
        if(count($params) > 0) {
            $this->containers[$key()] = new Multi($classNameVo, $params, $key);
        } else {
            $this->containers[$key()] = new Multi($classNameVo, [], $key);
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

    /** Notices a dependency to check in future before `get` call */
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

    /** Cercular check */
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
