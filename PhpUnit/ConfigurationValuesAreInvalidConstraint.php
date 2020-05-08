<?php

namespace Matthias\SymfonyConfigTest\PhpUnit;

use PHPUnit\Framework\Constraint\ExceptionMessage;
use PHPUnit\Framework\Constraint\ExceptionMessageRegularExpression;
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
            return new ExceptionMessageRegularExpression($this->expectedMessage);
        }

        return new ExceptionMessage($this->expectedMessage);
    }
}
