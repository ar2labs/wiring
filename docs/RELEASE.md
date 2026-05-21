# Release Process

This project follows Semantic Versioning for public APIs under `src/`, documented extension points, and Composer package requirements.

## Versioning Policy

* Patch releases fix bugs, documentation, tests, CI, or security hardening without intentionally changing public behavior.
* Minor releases add backward-compatible APIs, optional integrations, documentation, or safer defaults with low migration risk.
* Major releases may remove deprecated APIs, narrow public contracts, require newer PHP or dependency versions, or change lifecycle behavior that applications may observe.

Public extension points include interfaces in `Wiring\Interfaces`, controller base classes, middleware classes, strategy classes, aware traits, helper classes, and HTTP exception classes.

## Release Checklist

Run these checks before tagging a release:

```bash
composer validate --strict
composer install --no-interaction --no-progress --prefer-dist
composer audit --locked --abandoned=fail
composer check-platform-reqs
vendor/bin/php-cs-fixer fix --config=php_cs.dist --dry-run --diff --no-interaction
vendor/bin/phpstan analyse --configuration phpstan.neon --no-progress --ansi
vendor/bin/phpunit --configuration phpunit.xml.dist --colors=always
vendor/bin/phpunit --configuration phpunit.xml.dist --coverage-clover build/logs/clover.xml --colors=always
```

Before tagging, also verify the GitHub Actions matrix for locked dependencies, lowest supported dependencies, Ubuntu, and Windows.

## Upgrade Notes

Each release that changes behavior should update `CHANGELOG.md` and, when needed, include a migration section with:

* Changed public methods or signatures.
* Changed middleware lifecycle behavior.
* Changed default security behavior.
* New exceptions thrown for invalid input or invalid service configuration.
* Required changes in starter projects or consuming applications.

## Deprecation Policy

When possible, deprecate public APIs for one minor release before removing them in a major release. Deprecation notes should name the replacement API and include a small migration example.

Security fixes may change behavior without a full deprecation window when preserving the old behavior would expose consumers to avoidable risk.

## Tagging Flow

1. Move relevant `CHANGELOG.md` entries from `Unreleased` to the target version and date.
2. Run the release checklist locally.
3. Confirm CI passes on the release branch.
4. Create an annotated Git tag for the version.
5. Publish release notes from the changelog entry.