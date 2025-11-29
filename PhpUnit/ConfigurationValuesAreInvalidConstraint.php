<?php

namespace Matthias\SymfonyConfigTest\PhpUnit;

use PHPUnit\Framework\Constraint\ExceptionMessageIsOrContains;
use PHPUnit\Framework\Constraint\ExceptionMessageMatchesRegularExpression;
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

        if ($this->useRegExp) {
            return (new ExceptionMessageMatchesRegularExpression($this->expectedMessage))
                ->evaluate($exception, $description, $returnResult);
        }

        return (new ExceptionMessageIsOrContains($this->expectedMessage))
            ->evaluate($exception, $description, $returnResult);
    }
}
