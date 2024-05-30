# CHANGELOG

## [1.1.0] - 2024-05-30

### Added

- Callable
  - inside function `add` you can pass a callback as `$value` argument
  - function `get` will check if `$value` is_callable
  - if it is, then `get` will call `$value` and pass `$this` (a container instance) as an argument
