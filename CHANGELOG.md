# Changelog

All notable changes to `Laravel Tabler` will be documented in this file.

## Version 0.1.0

### Added
- GitHub Actions test workflow with Laravel matrix support (10, 11, 12, 13).
- Dependabot auto-merge workflow for development dependencies (minor/patch only).
- Package test suite for:
  - service provider loading
  - command registration
  - install command behavior and generated scaffold sanity checks
  - Blade stub compilation smoke coverage

### Changed
- Updated package constraints for broad framework support:
  - `illuminate/support` now supports Laravel `^10|^11|^12|^13`
  - `orchestra/testbench` and `phpunit` ranges updated accordingly
- Updated install scaffolding dependency ranges:
  - `@tabler/core` to `^1.0`
  - `vite-plugin-static-copy` to `^4.0`
- README expanded with:
  - package purpose and behavior
  - scaffolded pages/routes documentation
  - included components and usage examples
  - install command notes and overwrite warning

## Version 0.0.1

### Added
- Everything
