# Changelog

All notable changes to this project should be documented in this file.

The format follows Keep a Changelog conventions, and this project uses Semantic Versioning for public API changes.

## [Unreleased]

### Added

* Official integration guide for application bootstrapping and starter-package structure.
* Router middleware support for converting Wiring HTTP exceptions into PSR-7 responses when a PSR-17 response factory is configured.
* CI coverage for locked dependencies on Ubuntu and Windows, plus a lowest-supported-dependencies job on Ubuntu.
* Release governance documentation with SemVer, upgrade, and validation guidance.

### Changed

* Request handling is explicit: normal middleware runs during `Application::run()`, and after middleware runs during `Application::stop()`.
* Response strategies clear pending render/write state after producing a response.
* Internal middleware adapter properties use native type declarations where this does not narrow public extension contracts.
* JSON and XML request body parsing now distinguishes empty bodies from invalid structured payloads.

### Security

* Fallback error responses use a generic 500 message when no application error handler is registered.
* Error response content-type headers are applied using PSR-7 immutable response semantics.
* JSON controller throwable handling now returns an explicit JSON 500 response.

### Documentation

* API reference updated for middleware lifecycle, router exception adaptation, strategy reset behavior, input parsing behavior, and generic fallback errors.
