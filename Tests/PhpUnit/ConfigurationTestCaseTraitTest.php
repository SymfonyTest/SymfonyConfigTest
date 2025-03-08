<?php

namespace Matthias\SymfonyConfigTest\Tests\PhpUnit;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Matthias\SymfonyConfigTest\Tests\PhpUnit\Fixtures\ConfigurationWithRequiredValue;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class ConfigurationTestCaseTraitTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration()
    {
        return new ConfigurationWithRequiredValue();
    }

    #[Test]
    public function it_can_assert_that_a_configuration_is_invalid()
    {
        $this->assertConfigurationIsInvalid(
            [
                [], // no configuration values
            ],
            'required_value'
        );
    }

    #[Test]
    public function it_fails_when_a_configuration_is_valid_when_it_should_have_been_invalid()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('invalid');

        $this->assertConfigurationIsInvalid(
            [
                ['required_value' => 'some value'],
            ]
        );
    }

    #[Test]
    public function it_can_assert_that_a_configuration_is_valid()
    {
        $this->assertConfigurationIsValid(
            [
                ['required_value' => 'some value'],
            ]
        );
    }

    #[Test]
    public function it_fails_when_a_configuration_is_invalid_when_it_should_have_been_valid()
    {
        $this->expectException(ExpectationFailedException::class);

        $this->expectExceptionMessageMatches('/^The child (config|node) "required_value" (at path|under) "root" must be configured/');

        $this->assertConfigurationIsValid(
            [
                [],
            ]
        );
    }

    #[Test]
    public function it_can_assert_that_a_processed_configuration_matches_the_expected_array_of_values()
    {
        $value = 'some value';

        $this->assertProcessedConfigurationEquals(
            [
                [],
                ['required_value' => $value],
            ],
            [
                'required_value' => $value,
            ]
        );
    }

    #[Test]
    public function it_fails_when_a_processed_configuration_does_not_match_the_expected_array_of_values()
    {
        $value = 'some value';

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('equal');

        $this->assertProcessedConfigurationEquals(
            [
                ['required_value' => $value],
            ],
            [
                'invalid_key' => 'invalid_value',
            ]
        );
    }

    #[Test]
    public function it_throws_a_comparison_failed_exception_with_the_values_in_the_right_order()
    {
        $value = 'some value';

        $configurationValues = [
            ['required_value' => $value],
        ];

        $expectedProcessedConfigurationValues = [
            'invalid_key' => 'invalid_value',
        ];

        try {
            $this->assertProcessedConfigurationEquals(
                $configurationValues,
                $expectedProcessedConfigurationValues
            );
        } catch (ExpectationFailedException $exception) {
            $this->assertSame(
                $expectedProcessedConfigurationValues,
                $exception->getComparisonFailure()->getExpected()
            );
        }
    }
}
