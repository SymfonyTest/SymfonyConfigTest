<?php

namespace Matthias\SymfonyConfigTest\Tests\PhpUnit;

use Matthias\SymfonyConfigTest\PhpUnit\ProcessedConfigurationEqualsConstraint;
use Matthias\SymfonyConfigTest\Tests\PhpUnit\Fixtures\AlwaysValidConfiguration;
use Matthias\SymfonyConfigTest\Tests\PhpUnit\Fixtures\ConfigurationWithRequiredValue;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ProcessedConfigurationEqualsConstraintTest extends TestCase
{
    #[Test]
    public function if_configuration_is_invalid_it_does_not_match()
    {
        $constraint = new ProcessedConfigurationEqualsConstraint(
            new AlwaysValidConfiguration(),
            []
        );

        $this->assertFalse($constraint->evaluate(['non-existing-key' => []], '', true));
    }

    #[Test]
    public function if_processed_configuration_equals_the_expected_values_it_matches()
    {
        $value = 'some value';

        $constraint = new ProcessedConfigurationEqualsConstraint(
            new ConfigurationWithRequiredValue(),
            [['required_value' => $value]]
        );

        $this->assertTrue($constraint->evaluate(['required_value'=> $value], '', true));
    }

    #[Test]
    public function to_string_is_not_implemented()
    {
        $constraint = new ProcessedConfigurationEqualsConstraint(
            new AlwaysValidConfiguration(),
            []
        );

        $this->assertEquals('', $constraint->toString());
    }
}
