<?php

namespace Matthias\SymfonyConfigTest\Tests\PhpUnit;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationValuesAreInvalidConstraint;
use Matthias\SymfonyConfigTest\Tests\PhpUnit\Fixtures\AlwaysValidConfiguration;
use Matthias\SymfonyConfigTest\Tests\PhpUnit\Fixtures\ConfigurationWithRequiredValue;
use PHPUnit\Framework\TestCase;

class ConfigurationValuesAreInvalidConstraintTest extends TestCase
{
    /**
     * @test
     */
    public function if_configuration_values_is_no_array_it_fails()
    {
        $constraint = new ConfigurationValuesAreInvalidConstraint(new AlwaysValidConfiguration());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('array');

        $constraint->evaluate('not an array');
    }

    /**
     * @test
     */
    public function if_configuration_values_is_no_array_of_arrays_it_fails()
    {
        $constraint = new ConfigurationValuesAreInvalidConstraint(new AlwaysValidConfiguration());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('array');

        $constraint->evaluate(['not an array']);
    }

    /**
     * @test
     */
    public function if_configuration_values_are_valid_it_does_not_match()
    {
        $constraint = new ConfigurationValuesAreInvalidConstraint(new AlwaysValidConfiguration());

        $this->assertFalse($constraint->evaluate([[]], '', true));
    }

    /**
     * @test
     */
    public function if_configuration_values_are_invalid_it_matches()
    {
        $constraint = new ConfigurationValuesAreInvalidConstraint(new ConfigurationWithRequiredValue());

        $this->assertTrue($constraint->evaluate([[]], '', true));
    }

    /**
     * @test
     */
    public function if_configuration_values_are_invalid_it_does_not_match_when_exception_message_is_not_right()
    {
        $constraint = new ConfigurationValuesAreInvalidConstraint(
            new ConfigurationWithRequiredValue(),
            'expected message which will not be part of the actual message'
        );

        $this->assertFalse($constraint->evaluate([[]], '', true));
    }

    /**
     * @test
     */
    public function if_configuration_values_are_invalid_it_matches_when_exception_message_is_right()
    {
        $constraint = new ConfigurationValuesAreInvalidConstraint(
            new ConfigurationWithRequiredValue(),
            'required_value'
        );

        $this->assertTrue($constraint->evaluate([[]], '', true));
    }

    /**
     * @test
     */
    public function if_configuration_values_are_invalid_it_matches_when_exception_message_is_right_according_to_regexp()
    {
        $constraint = new ConfigurationValuesAreInvalidConstraint(
            new ConfigurationWithRequiredValue(),
            '/required[_]{1}value/',
            true // use regular expressions
        );

        $this->assertTrue($constraint->evaluate([[]], '', true));
    }

    /**
     * @test
     */
    public function to_string_returns_a_message()
    {
        $constraint = new ConfigurationValuesAreInvalidConstraint(
            new AlwaysValidConfiguration()
        );

        $this->assertSame('is invalid for the given configuration', $constraint->toString());
    }

    /**
     * @test
     */
    public function to_string_also_mentions_the_expected_exception_message()
    {
        $expectedMessage = 'the expected message';
        $constraint = new ConfigurationValuesAreInvalidConstraint(
            new AlwaysValidConfiguration(),
            $expectedMessage
        );

        $expectedResult = 'is invalid for the given configuration (expected exception message: '.$expectedMessage.')';

        $this->assertSame($expectedResult, $constraint->toString());
    }
}
