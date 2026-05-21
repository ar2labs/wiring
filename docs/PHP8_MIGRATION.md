# PHP 8.5 Migration And Security Notes

This file tracks the PHP 8.5 migration and security-hardening work for Wiring. It is an evidence log, not a guarantee that an application using Wiring is free from vulnerabilities.

## Scope

Wiring is a PSR-oriented micro-framework core. Public APIs and extension points are the interfaces in `src/Interfaces`, aware traits in `src/Traits`, controller base classes, request/middleware lifecycle, response strategies, helpers, and HTTP exception types.

Authentication, authorization, CSRF storage, session lifecycle, database queries, template rendering, file uploads, outbound HTTP clients, and application-specific validation are mostly contracts or consuming-application responsibilities in this repository.

## PHP 8.5 Compatibility

Completed checks and changes:

* `composer.json` requires `php: ^8.5`.
* Runtime and test files use `declare(strict_types=1)` where safe.
* Weak equality checks in runtime code were replaced with strict comparisons.
* Request body parsing now reads the PSR-7 stream once and reuses the captured body for JSON/XML decoding.
* XML decoding uses libxml internal errors and `LIBXML_NONET` to avoid warning leakage and network access during parsing.
* Public method names, namespaces, route invocation flow, strategy contracts, exception classes, and middleware lifecycle were preserved except where redirect validation intentionally rejects unsafe `Location` values.

No PHP 8.5-only syntax was adopted because the current code benefits more from a small compatibility patch than from new-language-feature churn.

## Security Hardening

Areas reviewed against OWASP Top 10, ASVS Level 2 themes, and OWASP cheat-sheet guidance:

* Input parsing: JSON, XML, form/query helpers.
* Output encoding: HTML error output, JSON strategy output, browser-console helper output.
* Redirects and headers: controller redirects and emitter header output.
* Cookies and sessions: cookie attributes and session helper boundaries.
* Logging and errors: production error output and logger context redaction.
* Interfaces delegated to applications: auth, authorization, CSRF, database/query execution, validation, and template rendering.

Fixes applied:

* Controller redirects reject empty, absolute, protocol-relative, backslash-containing, and CR/LF-injected URLs.
* XML entity expansion does not expose local file contents in the request parser regression test.
* Production error responses use generic or caller-provided messages rather than raw exception messages.
* HTML error output uses explicit UTF-8 `htmlspecialchars` flags.
* Logger messages and context redact common secret-bearing keys and key-value patterns.
* Console helper output uses JSON hex encoding for JavaScript string/object contexts.
* Cookie helper uses PHP's options array with `HttpOnly`, `SameSite=Lax`, and automatic `Secure` on HTTPS.
* Emitter rejects response headers, protocol versions, and reason phrases containing line breaks before calling `header()`.

## Automated Gates

Expected local and CI gates:

* `php -v`
* `composer validate --strict`
* `composer install`
* `composer audit --locked --abandoned=fail`
* `composer check-platform-reqs`
* `vendor/bin/php-cs-fixer fix --config=php_cs.dist --dry-run --diff --no-interaction`
* `vendor/bin/phpstan analyse --configuration phpstan.neon --no-progress --ansi`
* `vendor/bin/phpunit --configuration phpunit.xml.dist --colors=always`
* `vendor/bin/phpunit --configuration phpunit.xml.dist --coverage-clover build/logs/clover.xml --colors=always`

CI uses PHP 8.5, Composer install from the lock file, Composer audit, platform checks, PHP-CS-Fixer dry run, PHPStan, PHPUnit, Clover coverage generation, and GitHub dependency review for pull requests.

## Validation Evidence

Performed on 2026-05-21 in the local workspace:

* `php -v`: passed with PHP 8.5.6 CLI, Xdebug 3.5.1, and OPcache.
* `composer validate --strict`: passed.
* `composer update`: completed; final direct dependencies are current under constraints.
* `composer install --no-interaction --no-progress --prefer-dist`: passed with the lock file.
* `composer audit --locked --abandoned=fail`: passed with no vulnerability advisories or abandoned-package failures.
* `composer check-platform-reqs`: passed.
* `composer outdated --direct`: passed; all direct dependencies are up to date.
* `composer normalize --dry-run`: skipped because the Composer normalize command is not installed.
* `vendor/bin/php-cs-fixer fix --config=php_cs.dist --dry-run --diff --no-interaction`: passed; 0 of 91 files can be fixed.
* `vendor/bin/phpstan analyse --configuration phpstan.neon --no-progress --ansi`: passed with no errors.
* `vendor/bin/phpunit --configuration phpunit.xml.dist --colors=always`: passed with 63 tests and 277 assertions on PHPUnit 13.1.11.
* `vendor/bin/phpunit --configuration phpunit.xml.dist --coverage-clover build/logs/clover.xml --colors=always`: passed and generated `build/logs/clover.xml`.
* Clover metrics: 695/695 statements, 167/167 methods, and 862/862 elements covered across 66 source files.
* Optional local scanners `gitleaks`, `trufflehog`, and `semgrep`: skipped because the tools are not installed in this environment.

## Residual Risks And Assumptions

* Consuming applications must validate route, query, body, header, CLI, and upload inputs for their own domains.
* Template engines must apply context-aware escaping for HTML, attributes, JavaScript, CSS, URLs, XML, and raw output policies.
* Database implementations must use prepared statements or parameterized query APIs.
* Auth, authorization, CSRF, password hashing, token storage, and session fixation controls are interface-level responsibilities here and need implementation-level review.
* File upload, filesystem path normalization, SSRF prevention, and outbound URL allowlists are out of scope unless added by consuming applications.
* Deployments must configure HTTPS, HSTS, reverse-proxy trust, cookie domain/scope, production `php.ini`, secret management, logging retention, and error display policies.
* `Info::phpinfo()` intentionally exposes PHP configuration when called; applications should not expose it in production routes.