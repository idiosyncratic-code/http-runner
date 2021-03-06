# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2.0] 2019-11-30
### Changed
- PSR Logger implementation now required for logging uncaught exceptions
- Removed ErrorResponseFactory interface

## [0.1.1] 2019-04-23
### Changed
- Removed unused dev dependencies
- Removed extraneous second parameter when emitting headers

## [0.1.0] 2019-04-23
### Added
- Basic installation and usage documentation
- Initial implementation of `Idiosyncratic\Http\Runner\Runner` class and required interfaces
- Basic `Idiosyncratic\Http\Runner\PhpSapiResponseEmitter` class for emitting responses in a traditional PHP SAPI environment
