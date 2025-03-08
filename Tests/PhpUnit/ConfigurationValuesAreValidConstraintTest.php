<?php

namespace Matthias\SymfonyConfigTest\Tests\PhpUnit;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationValuesAreValidConstraint;
use Matthias\SymfonyConfigTest\Tests\PhpUnit\Fixtures\AlwaysValidConfiguration;
use Matthias\SymfonyConfigTest\Tests\PhpUnit\Fixtures\ConfigurationWithRequiredValue;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ConfigurationValuesAreValidConstraintTest extends TestCase
{
    #[Test]
    public function if_configuration_values_is_no_array_it_fails()
    {
        $constraint = new ConfigurationValuesAreValidConstraint(new AlwaysValidConfiguration());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('array');

        $constraint->evaluate('not an array');
    }

    #[Test]
    public function if_configuration_values_is_no_array_of_arrays_it_fails()
    {
        $constraint = new ConfigurationValuesAreValidConstraint(new AlwaysValidConfiguration());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('array');

        $constraint->evaluate(['not an array']);
    }

    #[Test]
    public function if_configuration_values_are_valid_it_matches()
    {
        $constraint = new ConfigurationValuesAreValidConstraint(new AlwaysValidConfiguration());

        $this->assertTrue($constraint->evaluate([[]], '', true));
    }

    #[Test]
    public function if_configuration_values_are_invalid_it_does_not_match()
    {
        $constraint = new ConfigurationValuesAreValidConstraint(new ConfigurationWithRequiredValue());

        $this->assertFalse($constraint->evaluate([[]], '', true));
    }

    #[Test]
    public function to_string_returns_a_message()
    {
        $constraint = new ConfigurationValuesAreValidConstraint(new AlwaysValidConfiguration());

        $this->assertSame('is valid for the given configuration', $constraint->toString());
    }
}
