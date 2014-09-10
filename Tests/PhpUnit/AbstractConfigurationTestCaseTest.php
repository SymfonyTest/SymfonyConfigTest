<?php

namespace Matthias\SymfonyConfigTest\Tests;

use Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase;
use Matthias\SymfonyConfigTest\Tests\PhpUnit\Fixtures\ConfigurationWithRequiredValue;

class AbstractConfigurationTestCaseTest extends AbstractConfigurationTestCase
{
    protected function getConfiguration()
    {
        return new ConfigurationWithRequiredValue();
    }

    /**
     * @test
     */
    public function it_can_assert_that_a_configuration_is_invalid()
    {
        $this->assertConfigurationIsInvalid(
            array(
                array() // no configuration values
            ),
            'required_value'
        );
    }

    /**
     * @test
     */
    public function it_fails_when_a_configuration_is_valid_when_it_should_have_been_invalid()
    {
        $this->setExpectedException('\PHPUnit_Framework_ExpectationFailedException', 'invalid');

        $this->assertConfigurationIsInvalid(
            array(
                array('required_value' => 'some value')
            )
        );
    }

    /**
     * @test
     */
    public function it_can_assert_that_a_configuration_is_valid()
    {
        $this->assertConfigurationIsValid(
            array(
                array('required_value' => 'some value')
            )
        );
    }

    /**
     * @test
     */
    public function it_fails_when_a_configuration_is_invalid_when_it_should_have_been_valid()
    {
        $this->setExpectedException('\PHPUnit_Framework_ExpectationFailedException', 'valid');

        $this->assertConfigurationIsValid(
            array(
                array()
            )
        );
    }

    /**
     * @test
     */
    public function it_can_assert_that_a_processed_configuration_matches_the_expected_array_of_values()
    {
        $value = 'some value';

        $this->assertProcessedConfigurationEquals(
            array(
                array(),
                array('required_value' => $value)
            ),
            array(
                'required_value' => $value
            )
        );
    }

    /**
     * @test
     */
    public function it_fails_when_a_processed_configuration_does_not_match_the_expected_array_of_values()
    {
        $value = 'some value';

        $this->setExpectedException('\PHPUnit_Framework_ExpectationFailedException', 'equal');
        $this->assertProcessedConfigurationEquals(
            array(
                array('required_value' => $value)
            ),
            array(
                'invalid_key' => 'invalid_value'
            )
        );
    }

    /**
     * @test
     */
    public function it_throws_a_comparison_failed_exception_with_the_values_in_the_right_order()
    {
        $value = 'some value';

        //$this->setExpectedException('\PHPUnit_Framework_ExpectationFailedException', 'equal');
        $configurationValues = array(
            array('required_value' => $value)
        );

        $expectedProcessedConfigurationValues = array(
            'invalid_key' => 'invalid_value'
        );

        try {
            $this->assertProcessedConfigurationEquals(
                $configurationValues,
                $expectedProcessedConfigurationValues
            );
        } catch (\PHPUnit_Framework_ExpectationFailedException $exception) {
            $this->assertSame(
                $expectedProcessedConfigurationValues,
                $exception->getComparisonFailure()->getExpected()
            );
        }
    }
}
