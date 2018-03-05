# Changelog

## 4.0.0

- dropped support for PHPUnit < 7.0
- [BC Break] add return type hints to all `toString()` methods of the constraint classes for compatability with PHPUnit 7
- dropped support for PHP < 7.1
- Allow for completely empty configuration

## 3.1.0 

- Support for Symfony 4

## 3.0.1

- Only support Symfony 2.* and 3.* LTS versions
- Require PHP ^7.0
- Drop support for HHVM

## 3.0.0

- Only support PHPUnit 6
- Only support Symfony 2.* and 3.* LTS versions
- Require PHP ^7.0
- Drop support for HHVM
- Deprecated `Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase`, use `Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait` instead.

## 2.0.0

- Use the breadcrumb path to test one particular part of a prototype node.
- Only support Symfony 2.* LTS versions; only support PHPUnit 4.* and 5.*.

## 1.2.0

- Use a breadcrumb path to test only one particular part of the configuration node tree.

## 1.1.0

- Add a trait for test cases, mark the abstract base class "deprecated"

## v0.1.1

- Fixed issue #1: ``ProcessedConfigurationEqualsConstraint`` had expected and actual value mixed up
