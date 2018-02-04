<?php

namespace Matthias\SymfonyConfigTest\PhpUnit;

use PHPUnit\Framework\TestCase;

/**
 * Add this trait to your Test Case to add the ability of testing your configuration
 * which should implement Symfony\Component\Config\Definition\ConfigurationInterface.
 */
trait ConfigurationTestCaseTrait
{
    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case.
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    abstract protected function getConfiguration();

    /**
     * Assert that the given configuration values are invalid.
     *
     * Optionally provide (part of) the exception message that you expect to receive.
     *
     * You need to set useRegExp to true if you'd like
     * to match the exception message using a regular expression.
     *
     * @param array       $configurationValues
     * @param string|null $expectedMessage
     * @param bool        $useRegExp
     */
    protected function assertConfigurationIsInvalid(array $configurationValues, $expectedMessage = null, $useRegExp = false)
    {
        TestCase::assertThat(
            $configurationValues,
            new ConfigurationValuesAreInvalidConstraint(
                $this->getConfiguration(),
                $expectedMessage,
                $useRegExp
            )
        );
    }

    /**
     * Assert that the given configuration values are invalid.
     *
     * Optionally provide (part of) the exception message that you expect to receive.
     *
     * You need to set useRegExp to true if you'd like
     * to match the exception message using a regular expression.
     *
     * @param array       $configurationValues
     * @param string      $breadcrumbPath      The path that should be validated, e.g. "doctrine.orm"
     * @param string|null $expectedMessage
     * @param bool        $useRegExp
     */
    protected function assertPartialConfigurationIsInvalid(
        array $configurationValues,
        $breadcrumbPath,
        $expectedMessage = null,
        $useRegExp = false
    ) {
        TestCase::assertThat(
            $configurationValues,
            new ConfigurationValuesAreInvalidConstraint(
                $this->getConfiguration(),
                $expectedMessage,
                $useRegExp,
                $breadcrumbPath
            )
        );
    }

    /**
     * Assert that the given configuration values are valid.
     *
     * Optionally provide the part of the configuration that you want to test, e.g. "doctrine.orm"
     *
     * @param array       $configurationValues
     * @param string|null $breadcrumbPath
     */
    protected function assertConfigurationIsValid(array $configurationValues, $breadcrumbPath = null)
    {
        TestCase::assertThat(
            $configurationValues,
            new ConfigurationValuesAreValidConstraint(
                $this->getConfiguration(),
                $breadcrumbPath
            )
        );
    }

    /**
     * Assert that the given configuration values, when processed, will equal to the given array.
     *
     * Optionally provide the part of the configuration that you want to test, e.g. "doctrine.orm"
     *
     * @param array       $configurationValues
     * @param array       $expectedProcessedConfiguration
     * @param string|null $breadcrumbPath
     */
    protected function assertProcessedConfigurationEquals(
        array $configurationValues,
        array $expectedProcessedConfiguration,
        $breadcrumbPath = null
    ) {
        TestCase::assertThat(
            $expectedProcessedConfiguration,
            new ProcessedConfigurationEqualsConstraint(
                $this->getConfiguration(),
                $configurationValues,
                $breadcrumbPath
            )
        );
    }
}
