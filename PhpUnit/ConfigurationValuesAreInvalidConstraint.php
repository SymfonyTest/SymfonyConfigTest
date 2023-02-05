<?php

namespace Matthias\SymfonyConfigTest\PhpUnit;

use PHPUnit\Framework\Constraint\ExceptionMessage;
use PHPUnit\Framework\Constraint\ExceptionMessageIsOrContains;
use PHPUnit\Framework\Constraint\ExceptionMessageMatchesRegularExpression;
use PHPUnit\Framework\Constraint\ExceptionMessageRegularExpression;
use PHPUnit\Framework\Constraint\MessageIsOrContains;
use PHPUnit\Framework\Constraint\MessageMatchesRegularExpression;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class ConfigurationValuesAreInvalidConstraint extends AbstractConfigurationConstraint
{
    private $expectedMessage;
    private $useRegExp;

    public function __construct(
        ConfigurationInterface $configuration,
        $expectedMessage = null,
        $useRegExp = false,
        $breadcrumbPath = null
    ) {
        parent::__construct($configuration, $breadcrumbPath);

        $this->expectedMessage = $expectedMessage;
        $this->useRegExp = $useRegExp;
    }

    public function evaluate($other, $description = '', $returnResult = false): ?bool
    {
        $this->validateConfigurationValuesArray($other);

        try {
            $this->processConfiguration($other);
        } catch (InvalidConfigurationException $exception) {
            return $this->evaluateException($exception, $description, $returnResult);
        }

        if ($returnResult) {
            return false;
        }

        $this->fail($other, $description);

        return null;
    }

    public function toString(): string
    {
        $toString = 'is invalid for the given configuration';

        if ($this->expectedMessage !== null) {
            $toString .= ' (expected exception message: '.$this->expectedMessage.')';
        }

        return $toString;
    }

    private function evaluateException(\Exception $exception, $description, $returnResult)
    {
        if ($this->expectedMessage === null) {
            return true;
        }

        return $this->createPhpUnitConstraint()
            ->evaluate($exception, $description, $returnResult);
    }

    private function createPhpUnitConstraint()
    {
        if ($this->useRegExp) {
            // Available since PHPUnit 10.0.15
            if (class_exists(ExceptionMessageMatchesRegularExpression::class)) {
                return new ExceptionMessageMatchesRegularExpression($this->expectedMessage);
            }

            // Available between PHPUnit 10.0.0 and 10.0.14 (inclusive)
            if (class_exists(MessageMatchesRegularExpression::class)) {
                return new MessageMatchesRegularExpression('exception', $this->expectedMessage);
            }

            // Available in PHPUnit 9.6
            return new ExceptionMessageRegularExpression($this->expectedMessage);
        }

        // Available since PHPUnit 10.0.15
        if (class_exists(ExceptionMessageIsOrContains::class)) {
            return new ExceptionMessageIsOrContains($this->expectedMessage);
        }

        // Available between PHPUnit 10.0.0 and 10.0.14 (inclusive)
        if (class_exists(MessageIsOrContains::class)) {
            return new MessageIsOrContains('exception', $this->expectedMessage);
        }

        // Available in PHPUnit 9.6
        return new ExceptionMessage($this->expectedMessage);
    }
}
