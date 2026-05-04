# Security Policy

## Supported Scope

This package is a Yii2 extension for [`mirrorps/taler-php`](https://github.com/mirrorps/taler-php).

Security-sensitive behavior is provided by the upstream SDK, including:

- request construction and transport behavior
- authentication and token handling
- endpoint validation
- secret redaction in SDK debug logging

This package is responsible for Yii2-specific integration concerns, including:

- mapping the `taler` application component properties into SDK options
- optional bootstrap registration so the `taler` component alias resolves from configuration
- wiring the SDK to PSR-3 logging (default: Yii’s log dispatcher via `YiiLogger`) and the `loggerCategory` setting
- component defaults such as `debugLoggingEnabled = false` (enable only when your Yii log targets and retention are appropriate for the environment)

## Reporting A Vulnerability

Please do not open public issues for suspected security vulnerabilities.

Use GitHub's private vulnerability reporting for this repository and include:

- a clear description of the issue
- affected package version(s)
- steps to reproduce
- impact assessment
- any suggested remediation

## Security Notes

- Keep `'debugLoggingEnabled' => false` in production unless you have verified that your full Yii logging path is appropriate for production use.
