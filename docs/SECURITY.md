# Security Policy

## Supported Version

The active development line supports PHP 8.5.

## Reporting A Vulnerability

Please report suspected vulnerabilities privately to the maintainers before publishing details. Do not include production secrets, tokens, passwords, private keys, or sensitive customer data in reports or proof-of-concept material.

## Framework Security Scope

Wiring provides framework contracts, request/middleware/controller flow, helpers, strategies, and exception handling. Applications using Wiring remain responsible for their own authentication, authorization, CSRF enforcement, template escaping policy, database query safety, file upload controls, SSRF protections, secret management, deployment configuration, and operational monitoring.

Current core hardening includes relative-only redirects, network-disabled XML parsing, generic production error messages, logger redaction for common secret keys, JavaScript-safe console helper encoding, CR/LF header rejection in the emitter, and cookies with `HttpOnly` plus `SameSite=Lax` defaults.

Security claims should be based on performed checks, tests, scans, and review evidence. This project does not claim to be impossible to exploit.