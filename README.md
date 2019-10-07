# Wiring

[![Build Status](https://travis-ci.org/ar2labs/wiring.svg?branch=master)](https://travis-ci.org/ar2labs/wiring)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=com.github.ar2labs-wiring&metric=alert_status)](https://sonarcloud.io/dashboard?id=com.github.ar2labs-wiring)
[![Coverage Status](https://coveralls.io/repos/github/ar2labs/wiring/badge.svg?branch=master&service=github)](https://coveralls.io/github/ar2labs/wiring?branch=master)
<a href="https://packagist.org/packages/ar2labs/wiring"><img src="https://poser.pugx.org/ar2labs/wiring/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/ar2labs/wiring"><img src="https://poser.pugx.org/ar2labs/wiring/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/ar2labs/wiring"><img src="https://poser.pugx.org/ar2labs/wiring/license.svg" alt="License"></a>

Wiring is a PHP micro framework core with Interoperability (PSRs).

This package is compliant with [PSR-1](https://www.php-fig.org/psr/psr-1/), [PSR-2](https://www.php-fig.org/psr/psr-2/), [PSR-3](https://www.php-fig.org/psr/psr-3/), [PSR-4](https://www.php-fig.org/psr/psr-4/), [PSR-6](https://www.php-fig.org/psr/psr-6/), [PSR-7](https://www.php-fig.org/psr/psr-7/), [PSR-11](https://www.php-fig.org/psr/psr-11/), [PSR-14](https://www.php-fig.org/psr/psr-14/), [PSR-15](https://www.php-fig.org/psr/psr-15/), [PSR-17](https://www.php-fig.org/psr/psr-17/) and [PSR-18](https://www.php-fig.org/psr/psr-18/).

## Package install

1. Via Composer

    ```bash
    composer require ar2labs/wiring
    ```
    or if you don't have a composer installation:

    [Get Composer](https://getcomposer.org/download/)

## Quick start project

1. Clone the repo:

    ```bash
    git clone https://github.com/ar2labs/wiring-start.git
    ```

2. Change to the directory created

    ```bash
    cd wiring-start/
    ```

3. Composer Install

    ```bash
    composer install
    ```

4. Create `.env`

    ```bash
    cp .env.example .env
    ```

5. Start PHP Built-in web server:

    ```bash
    php maker serve
    ```

    or run with php:

    ```bash
    php -S 127.0.0.1:8000 -t public/
    ```

6. Open your browser at:

    ```bash
    http://127.0.0.1:8000
    ```

## Requirements

The following versions of PHP are supported by this version.

* PHP 7.1
* PHP 7.2
* PHP 7.3

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

Code and documentation copyright (c) 2019, Code released under the BSD-3-Clause license.
