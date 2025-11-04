<?php

/**
 * Implemetation of psr-11 container
 *
 * Php version 8.3
 *
 * @link     no link
 */

declare(strict_types=1);

namespace Romchik38\Container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function array_key_exists;
use function array_merge;
use function count;
use function implode;
use function sprintf;

/**
 * Implemetation of psr-11 container
 *
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

    public function __construct(
        public readonly bool $isLazy = false
    ) {
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function get(string $key): mixed
    {
        // promise ready check
        if (count($this->promised) > 0) {
            foreach ($this->promised as $promise) {
                $promisedClass = $promise->keyAsString();
                $checkPromise  = $this->containers[$promisedClass] ?? null;
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
     * Links a key with another key.
     * */
    public function link(string $key, string $promisedKey): void
    {
        // add to Promise
        $promise                = new Promise($promisedKey);
        $this->promised[]       = $promise;
        $this->containers[$key] = new Link(new Key($key), $promise);
    }

    /**
     * Creates a shared object of provided ClassName
     *
     * @param array<int,mixed> $params
     * @throws ContainerExceptionInterface - On re-add.
     * @throws ContainerExceptionInterface - On cercular dependency.
     * */
    public function shared(string $className, array $params = []): void
    {
        $classNameVo = new ClassName($className);
        // check on re-add
        $this->chechReAdd($classNameVo());

        // check cercular and do promise
        $this->promise($classNameVo, $params);

        // create an instance
        $this->containers[$className] = new Shared($classNameVo, $params);
    }

    /**
     * Creates a new copy of provided ClassName
     *
     * @param array<int,mixed> $params
     * @throws ContainerExceptionInterface - On re-add.
     * @throws ContainerExceptionInterface - On cercular dependency.
     */
    public function fresh(string $className, array $params = []): void
    {
        $classNameVo = new ClassName($className);
        // check on re-add
        $this->chechReAdd($classNameVo());

        // check cercular and do promise
        $this->promise($classNameVo, $params);

        // create an instance
        $this->containers[$className] = new Fresh($classNameVo, $params);
    }

    /**
     * Creates a shared instance by provided Key (not class name)
     *
     * @param array<int,mixed> $params
     * @throws ContainerExceptionInterface - On re-add.
     * @throws ContainerExceptionInterface - On cercular dependency.
     */
    public function multi(
        string $className,
        string $key,
        bool $isShared = true,
        array $params = []
    ): void {
        $classNameVo = new ClassName($className);
        $keyVo       = new Key($key);
        // check on re-add
        $this->chechReAdd($keyVo());

        // check cercular and do promise
        $this->promise($classNameVo, $params);

        // create an instance
        $this->containers[$keyVo()] = new Multi($classNameVo, $params, $keyVo, $isShared);
    }

    /** @throws ContainerExceptionInterface */
    protected function chechReAdd(string $key): void
    {
        $isAdded = $this->containers[$key] ?? null;
        if ($isAdded !== null) {
            throw new ContainerException(
                sprintf('Container key %s was already added', $key)
            );
        }
    }

    /**
     * Notices a dependency to check in future before `get` call
     *
     * @param array<int, mixed> $params
     * @throws ContainerExceptionInterface - On cercular dependency.
     * */
    protected function promise(callable $key, array $params): void
    {
        $list = [];
        foreach ($params as $param) {
            if ($param instanceof Promise) {
                $this->promised[] = $param;
                $result           = $this->checkDependency(
                    $key(),
                    $param->keyAsString(),
                    $list
                );
                $list             = array_merge($list, $result);
            }
        }
    }

    /**
     * Cercular check
     *
     * @param array<int,string> $checked
     * @throws ContainerExceptionInterface - On cercular dependency.
     * @return array<int,string>
     * */
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

        foreach ($candidateInstance->params() as $candidateDep) {
            if ($candidateDep instanceof Promise) {
                $checked = $this->checkDependency($target, $candidateDep->keyAsString(), $checked);
            }
        }

        return $checked;
    }
}
