# Wiring

[![Build Status](https://travis-ci.org/ar2labs/wiring.svg?branch=master)](https://travis-ci.org/ar2labs/wiring)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=com.github.ar2labs-wiring&metric=alert_status)](https://sonarcloud.io/dashboard?id=com.github.ar2labs-wiring)
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

* PHP 7.2
* PHP 7.3
* PHP 7.4

PHP Extension Requirements:

* CMath
* Ctype
* JSON
* Mbstring
* OpenSSL
* PDO
* Tokenizer
* XML

## Documentation

Contribute to this documentation. ;)

## Copyright and license

Code and documentation copyright (c) 2020, Code released under the <a href="https://github.com/ar2labs/wiring/blob/master/LICENSE.md">BSD-3-Clause license</a>.
