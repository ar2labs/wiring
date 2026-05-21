# Wiring Documentation

Wiring is a small PSR-oriented PHP micro-framework core. It provides the request handling pipeline, abstract controller layers, response strategies, helper contracts, and integration points needed by an application or starter project.

This documentation is organized for maintainers and application developers who need to understand the framework behavior without reading every source file first.

## Contents

* [Getting Started](GETTING_STARTED.md) - installation, package requirements, and the basic framework building blocks.
* [Integration Guide](INTEGRATION.md) - recommended application bootstrap shape and starter package checklist.
* [Architecture](ARCHITECTURE.md) - request lifecycle, middleware flow, controllers, strategies, helpers, and security boundaries.
* [API Reference](API_REFERENCE.md) - all framework classes, interfaces, traits, and methods in `src/`.
* [PHP 8.5 Migration And Security Notes](PHP8_MIGRATION.md) - migration evidence, validation gates, and residual risks.
* [Security Policy](SECURITY.md) - supported version, reporting guidance, framework security scope, and user responsibilities.

## Package Scope

Wiring is intentionally a framework core. It does not ship a full router implementation, dependency injection container, template engine, database layer, authentication system, authorization system, CSRF storage implementation, upload handler, or outbound HTTP client. Those pieces are represented by interfaces and are expected to be supplied by the consuming application or a starter package.

## Public Extension Points

The public extension points are:

* Interfaces under `Wiring\Interfaces`.
* Abstract controller classes under `Wiring\Http\Controller`.
* PSR-15 middleware classes under `Wiring\Http\Middleware`.
* Response strategy classes under `Wiring\Strategy`.
* Aware traits under `Wiring\Traits`.
* HTTP exception classes under `Wiring\Http\Exception`.
* Helper classes under `Wiring\Http\Helpers`.

## Security Note

The framework includes safer defaults for redirects, XML parsing, error output, cookie attributes, JavaScript console encoding, logger redaction, and header emission. Application-specific security still belongs to the consuming application: validate input, use prepared SQL statements, escape output by context, enforce authorization, protect state-changing requests with CSRF controls, and configure production deployment settings carefully.