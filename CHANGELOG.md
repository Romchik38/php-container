# CHANGELOG

- [n] new  
- [!] breaking changes  
- [f] fix

## 2.0.0

- [n] added method `shared` to create shared objects
- [n] added method `fresh` to create new instance on each `get` request
- [n] added method `multi` create an object by *key*, not a class name
- [n] *promise* feature to defer object creation
- [n] cercular dependency detection
- [!] refactored method `add` - now it accepts only static values
- [!] method `get`:
  - throws an `ContainerExceptionInterface` on promised, but didn't added classes,
  - does not call a `callable`, if it was given via method `add`

## [1.1.0] - 2024-05-30

### Added

- Callable
  - inside function `add` you can pass a callback as `$value` argument
  - function `get` will check if `$value` is_callable
  - if it is, then `get` will call `$value` and pass `$this` (a container instance) as an argument
