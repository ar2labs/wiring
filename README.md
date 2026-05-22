# Wiring

[![Build](https://github.com/ar2labs/wiring/actions/workflows/build.yml/badge.svg)](https://github.com/ar2labs/wiring/actions/workflows/build.yml)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=ar2labs_wiring&metric=alert_status)](https://sonarcloud.io/project/overview?id=ar2labs_wiring&branch=master)
[![Coverage Status](https://coveralls.io/repos/github/ar2labs/wiring/badge.svg?branch=master&service=github)](https://coveralls.io/github/ar2labs/wiring?branch=master)
<a href="https://packagist.org/packages/ar2labs/wiring"><img src="https://poser.pugx.org/ar2labs/wiring/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/ar2labs/wiring"><img src="https://poser.pugx.org/ar2labs/wiring/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://github.com/ar2labs/wiring/blob/master/LICENSE.md"><img src="https://poser.pugx.org/ar2labs/wiring/license.svg" alt="License"></a>

Wiring is a PHP micro framework core with Interoperability (PSRs).

This package is compliant with [PSR-1](https://www.php-fig.org/psr/psr-1/), [PSR-3](https://www.php-fig.org/psr/psr-3/), [PSR-4](https://www.php-fig.org/psr/psr-4/), [PSR-6](https://www.php-fig.org/psr/psr-6/), [PSR-7](https://www.php-fig.org/psr/psr-7/), [PSR-11](https://www.php-fig.org/psr/psr-11/), [PSR-12](https://www.php-fig.org/psr/psr-12/), [PSR-14](https://www.php-fig.org/psr/psr-14/), [PSR-15](https://www.php-fig.org/psr/psr-15/), [PSR-17](https://www.php-fig.org/psr/psr-17/) and [PSR-18](https://www.php-fig.org/psr/psr-18/).

## Package install

1. Via Composer

    ```bash
    composer require ar2labs/wiring
    ```
    or if you don't have a composer installation:

    [Get Composer](https://getcomposer.org/download/)

## Quick start project

1. Create a start project:

    ```bash
    composer create-project ar2labs/wiring-start
    ```

2. Change to the directory created

    ```bash
    cd wiring-start/
    ```

3. Create `.env`

    ```bash
    cp .env.example .env
    ```

4. Start PHP Built-in web server:

    ```bash
    php maker serve
    ```

    or run with php:

    ```bash
    php -S 127.0.0.1:8000 -t public/
    ```

5. Open your browser at:

    ```bash
    http://127.0.0.1:8000
    ```

## Requirements

The following versions of PHP are supported by this version.

* PHP 8.5

PHP Extension Requirements:

* JSON
* Mbstring

Development and CI checks also use Composer, PHPUnit, PHPStan, PHP-CS-Fixer, DOM/XML extensions, and Xdebug for Clover coverage generation.

## Quality gates

The project is expected to pass these release checks:

```bash
composer validate --strict
composer install
composer audit --locked --abandoned=fail
composer check-platform-reqs
vendor/bin/php-cs-fixer fix --config=php_cs.dist --dry-run --diff --no-interaction
vendor/bin/phpstan analyse --configuration phpstan.neon --no-progress --ansi
vendor/bin/phpunit --configuration phpunit.xml.dist --colors=always
vendor/bin/phpunit --configuration phpunit.xml.dist --coverage-clover build/logs/clover.xml --colors=always
```

## Security posture

Wiring is a micro-framework core. Authentication, authorization, CSRF token storage, session storage, database query execution, template escaping policy, and application-specific input validation are provided by consuming applications through interfaces and injected services.

Security-sensitive defaults in this package include relative-only controller redirects, XML request parsing with network access disabled, JavaScript-safe console helper encoding, generic production error messages, redaction of sensitive logger context values, CR/LF rejection before emitting headers, and cookies with `HttpOnly` plus `SameSite=Lax` by default. Cookie `Secure` is set automatically when PHP reports an HTTPS request; deployments behind reverse proxies should pass `true` explicitly or normalize HTTPS server parameters before setting cookies.

Residual deployment assumptions include HTTPS termination, HSTS policy, trusted reverse-proxy headers, cookie domain scope, production `php.ini`, secret management, template escaping in the configured renderer, prepared SQL statements in database implementations, CSRF enforcement in applications that process state-changing forms, and operational log retention/redaction controls.

## Documentation

Project documentation is available in [docs/README.md](docs/README.md).

* [Getting Started](docs/GETTING_STARTED.md)
* [Integration Guide](docs/INTEGRATION.md)
* [Architecture](docs/ARCHITECTURE.md)
* [API Reference](docs/API_REFERENCE.md)
* [PHP 8.5 Migration And Security Notes](docs/PHP8_MIGRATION.md)
* [Security Policy](docs/SECURITY.md)
* [Release Process](docs/RELEASE.md)

## Copyright and license

Code and documentation copyright (c) 2020, Code released under the <a href="https://github.com/ar2labs/wiring/blob/master/LICENSE.md">BSD-3-Clause license</a>.
