<?php

namespace Matthias\SymfonyConfigTest\Tests;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationValuesAreValidConstraint;
use Matthias\SymfonyConfigTest\Tests\PhpUnit\Fixtures\AlwaysValidConfiguration;
use Matthias\SymfonyConfigTest\Tests\PhpUnit\Fixtures\ConfigurationWithRequiredValue;

class ConfigurationValuesAreValidConstraintTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function if_configuration_values_is_no_array_it_fails()
    {
        $constraint = new ConfigurationValuesAreValidConstraint(new AlwaysValidConfiguration());

        $this->setExpectedException('\InvalidArgumentException', 'array');

        $constraint->evaluate('not an array');
    }

    /**
     * @test
     */
    public function if_configuration_values_is_no_array_of_arrays_it_fails()
    {
        $constraint = new ConfigurationValuesAreValidConstraint(new AlwaysValidConfiguration());

        $this->setExpectedException('\InvalidArgumentException', 'array');

        $constraint->evaluate(array('not an array'));
    }

    /**
     * @test
     */
    public function if_configuration_values_are_valid_it_matches()
    {
        $constraint = new ConfigurationValuesAreValidConstraint(new AlwaysValidConfiguration());

        $this->assertTrue($constraint->evaluate(array(array()), '', true));
    }

    /**
     * @test
     */
    public function if_configuration_values_are_invalid_it_does_not_match()
    {
        $constraint = new ConfigurationValuesAreValidConstraint(new ConfigurationWithRequiredValue());

        $this->assertFalse($constraint->evaluate(array(array()), '', true));
    }

    /**
     * @test
     */
    public function to_string_returns_a_message()
    {
        $constraint = new ConfigurationValuesAreValidConstraint(new AlwaysValidConfiguration());

        $this->assertSame('is valid for the given configuration', $constraint->toString());
    }
}
