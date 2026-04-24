# Laravel Tabler

[![Tests](https://github.com/MimisK13/laravel-tabler/actions/workflows/tests.yml/badge.svg)](https://github.com/MimisK13/laravel-tabler/actions/workflows/tests.yml)
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

`mimisk13/laravel-tabler` is a Laravel UI scaffolding package based on Tabler.

It installs a ready starter layout, demo pages, Blade components, and route/vite stubs so you can start quickly with a Tabler-style admin UI.

## Requirements

- PHP 8.2+
- Laravel 10, 11, 12, or 13

## Installation

``` bash
composer require mimisk13/laravel-tabler
```

## Usage

```bash
php artisan tabler:install
```

### What the install command does

- Copies Blade views from package stubs to `resources/views`
- Copies route stub to `routes/web.php`
- Copies `vite.config.js` stub to project root
- Adds frontend dependencies in `package.json` (`@tabler/core`, `vite-plugin-static-copy`)
- Runs frontend install/build command (`npm`, `yarn`, or `pnpm` depending on lock file)

### Scaffolded pages and routes

Installed route stub includes:

- `GET /` -> `dashboard` view
- `GET /empty` -> `empty` view (`empty` route name)
- `GET /license` -> `license` view (`license` route name)

### Included Blade components

The package stubs include reusable components under `resources/views/components`, including:

- alerts
- badges
- buttons
- cards
- forms/inputs
- table wrappers (`table`, `th`, `td`)
- icons
- empty state blocks
- loading spinner

Example usage:

```blade
<x-alert />

<x-badge class="bg-blue">Active</x-badge>

<x-button route="{{ route('license') }}">
    View License
</x-button>

<x-input name="email" type="email" label="Email" required />

<x-empty
    title="No records"
    message="There are no items yet."
    button_label="Create item"
    button_route="{{ url('/') }}"
/>
```

## Notes

- `tabler:install` is scaffolding-oriented and will overwrite target files like `routes/web.php` and `vite.config.js` with package stubs.
- Review generated files before running in an existing production project.

## Change log

Please see the [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details and a todolist.

## Credits

- [MimisK][link-author]
- [All Contributors][link-contributors]

## License
The MIT License (MIT). Please see [license file](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/mimisk13/laravel-tabler.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/mimisk13/laravel-tabler.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/mimisk13/laravel-tabler/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/mimisk13/laravel-tabler
[link-downloads]: https://packagist.org/packages/mimisk13/laravel-tabler
[link-travis]: https://travis-ci.org/mimisk13/laravel-tabler
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/mimisk13
[link-contributors]: ../../contributors
