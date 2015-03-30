<?php

namespace Matthias\SymfonyConfigTest\PhpUnit;

/**
 * Add this trait to your Test Case to add the ability of testing your configuration
 * which should implement Symfony\Component\Config\Definition\ConfigurationInterface
 */
trait ConfigurationTestCaseTrait
{
    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    abstract protected function getConfiguration();

    /**
     * Assert that the given configuration values are invalid.
     *
     * Optionally provide (part of) the exception message that you expect to receive.
     *
     * When running PHPUnit >=4.3.0, you need to set useRegExp to true if you'd like
     * to match the exception message using a regular expression.
     *
     * @param array $configurationValues
     * @param string|null $expectedMessage
     * @param bool $useRegExp
     */
    protected function assertConfigurationIsInvalid(array $configurationValues, $expectedMessage = null, $useRegExp = false)
    {
        \PHPUnit_Framework_TestCase::assertThat(
            $configurationValues,
            new ConfigurationValuesAreInvalidConstraint(
                $this->getConfiguration(),
                $expectedMessage,
                $useRegExp
            )
        );
    }

    /**
     * Assert that the given configuration values are valid.
     *
     * @param array $configurationValues
     */
    protected function assertConfigurationIsValid(array $configurationValues)
    {
        \PHPUnit_Framework_TestCase::assertThat(
            $configurationValues,
            new ConfigurationValuesAreValidConstraint(
                $this->getConfiguration()
            )
        );
    }

    /**
     * Assert that the given configuration values, when processed, will equal to the given array
     *
     * @param array $configurationValues
     * @param array $expectedProcessedConfiguration
     */
    protected function assertProcessedConfigurationEquals(
        array $configurationValues,
        array $expectedProcessedConfiguration
    ) {
        \PHPUnit_Framework_TestCase::assertThat(
            $expectedProcessedConfiguration,
            new ProcessedConfigurationEqualsConstraint(
                $this->getConfiguration(),
                $configurationValues
            )
        );
    }
}
