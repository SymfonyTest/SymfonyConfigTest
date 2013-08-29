<?php

namespace Matthias\SymfonyConfigTest\PhpUnit;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class ConfigurationValuesAreInvalidConstraint extends AbstractConfigurationConstraint
{
    private $expectedMessage;

    public function __construct(ConfigurationInterface $configuration, $expectedMessage = null)
    {
        parent::__construct($configuration);

        $this->expectedMessage = $expectedMessage;
    }

    public function evaluate($other, $description = '', $returnResult = false)
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
    }

    public function toString()
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

        // reuse the exception message constraint from PHPUnit itself
        $constraint = new \PHPUnit_Framework_Constraint_ExceptionMessage($this->expectedMessage);

        return $constraint->evaluate($exception, $description, $returnResult);
    }
}
