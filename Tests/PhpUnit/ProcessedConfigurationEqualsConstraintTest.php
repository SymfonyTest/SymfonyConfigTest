<?php

namespace Matthias\SymfonyConfigTest\Tests;

use Matthias\SymfonyConfigTest\PhpUnit\ProcessedConfigurationEqualsConstraint;
use Matthias\SymfonyConfigTest\Tests\PhpUnit\Fixtures\AlwaysValidConfiguration;
use Matthias\SymfonyConfigTest\Tests\PhpUnit\Fixtures\ConfigurationWithRequiredValue;

class ProcessedConfigurationEqualsConstraintTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function if_configuration_is_invalid_it_does_not_match()
    {
        $constraint = new ProcessedConfigurationEqualsConstraint(
            new AlwaysValidConfiguration(),
            array()
        );

        $this->assertFalse($constraint->evaluate(array('non-existing-key' => array()), '', true));
    }

    /**
     * @test
     */
    public function if_processed_configuration_equals_the_expected_values_it_matches()
    {
        $value = 'some value';

        $constraint = new ProcessedConfigurationEqualsConstraint(
            new ConfigurationWithRequiredValue(),
            array(array('required_value' => $value))
        );

        $this->assertTrue($constraint->evaluate(array('required_value'=> $value), '', true));
    }

    /**
     * @test
     */
    public function to_string_is_not_implemented()
    {
        $constraint = new ProcessedConfigurationEqualsConstraint(
            new AlwaysValidConfiguration(),
            array()
        );

        $this->assertNull($constraint->toString());
    }
}
