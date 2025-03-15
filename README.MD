# DI Container

![status](https://placehold.co/15x15/10b223/10b223.png) `status: ready to use`
![phpstan](https://placehold.co/15x15/1589F0/1589F0.png) `phpstan: level 8`
![phpunit](https://placehold.co/15x15/0dbc79/0dbc79.png) `phpunit: full`

The `Container` helps configure an application. The implementation is compatible with PSR-11 `ContainerInterface`.

Main features:

- stores values *as is* (primitive, objects etc).
- creates a *shared* object (singleton).
- creates a *fresh* copy of the same class on each call.
- *multi* ability to create a different instance of the same class by provided keys.
- *promises* - container creates an instance only on a `get` call, to avoid unused creations.
- can detect a circular dependency

## Latest version

v2.0.0

## Install

`composer install romchik38/php-container`

## Quick examples

### Primitive

`Primitive` class is responsible to hold a static `mixed` value. Use method `add` to sore it.

```php
$container = new \Romchik38\Container\Container();
$container->add('some key', 'any string');
$container->add('config', ['key' => 0]);
$container->add('important_object', new ImposrtantClass());
// somehere in the code
$str = $container->get('some key');
// ...etc
```

### Shared

`Shared` class is responsible to create only one instance of concrete class and returns it on each `get` request. Use method `shared` there.

```php
$container = new \Romchik38\Container\Container();
$container->shared(
    '\Classes\Primitive1',                 // class name
    [7]                                    // params, number 7 in this case
);
// $shared1 and $shared2 are the same
$shared1 = $container->get('\Classes\Primitive1');
$shared2 = $container->get('\Classes\Primitive1');
```

### Fresh

`Fresh` class is responsible to create a new instance of concrete class on each `get` request. Use method `fresh` of the *container*.

```php
$container = new \Romchik38\Container\Container();
$container->fresh(
    '\Classes\Primitive1',                  // class name
    [7]                                     // params, number 7 in this case
);
// $fresh1 and $fresh2 are defferent objects that hold the same number 7
$fresh1 = $container->get('\Classes\Primitive1');
$fresh2 = $container->get('\Classes\Primitive1');
```

### Multi

`Multi` class is responsible to create a new (or shared) copy of a concrete class with given *key*. It maght be usefull when dealing with interfaces or creating a few same classes with different configuration. Use method `multi`.

```php
// Interface example
$container = new \Romchik38\Container\Container();
$container->multi(
    '\DB\DatabaseUseSql',
    '\DatabaseInterface',
    true,                           // true - shared, false - fresh
    ['dsn:localhost']               // params
);

$database = $container->get('\DatabaseInterface');
```

## Promises

`Promise` is responsible to defer a creation of the class's dependency. It used when a class has another class as a dependency.

Promises can be used with methods `shared`, `fresh` and `multi`.

```php
$container = new \Romchik38\Container\Container();
$container->shared(
    '\DB\Database', 
    [
        'db:mysql',                         // first param as a string
        new Promise('\SomeConnection'),     // second param as a promise
    ]
);
```

In the example above, we *promise* to add a class `\SomeConnection` to the container. `\SomeConnection` is a class name, but it can be any *key*, used with `multi` method. In other words we pass a *container key* as a param.

Do not forget to add a `\SomeConnection`

```php
$container->fresh(
    '\SomeConnection',     // class name
    []                     // params, in that case is nothing
);
```

### Circular dependency detection

We can primise  to add the first class to another, and a the second class to the first and so on. Promise chain can be as long as you want. But if you try to add a class as a *promise* which depends on any early promised classes an exception will be thrown.

```php
$container = new \Romchik38\Container\Container();

$container->shared(
    '\ClassA1', 
    [new Promise('\ClassA2')]
);

$container->shared(
    '\ClassA2', 
    [new Promise('\ClassA1')]
);
// Exception there
```

## Code quality

- phpstan level 8
  - ![passes](https://placehold.co/15x15/0dbc79/0dbc79.png)`[OK] No errors`  
- phpunit
  - ![passes](https://placehold.co/15x15/0dbc79/0dbc79.png)`OK (36 tests, 54 assertions)`
  - tested partially
- laminas-coding-standard
  - ![passes](https://placehold.co/15x15/0dbc79/0dbc79.png)`32 / 32 (100%)`
