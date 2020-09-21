<?php

namespace Matthias\SymfonyConfigTest\Tests\PhpUnit;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Matthias\SymfonyConfigTest\Tests\PhpUnit\Fixtures\ConfigurationWithMultipleArrayKeys;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class PartialConfigurationIntegrationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration()
    {
        return new ConfigurationWithMultipleArrayKeys();
    }

    /**
     * @test
     */
    public function it_can_assert_that_a_configuration_is_invalid()
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                [], // no configuration values
            ],
            'array_node_1',
            'array_node_1'
        );
    }

    /**
     * @test
     */
    public function it_fails_when_a_configuration_is_valid_when_it_should_have_been_invalid()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('invalid');

        $this->assertPartialConfigurationIsInvalid(
            [
                [
                    'array_node_1' => [
                        'required_value_1' => 'some value',
                    ],
                ],
            ],
            'array_node_1'
        );
    }

    /**
     * @test
     */
    public function it_can_assert_that_a_configuration_is_valid()
    {
        $this->assertConfigurationIsValid(
            [
                [
                    'array_node_1' => [
                        'required_value_1' => 'some value',
                    ],
                ],
            ],
            'array_node_1'
        );
    }

    /**
     * @test
     */
    public function it_fails_when_a_configuration_is_invalid_when_it_should_have_been_valid()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('valid');

        $this->assertConfigurationIsValid(
            [
                [],
            ],
            'array_node_1'
        );
    }

    /**
     * @test
     */
    public function it_can_assert_that_a_processed_configuration_matches_the_expected_array_of_values()
    {
        $value = 'some value';

        $this->assertProcessedConfigurationEquals(
            [
                [],
                [
                    'array_node_1' => [
                        'required_value_1' => $value,
                    ],
                ],
            ],
            [
                'array_node_1' => [

                    'required_value_1' => $value,
                ],
            ],
            'array_node_1'
        );
    }

    /**
     * @test
     */
    public function it_fails_when_a_processed_configuration_does_not_match_the_expected_array_of_values()
    {
        $value = 'some value';

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('equal');
        $this->assertProcessedConfigurationEquals(
            [
                [
                    'array_node_1' => [
                        'required_value_1' => $value,
                    ],
                ],
            ],
            [
                'invalid_key' => 'invalid_value',
            ],
            'array_node_1'
        );
    }

    /**
     * @test
     */
    public function it_throws_a_comparison_failed_exception_with_the_values_in_the_right_order()
    {
        $value = 'some value';

        $configurationValues = [
            [
                'array_node_1' => [
                    'required_value_1' => $value,
                ],
            ],
        ];

        $expectedProcessedConfigurationValues = [
            'invalid_key' => 'invalid_value',
        ];

        try {
            $this->assertProcessedConfigurationEquals(
                $configurationValues,
                $expectedProcessedConfigurationValues,
                'array_node_1'
            );
        } catch (ExpectationFailedException $exception) {
            $this->assertSame(
                $expectedProcessedConfigurationValues,
                $exception->getComparisonFailure()->getExpected()
            );
        }
    }
}
